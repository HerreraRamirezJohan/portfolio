<?php

namespace Tests\Feature;

use App\Models\CostFactor;
use App\Models\Faq;
use App\Models\ProcessStep;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\WorkExperience;
use Database\Seeders\CvSeeder;
use Database\Seeders\FreelanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseeding_removes_skills_that_left_the_cv(): void
    {
        $this->seed(CvSeeder::class);

        $group = SkillGroup::orderBy('sort_order')->firstOrFail();
        $group->skills()->create(['name' => 'Perl', 'sort_order' => 99]);

        $this->seed(CvSeeder::class);

        $this->assertFalse($group->skills()->where('name', 'Perl')->exists());
        $this->assertTrue($group->skills()->where('name', 'PHP')->exists());
    }

    public function test_the_seeded_cv_is_the_software_engineer_one_with_rap_detail(): void
    {
        $this->seed(CvSeeder::class);

        $mavi = WorkExperience::where('company', 'MAVI de Occidente')->firstOrFail();
        $this->assertSame('ABAP Developer', $mavi->getTranslation('role', 'en'));
        $this->assertSame('Desarrollador ABAP', $mavi->getTranslation('role', 'es'));
        $this->assertSame('RAP business objects', $mavi->getTranslation('bullets', 'en')[0]['label']);
        $this->assertSame('Objetos de negocio RAP', $mavi->getTranslation('bullets', 'es')[0]['label']);

        $profile = Profile::firstOrFail();
        $this->assertStringContainsString('My edge is requirements', $profile->getTranslation('summary', 'en'));
        $this->assertStringContainsString('Mi ventaja son los requerimientos', $profile->getTranslation('summary', 'es'));
        // The availability line belongs to one application, not the standing profile.
        $this->assertStringNotContainsString('Eastern Time', $profile->getTranslation('summary', 'en'));
        $this->assertStringNotContainsString('Este de EE. UU.', $profile->getTranslation('summary', 'es'));
    }

    public function test_reseeding_does_not_duplicate_anything(): void
    {
        $this->seed(CvSeeder::class);
        $groups = SkillGroup::count();
        $skills = Skill::count();
        $services = Service::count();
        $steps = ProcessStep::count();
        $faqs = Faq::count();
        $factors = CostFactor::count();

        $this->seed(CvSeeder::class);

        $this->assertSame($groups, SkillGroup::count());
        $this->assertSame($skills, Skill::count());
        $this->assertSame($services, Service::count());
        $this->assertSame($steps, ProcessStep::count());
        $this->assertSame($faqs, Faq::count());
        $this->assertSame($factors, CostFactor::count());
    }

    public function test_the_freelance_copy_is_seeded_in_both_languages(): void
    {
        $this->seed(CvSeeder::class);

        $this->assertSame(['programacion-web', 'desarrollo-sap'], Service::ordered()->pluck('slug')->all());
        $this->assertSame([15, 20], Service::ordered()->pluck('rate_usd')->all());

        $sap = Service::where('slug', 'desarrollo-sap')->firstOrFail();
        $this->assertSame('sap', $sap->path);
        $this->assertSame('SAP development (ABAP)', $sap->getTranslation('title', 'en'));
        $this->assertSame('Desarrollo SAP (ABAP)', $sap->getTranslation('title', 'es'));
        $this->assertContains('Documentation', $sap->getTranslation('deliverables', 'en'));

        $this->assertSame(
            ['UI/UX design', 'Mobile design', 'SEO/GEO analysis', 'Testing', 'Documentation', 'Integrations'],
            array_map(fn (CostFactor $factor) => $factor->getTranslation('title', 'en'), CostFactor::ordered()->get()->all()),
        );
    }

    public function test_reseeding_the_cv_leaves_the_freelance_copy_to_the_admin(): void
    {
        $this->seed(CvSeeder::class);

        Service::where('slug', 'programacion-web')->firstOrFail()->update(['rate_usd' => 40]);
        Service::where('slug', 'desarrollo-sap')->delete();
        Faq::query()->delete();

        $this->seed(CvSeeder::class);

        $this->assertSame(40, Service::where('slug', 'programacion-web')->firstOrFail()->rate_usd);
        $this->assertFalse(Service::where('slug', 'desarrollo-sap')->exists());
        $this->assertSame(0, Faq::count());
    }

    public function test_the_freelance_seeder_fills_only_what_is_empty(): void
    {
        $this->seed(CvSeeder::class);

        Faq::query()->delete();
        Service::where('slug', 'programacion-web')->firstOrFail()->update(['rate_usd' => 40]);

        $this->seed(FreelanceSeeder::class);
        $this->seed(FreelanceSeeder::class);

        $this->assertSame(5, Faq::count());
        $this->assertSame(2, Service::count());
        $this->assertSame(40, Service::where('slug', 'programacion-web')->firstOrFail()->rate_usd);
    }
}
