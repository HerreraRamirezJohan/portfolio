<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    public function test_root_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/'.config('app.locale'));
    }

    public function test_the_shell_renders_the_head_for_crawlers(): void
    {
        $this->get('/es/cv')
            ->assertOk()
            ->assertSee('<html lang="es"', false)
            ->assertSee('<title>Johan Osvaldo Herrera Ramírez — Ingeniero de Software · Backend, APIs e Integraciones Empresariales</title>', false)
            ->assertSee('<div id="app"', false);
    }

    public function test_the_shell_links_every_language_version(): void
    {
        $this->get('/es/cv')->assertSee('hreflang="en" href="'.url('/en/cv').'"', false);
    }

    public function test_an_unknown_locale_is_not_routable(): void
    {
        $this->get('/fr')->assertNotFound();
        $this->getJson('/api/fr/home')->assertNotFound();
        $this->getJson('/api/fr/cv')->assertNotFound();
    }

    public function test_the_spanish_api_returns_the_cv(): void
    {
        $response = $this->getJson('/api/es/cv')
            ->assertOk()
            ->assertJsonPath('data.person.name', 'Johan Osvaldo Herrera Ramírez')
            ->assertJsonPath('data.person.headline', 'Ingeniero de Software · Backend, APIs e Integraciones Empresariales')
            ->assertJsonPath('data.meta.title', 'Johan Osvaldo Herrera Ramírez — Ingeniero de Software · Backend, APIs e Integraciones Empresariales');

        $this->assertJsonMentions($response, 'MAVI de Occidente', 'Universidad de Guadalajara (CUCEI)', 'Ingeniería en Computación');
        $this->assertJsonDoesNotMention($response, 'DocChain');
    }

    public function test_the_english_api_returns_the_english_translations(): void
    {
        $response = $this->getJson('/api/en/cv')->assertOk();

        $this->assertJsonMentions(
            $response,
            'Software Engineer · Backend, APIs & Enterprise Integrations',
            'ABAP Developer',
            'Bachelor of Engineering in Computer Science',
        );
        $this->assertJsonDoesNotMention($response, 'Ingeniero de Software', 'Computer Engineer');
    }

    public function test_the_home_payload_lists_every_path_in_the_active_locale(): void
    {
        $this->getJson('/api/es/cv')
            ->assertJsonPath('data.paths.*.key', ['sap', 'web', 'infra'])
            ->assertJsonPath('data.paths.0.card_title', 'SAP y empresa');

        $this->getJson('/api/en/cv')->assertJsonPath('data.paths.0.card_title', 'SAP & enterprise');
    }

    public function test_each_role_names_the_paths_it_belongs_to(): void
    {
        $items = collect($this->getJson('/api/en/cv')->json('data.experience.items'))->keyBy('company');

        $this->assertSame(['sap'], $items['MAVI de Occidente']['paths']);
        $this->assertSame(['sap', 'web', 'infra'], $items['Global Asiática']['paths']);
    }

    public function test_ledger_rows_carry_month_stamps(): void
    {
        $items = collect($this->getJson('/api/en/cv')->json('data.experience.items'))->keyBy('company');

        $this->assertSame('2026-03', $items['MAVI de Occidente']['from']);
        $this->assertSame('2026-08', $items['MAVI de Occidente']['to']);
        $this->assertSame('2024-07', $items['Global Asiática']['from']);
    }

    public function test_each_path_is_a_pipeline_with_one_focus_node(): void
    {
        $pipelines = collect($this->getJson('/api/es/cv')->json('data.pipelines'))->keyBy('key');

        $this->assertSame(['sap', 'web', 'infra'], $pipelines->keys()->all());
        $this->assertSame('SAP y empresa', $pipelines['sap']['title']);

        foreach ($pipelines as $pipeline) {
            $this->assertCount(1, array_filter(array_column($pipeline['nodes'], 'focus')));
        }
    }

    public function test_the_home_payload_has_skill_groups_and_only_published_projects(): void
    {
        $user = User::firstOrFail();

        Project::create([
            'user_id' => $user->id,
            'slug' => 'erp',
            'title' => ['en' => 'Multi-company ERP', 'es' => 'ERP multiempresa'],
            'tech_stack' => ['PHP'],
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Project::create([
            'user_id' => $user->id,
            'slug' => 'draft',
            'title' => ['en' => 'Unfinished draft', 'es' => 'Borrador sin terminar'],
            'tech_stack' => ['PHP'],
            'is_published' => false,
            'sort_order' => 2,
        ]);

        $response = $this->getJson('/api/en/cv')->assertJsonPath('data.projects.*.title', ['Multi-company ERP']);

        $groups = $response->json('data.skills');
        $this->assertNotEmpty($groups);
        $this->assertContains('Laravel', $groups[0]['skills']);
    }

    public function test_the_sap_skills_are_grouped_apart_from_the_rest(): void
    {
        $groups = collect($this->getJson('/api/en/cv')->assertOk()->json('data.skills'))->keyBy('label');

        $this->assertSame(
            ['Languages & Frameworks', 'Front End', 'APIs & Integration', 'SAP S/4HANA', 'Databases', 'Infrastructure & Tooling', 'Practices'],
            $groups->keys()->all(),
        );
        $this->assertContains('RAP', $groups['SAP S/4HANA']['skills']);
        $this->assertContains('Fiori Elements', $groups['SAP S/4HANA']['skills']);

        // The Spanish site has the same groups.
        $this->assertCount(7, $this->getJson('/api/es/cv')->json('data.skills'));
    }
}
