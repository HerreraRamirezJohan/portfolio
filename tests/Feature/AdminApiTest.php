<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SkillGroup;
use App\Models\User;
use App\Models\WorkExperience;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
        $this->actingAs(User::firstOrFail());
    }

    public function test_collections_return_every_locale_for_translatable_fields(): void
    {
        $this->getJson('/api/admin/experiences')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'company', 'role' => ['es', 'en'], 'bullets' => ['es', 'en']]]]);
    }

    public function test_a_project_can_be_created_updated_and_deleted(): void
    {
        $id = $this->postJson('/api/admin/projects', [
            'slug' => 'homelab',
            'title' => ['es' => 'Stack del homelab', 'en' => ''],
            'tech_stack' => [' Docker ', '', 'Nginx'],
            'is_published' => true,
        ])->assertCreated()->json('data.id');

        $project = Project::findOrFail($id);
        $this->assertSame(['Docker', 'Nginx'], $project->tech_stack);
        // A blank locale is dropped rather than stored as an empty string.
        $this->assertSame(['es' => 'Stack del homelab'], $project->getTranslations('title'));
        $this->assertSame(Project::max('sort_order'), $project->sort_order);

        $this->putJson("/api/admin/projects/{$id}", [
            'slug' => 'homelab',
            'title' => ['es' => 'Stack del homelab', 'en' => 'Homelab stack'],
            'is_published' => false,
        ])->assertOk()->assertJsonPath('data.title.en', 'Homelab stack');

        $this->assertFalse($project->fresh()->is_published);

        $this->deleteJson("/api/admin/projects/{$id}")->assertNoContent();
        $this->assertModelMissing($project);
    }

    public function test_project_slugs_must_be_unique(): void
    {
        $this->postJson('/api/admin/projects', ['slug' => 'one', 'is_published' => true])->assertCreated();

        $this->postJson('/api/admin/projects', ['slug' => 'one', 'is_published' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slug');
    }

    public function test_experience_bullets_are_cleaned_per_locale(): void
    {
        $job = WorkExperience::firstOrFail();

        $this->putJson("/api/admin/experiences/{$job->id}", [
            'company' => $job->company,
            'start_date' => '2024-01-01',
            'end_date' => '2023-01-01',
            'is_current' => true,
            'bullets' => [
                'es' => [['label' => '', 'body' => 'Algo'], ['label' => 'Vacío', 'body' => '']],
                'en' => [],
            ],
        ])->assertUnprocessable()->assertJsonValidationErrors('end_date');

        $this->putJson("/api/admin/experiences/{$job->id}", [
            'company' => $job->company,
            'start_date' => '2024-01-01',
            'end_date' => '2025-01-01',
            'is_current' => true,
            'bullets' => [
                'es' => [['label' => '', 'body' => 'Algo'], ['label' => 'Vacío', 'body' => '']],
                'en' => [],
            ],
        ])->assertOk();

        $job->refresh();
        $this->assertNull($job->end_date, 'A current role has no end date.');
        $this->assertSame(['es' => [['label' => null, 'body' => 'Algo']]], $job->getTranslations('bullets'));
    }

    public function test_records_move_up_and_down(): void
    {
        $ids = WorkExperience::orderBy('sort_order')->pluck('id');

        $this->patchJson("/api/admin/experiences/{$ids[1]}/move", ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('data.0.id', $ids[1])
            ->assertJsonPath('data.1.id', $ids[0]);

        // Already first: moving up again is a no-op.
        $this->patchJson("/api/admin/experiences/{$ids[1]}/move", ['direction' => 'up'])
            ->assertJsonPath('data.0.id', $ids[1]);

        $this->patchJson("/api/admin/experiences/{$ids[1]}/move", ['direction' => 'sideways'])
            ->assertUnprocessable();
    }

    public function test_saving_a_skill_group_replaces_its_skills(): void
    {
        $group = SkillGroup::firstOrFail();

        $this->putJson("/api/admin/skill-groups/{$group->id}", [
            'label' => ['es' => 'Backend', 'en' => 'Backend'],
            'skills' => ['PHP', '  ', 'Laravel'],
        ])->assertOk()->assertJsonPath('data.skills', ['PHP', 'Laravel']);

        $this->assertSame(['PHP', 'Laravel'], $group->skills()->pluck('name')->all());
    }

    public function test_the_profile_can_be_updated(): void
    {
        $this->putJson('/api/admin/profile', [
            'name' => 'Johan Herrera',
            'github_url' => 'not a url',
        ])->assertUnprocessable()->assertJsonValidationErrors('github_url');

        $this->putJson('/api/admin/profile', [
            'name' => 'Johan Herrera',
            'headline' => ['es' => 'Ingeniero', 'en' => 'Engineer'],
        ])->assertOk()->assertJsonPath('data.headline.en', 'Engineer');

        $this->assertSame('Johan Herrera', User::firstOrFail()->name);
    }
}
