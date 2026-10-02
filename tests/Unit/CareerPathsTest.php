<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\WorkExperience;
use App\Support\CareerPaths;
use Tests\TestCase;

class CareerPathsTest extends TestCase
{
    public function test_next_wraps_back_to_the_first_path(): void
    {
        $this->assertSame('web', CareerPaths::next('sap'));
        $this->assertSame('sap', CareerPaths::next('infra'));
    }

    public function test_a_company_belongs_to_every_path_that_names_it(): void
    {
        $this->assertSame(['sap', 'web', 'infra'], CareerPaths::forCompany('Global Asiática'));
        $this->assertSame(['sap'], CareerPaths::forCompany('MAVI de Occidente'));
        $this->assertSame([], CareerPaths::forCompany('Somewhere Else'));
    }

    public function test_years_span_the_earliest_start_to_the_latest_end(): void
    {
        $rows = collect([
            $this->experience('2024-07-01', '2026-03-01'),
            $this->experience('2026-03-01', '2026-08-01'),
        ]);

        $this->assertSame('2024 - 2026', CareerPaths::years($rows));
        $this->assertNull(CareerPaths::years(collect()));
    }

    public function test_years_end_in_present_for_a_current_role(): void
    {
        $this->app->setLocale('en');
        $this->assertSame('2025 - Present', CareerPaths::years(collect([$this->experience('2025-01-01', null, true)])));
    }

    public function test_a_range_within_one_year_shows_months(): void
    {
        $this->app->setLocale('en');
        $this->assertSame('Mar - Aug 2026', $this->experience('2026-03-01', '2026-08-01')->compactRange());

        $this->app->setLocale('es');
        $this->assertSame('Mar - Ago 2026', $this->experience('2026-03-01', '2026-08-01')->compactRange());
        $this->assertSame('2024 - 2026', $this->experience('2024-07-01', '2026-03-01')->compactRange());
    }

    public function test_short_name_keeps_the_first_name_and_paternal_surname(): void
    {
        $this->assertSame('Johan Herrera', (new User(['name' => 'Johan Osvaldo Herrera Ramírez']))->shortName());
        $this->assertSame('Ana López', (new User(['name' => 'Ana López']))->shortName());
    }

    private function experience(string $start, ?string $end, bool $current = false): WorkExperience
    {
        return new WorkExperience(['start_date' => $start, 'end_date' => $end, 'is_current' => $current]);
    }
}
