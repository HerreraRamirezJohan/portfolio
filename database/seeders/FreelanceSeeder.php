<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\PublicContent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Starter copy for the freelance home page: services, cost factors, process
 * steps and FAQs.
 *
 * Source is database/data/freelance.json. CvSeeder calls this only when it
 * creates the user; an existing database runs it once with
 * `db:seed --class=FreelanceSeeder`. It never updates rows, and fills a
 * collection only while it is empty, so running it again cannot duplicate or
 * overwrite what was edited in the admin panel.
 */
class FreelanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();
        $data = $this->data();

        $this->fillIfEmpty($user->services(), $data['services']);
        $this->fillIfEmpty($user->processSteps(), $data['process_steps']);
        $this->fillIfEmpty($user->faqs(), $data['faqs']);
        $this->fillIfEmpty($user->costFactors(), $data['cost_factors']);

        PublicContent::flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        $path = database_path('data/freelance.json');

        if (! is_readable($path)) {
            throw new RuntimeException("Freelance seed data not found at {$path}.");
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param  HasMany<*, User>  $relation
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function fillIfEmpty(HasMany $relation, array $rows): void
    {
        if ($relation->exists()) {
            return;
        }

        foreach ($rows as $row) {
            $relation->create($row);
        }
    }
}
