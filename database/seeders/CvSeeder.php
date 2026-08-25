<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Language;
use App\Models\Project;
use App\Models\SkillGroup;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Seeds the portfolio from the real CV.
 *
 * Source of truth is database/data/cv.json, transcribed from the reportlab
 * generators in ~/user_poyoevil_server_config/cv (full-stack flavour, ES + EN).
 *
 * Idempotent: re-running updates the existing rows rather than duplicating them.
 */
class CvSeeder extends Seeder
{
    public function run(): void
    {
        $cv = $this->data();

        $user = User::updateOrCreate(
            ['email' => $cv['user']['email']],
            [
                'name' => $cv['user']['name'],
                'password' => $this->adminPassword(),
            ],
        );

        $this->seedProfile($user, $cv['profile']);
        $this->seedWorkExperiences($user, $cv['work_experiences']);
        $this->seedEducations($user, $cv['educations']);
        $this->seedSkillGroups($user, $cv['skill_groups']);
        $this->seedLanguages($user, $cv['languages']);
        $this->seedProjects($user, $cv['projects']);
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        $path = database_path('data/cv.json');

        if (! is_readable($path)) {
            throw new RuntimeException("CV seed data not found at {$path}.");
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function adminPassword(): string
    {
        $password = config('portfolio.admin_password');

        if (blank($password)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD is not set. Add it to your .env before seeding -- '.
                'the admin password is deliberately not hardcoded.'
            );
        }

        return $password;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function seedProfile(User $user, array $data): void
    {
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $data['phone'],
                'public_email' => $data['public_email'],
                'linkedin_url' => $data['linkedin_url'],
                'github_url' => $data['github_url'],
                'headline' => $data['headline'],
                'location' => $data['location'],
                'summary' => $data['summary'],
            ],
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function seedWorkExperiences(User $user, array $rows): void
    {
        foreach ($rows as $row) {
            WorkExperience::updateOrCreate(
                ['user_id' => $user->id, 'company' => $row['company']],
                [
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date'],
                    'is_current' => $row['is_current'],
                    'sort_order' => $row['sort_order'],
                    'role' => $row['role'],
                    'location' => $row['location'],
                    'bullets' => $row['bullets'],
                ],
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function seedEducations(User $user, array $rows): void
    {
        foreach ($rows as $row) {
            Education::updateOrCreate(
                ['user_id' => $user->id, 'institution' => $row['institution']],
                [
                    'start_year' => $row['start_year'],
                    'end_year' => $row['end_year'],
                    'sort_order' => $row['sort_order'],
                    'degree' => $row['degree'],
                    'location' => $row['location'],
                    'notes' => array_filter($row['notes'] ?? []),
                ],
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function seedSkillGroups(User $user, array $rows): void
    {
        foreach ($rows as $row) {
            $group = SkillGroup::updateOrCreate(
                ['user_id' => $user->id, 'sort_order' => $row['sort_order']],
                [
                    'label' => $row['label'],
                    'description' => $row['description'],
                ],
            );

            foreach ($row['skills'] as $index => $name) {
                $group->skills()->updateOrCreate(
                    ['name' => $name],
                    ['sort_order' => $index + 1],
                );
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function seedLanguages(User $user, array $rows): void
    {
        foreach ($rows as $row) {
            Language::updateOrCreate(
                ['user_id' => $user->id, 'code' => $row['code']],
                [
                    'sort_order' => $row['sort_order'],
                    'name' => $row['name'],
                    'level_label' => $row['level_label'],
                ],
            );
        }
    }

    /**
     * Seeded empty by design -- projects are added through the admin panel.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function seedProjects(User $user, array $rows): void
    {
        foreach ($rows as $row) {
            Project::updateOrCreate(
                ['user_id' => $user->id, 'slug' => $row['slug']],
                $row,
            );
        }
    }
}
