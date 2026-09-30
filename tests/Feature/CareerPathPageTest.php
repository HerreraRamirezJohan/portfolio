<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerPathPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    public function test_every_path_resolves_in_every_locale(): void
    {
        $titles = [
            'es' => ['sap' => 'SAP y sistemas empresariales', 'web' => 'Desarrollo web', 'infra' => 'Servidores e infraestructura'],
            'en' => ['sap' => 'SAP & enterprise systems', 'web' => 'Web development', 'infra' => 'Servers & infrastructure'],
        ];

        foreach ($titles as $locale => $paths) {
            foreach ($paths as $path => $title) {
                $this->get("/{$locale}/{$path}")->assertOk()->assertSee(e($title), false);
                $this->getJson("/api/{$locale}/paths/{$path}")->assertOk()->assertJsonPath('data.path.title', $title);
            }
        }
    }

    public function test_an_unknown_path_is_not_routable(): void
    {
        $this->get('/es/devops')->assertNotFound();
        $this->getJson('/api/es/paths/devops')->assertNotFound();
    }

    public function test_a_path_only_lists_the_roles_it_names(): void
    {
        $companies = fn (string $path) => collect($this->getJson("/api/en/paths/{$path}")->json('data.roles'))->pluck('company');

        $this->assertEqualsCanonicalizing(['MAVI de Occidente', 'Global Asiática'], $companies('sap')->all());
        $this->assertContains('Operadora Macrobús', $companies('web'));
    }

    public function test_each_role_carries_the_blurb_for_that_path(): void
    {
        $roles = collect($this->getJson('/api/en/paths/sap')->json('data.roles'))->keyBy('company');

        $this->assertSame(
            'OData services, CDS views and Fiori apps; FSD review before every estimate.',
            $roles['MAVI de Occidente']['blurb'],
        );
    }

    public function test_the_shell_links_the_same_path_in_every_language(): void
    {
        $this->get('/es/web')->assertSee('hreflang="en" href="'.url('/en/web').'"', false);
    }

    public function test_the_next_path_wraps_around(): void
    {
        $this->getJson('/api/en/paths/infra')->assertJsonPath('data.next.key', 'sap');
    }

    public function test_stack_names_carry_their_icon_slug(): void
    {
        $stack = collect($this->getJson('/api/en/paths/web')->json('data.path.stack'))->keyBy('name');

        $this->assertSame('php', $stack['PHP']['icon']);
        $this->assertNull($stack['cron']['icon'] ?? null);
    }

    public function test_published_projects_appear_on_the_paths_their_stack_matches(): void
    {
        $user = User::firstOrFail();

        Project::create([
            'user_id' => $user->id,
            'slug' => 'homelab',
            'title' => ['en' => 'Homelab stack', 'es' => 'Stack del homelab'],
            'tech_stack' => ['docker', 'Nginx'],
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Project::create([
            'user_id' => $user->id,
            'slug' => 'draft',
            'title' => ['en' => 'Unfinished draft', 'es' => 'Borrador sin terminar'],
            'tech_stack' => ['Docker'],
            'is_published' => false,
            'sort_order' => 2,
        ]);

        $this->getJson('/api/en/paths/infra')->assertJsonPath('data.projects.*.title', ['Homelab stack']);
        $this->getJson('/api/en/paths/sap')->assertJsonPath('data.projects', []);
    }
}
