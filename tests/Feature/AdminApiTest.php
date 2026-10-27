<?php

namespace Tests\Feature;

use App\Models\CostFactor;
use App\Models\Education;
use App\Models\Faq;
use App\Models\Language;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
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

    public function test_an_education_can_be_created_updated_and_deleted(): void
    {
        $id = $this->postJson('/api/admin/educations', [
            'institution' => 'Platzi',
            'start_year' => 2024,
            'end_year' => 2025,
            'degree' => ['es' => 'Curso de Laravel', 'en' => 'Laravel course'],
            'notes' => ['es' => 'Notas', 'en' => ''],
        ])->assertCreated()->json('data.id');

        $education = Education::findOrFail($id);
        $this->assertSame(['es' => 'Notas'], $education->getTranslations('notes'));
        $this->assertSame(Education::max('sort_order'), $education->sort_order);

        $this->putJson("/api/admin/educations/{$id}", [
            'institution' => 'Platzi',
            'degree' => ['es' => 'Curso de Laravel', 'en' => 'Laravel course'],
            'notes' => ['es' => '', 'en' => 'Notes'],
        ])->assertOk()->assertJsonPath('data.notes', ['es' => '', 'en' => 'Notes']);

        $this->deleteJson("/api/admin/educations/{$id}")->assertNoContent();
        $this->assertModelMissing($education);
    }

    public function test_an_education_cannot_end_before_it_starts(): void
    {
        $this->postJson('/api/admin/educations', [
            'institution' => 'Platzi',
            'start_year' => 2025,
            'end_year' => 2024,
        ])->assertUnprocessable()->assertJsonValidationErrors('end_year');

        $this->postJson('/api/admin/educations', ['start_year' => 2024])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('institution');
    }

    public function test_a_language_can_be_created_updated_and_deleted(): void
    {
        $id = $this->postJson('/api/admin/languages', [
            'code' => 'fr',
            'name' => ['es' => 'Francés', 'en' => 'French'],
            'level_label' => ['es' => 'A1', 'en' => 'A1'],
        ])->assertCreated()->json('data.id');

        $language = Language::findOrFail($id);
        $this->assertSame(Language::max('sort_order'), $language->sort_order);

        $this->putJson("/api/admin/languages/{$id}", [
            'code' => 'fr',
            'name' => ['es' => 'Francés', 'en' => 'French'],
            'level_label' => ['es' => 'A2', 'en' => 'A2'],
        ])->assertOk()->assertJsonPath('data.level_label.en', 'A2');

        $this->deleteJson("/api/admin/languages/{$id}")->assertNoContent();
        $this->assertModelMissing($language);
    }

    public function test_a_language_needs_a_short_code(): void
    {
        $this->postJson('/api/admin/languages', ['name' => ['en' => 'French']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');

        $this->postJson('/api/admin/languages', ['code' => 'toolong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_a_skill_group_is_created_with_its_skills_and_deleted_with_them(): void
    {
        $id = $this->postJson('/api/admin/skill-groups', [
            'label' => ['es' => 'Pruebas', 'en' => 'Testing'],
            'skills' => ['PHPUnit', '', 'Playwright'],
        ])->assertCreated()
            ->assertJsonPath('data.skills', ['PHPUnit', 'Playwright'])
            ->json('data.id');

        $group = SkillGroup::findOrFail($id);
        $this->assertSame(['PHPUnit', 'Playwright'], $group->skills()->orderBy('sort_order')->pluck('name')->all());

        $this->deleteJson("/api/admin/skill-groups/{$id}")->assertNoContent();

        $this->assertModelMissing($group);
        $this->assertSame(0, Skill::where('skill_group_id', $id)->count());
    }

    public function test_blanking_a_translation_removes_it(): void
    {
        $this->putJson('/api/admin/profile', [
            'name' => 'Johan Herrera',
            'headline' => ['es' => 'Ingeniero', 'en' => 'Engineer'],
        ])->assertOk();

        $this->putJson('/api/admin/profile', [
            'name' => 'Johan Herrera',
            'headline' => ['es' => 'Ingeniero', 'en' => ''],
        ])->assertOk()->assertJsonPath('data.headline', ['es' => 'Ingeniero', 'en' => '']);

        $this->assertSame(
            ['es' => 'Ingeniero'],
            User::firstOrFail()->profile->getTranslations('headline'),
        );
    }

    public function test_the_last_record_cannot_move_down_and_other_collections_move(): void
    {
        $last = WorkExperience::orderBy('sort_order')->get()->last();
        $before = WorkExperience::orderBy('sort_order')->pluck('id')->all();

        $this->patchJson("/api/admin/experiences/{$last->id}/move", ['direction' => 'down'])
            ->assertOk()
            ->assertJsonPath('data.*.id', $before);

        $first = $this->postJson('/api/admin/languages', ['code' => 'fr', 'name' => ['en' => 'French']])
            ->json('data.id');

        $order = Language::orderBy('sort_order')->pluck('id')->all();
        $this->assertSame($first, end($order));

        $this->patchJson("/api/admin/languages/{$first}/move", ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('data.1.id', $first);
    }

    public function test_the_dashboard_counts_the_seeded_records(): void
    {
        $this->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.counts.experiences', WorkExperience::count())
            ->assertJsonPath('data.counts.educations', Education::count())
            ->assertJsonPath('data.counts.skill_groups', SkillGroup::count())
            ->assertJsonPath('data.counts.languages', Language::count())
            ->assertJsonPath('data.counts.projects', 0)
            ->assertJsonPath('data.counts.services', Service::count())
            ->assertJsonPath('data.counts.process_steps', ProcessStep::count())
            ->assertJsonPath('data.counts.faqs', Faq::count())
            ->assertJsonPath('data.counts.cost_factors', CostFactor::count());
    }

    public function test_a_service_can_be_created_updated_and_deleted(): void
    {
        $id = $this->postJson('/api/admin/services', [
            'slug' => 'audits',
            'path' => 'infra',
            'title' => ['es' => 'Auditorías', 'en' => ''],
            'rate_usd' => '25',
            'deliverables' => ['es' => [' Informe ', '', 'Plan de mejora'], 'en' => ['', '  ']],
            'is_published' => true,
        ])->assertCreated()->json('data.id');

        $service = Service::findOrFail($id);
        $this->assertSame('infra', $service->path);
        $this->assertSame(25, $service->rate_usd);
        // A blank locale is dropped, and so are blank lines in a list.
        $this->assertSame(['es' => 'Auditorías'], $service->getTranslations('title'));
        $this->assertSame(['es' => ['Informe', 'Plan de mejora']], $service->getTranslations('deliverables'));
        $this->assertSame(Service::max('sort_order'), $service->sort_order);

        $this->putJson("/api/admin/services/{$id}", [
            'slug' => 'audits',
            'path' => '',
            'rate_usd' => '',
            'title' => ['es' => 'Auditorías', 'en' => 'Audits'],
            'is_published' => false,
        ])->assertOk()
            ->assertJsonPath('data.title.en', 'Audits')
            ->assertJsonPath('data.path', '')
            // A blank rate means "no rate shown", not zero.
            ->assertJsonPath('data.rate_usd', null)
            ->assertJsonPath('data.deliverables', ['es' => [], 'en' => []]);

        $service->refresh();
        $this->assertNull($service->path);
        $this->assertFalse($service->is_published);

        $this->deleteJson("/api/admin/services/{$id}")->assertNoContent();
        $this->assertModelMissing($service);
    }

    public function test_a_service_needs_a_unique_slug_and_a_known_path(): void
    {
        $this->postJson('/api/admin/services', ['slug' => 'programacion-web', 'is_published' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slug');

        $this->postJson('/api/admin/services', ['slug' => 'audits', 'path' => 'mainframe'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('path');
    }

    public function test_the_hourly_rate_must_be_a_whole_non_negative_number(): void
    {
        foreach ([-1, 12.5, 'cheap', 100000] as $rate) {
            $this->postJson('/api/admin/services', ['slug' => 'audits', 'rate_usd' => $rate])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('rate_usd');
        }
    }

    public function test_services_move_up_and_down(): void
    {
        $ids = Service::ordered()->pluck('id');

        $this->patchJson("/api/admin/services/{$ids[1]}/move", ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('data.0.id', $ids[1])
            ->assertJsonPath('data.1.id', $ids[0]);
    }

    public function test_process_steps_and_faqs_can_be_created_updated_and_deleted(): void
    {
        $stepId = $this->postJson('/api/admin/process-steps', [
            'title' => ['es' => 'Kickoff', 'en' => 'Kickoff'],
            'body' => ['es' => '', 'en' => 'We start.'],
        ])->assertCreated()->json('data.id');

        $step = ProcessStep::findOrFail($stepId);
        $this->assertSame(['en' => 'We start.'], $step->getTranslations('body'));
        $this->assertSame(ProcessStep::max('sort_order'), $step->sort_order);

        $this->putJson("/api/admin/process-steps/{$stepId}", [
            'title' => ['es' => 'Inicio', 'en' => 'Kickoff'],
            'body' => ['es' => '', 'en' => ''],
        ])->assertOk()->assertJsonPath('data.title.es', 'Inicio')->assertJsonPath('data.body', ['es' => '', 'en' => '']);

        $this->deleteJson("/api/admin/process-steps/{$stepId}")->assertNoContent();
        $this->assertModelMissing($step);

        $faqId = $this->postJson('/api/admin/faqs', [
            'question' => ['es' => '¿Facturas?', 'en' => 'Do you invoice?'],
            'answer' => ['es' => 'Sí.', 'en' => 'Yes.'],
        ])->assertCreated()->json('data.id');

        $faq = Faq::findOrFail($faqId);
        $this->assertSame(Faq::max('sort_order'), $faq->sort_order);

        $this->patchJson("/api/admin/faqs/{$faqId}/move", ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('data.'.(Faq::count() - 2).'.id', $faqId);

        $this->deleteJson("/api/admin/faqs/{$faqId}")->assertNoContent();
        $this->assertModelMissing($faq);
    }

    public function test_cost_factors_can_be_created_updated_moved_and_deleted(): void
    {
        $id = $this->postJson('/api/admin/cost-factors', [
            'title' => ['es' => 'Seguridad', 'en' => 'Security'],
            'body' => ['es' => '', 'en' => 'Hardening before launch.'],
        ])->assertCreated()->json('data.id');

        $factor = CostFactor::findOrFail($id);
        $this->assertSame(['en' => 'Hardening before launch.'], $factor->getTranslations('body'));
        $this->assertSame(CostFactor::max('sort_order'), $factor->sort_order);

        $this->putJson("/api/admin/cost-factors/{$id}", [
            'title' => ['es' => 'Seguridad', 'en' => 'Security'],
            'body' => ['es' => '', 'en' => ''],
        ])->assertOk()->assertJsonPath('data.body', ['es' => '', 'en' => '']);

        $this->patchJson("/api/admin/cost-factors/{$id}/move", ['direction' => 'up'])
            ->assertOk()
            ->assertJsonPath('data.'.(CostFactor::count() - 2).'.id', $id);

        $this->deleteJson("/api/admin/cost-factors/{$id}")->assertNoContent();
        $this->assertModelMissing($factor);
    }

    public function test_unknown_records_are_404(): void
    {
        $this->putJson('/api/admin/projects/9999', ['slug' => 'nope'])->assertNotFound();
        $this->deleteJson('/api/admin/educations/9999')->assertNotFound();
        $this->patchJson('/api/admin/languages/9999/move', ['direction' => 'up'])->assertNotFound();
    }
}
