<?php

namespace App\Support;

use App\Models\Project;
use App\Models\WorkExperience;
use Illuminate\Support\Collection;

/**
 * Reads the career paths from config/portfolio.php and joins them to the
 * database rows they describe.
 */
class CareerPaths
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return config('portfolio.paths');
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /**
     * The path after this one, wrapping back to the first.
     */
    public static function next(string $key): string
    {
        $keys = self::keys();
        $index = array_search($key, $keys, true);

        return $keys[($index + 1) % count($keys)];
    }

    /**
     * Keys of every path that lists this company among its roles.
     *
     * @return array<int, string>
     */
    public static function forCompany(string $company): array
    {
        return array_keys(array_filter(
            self::all(),
            fn (array $path) => array_key_exists($company, $path['roles']),
        ));
    }

    /**
     * @param  Collection<int, WorkExperience>  $experiences
     * @return Collection<int, WorkExperience>
     */
    public static function experiences(string $key, Collection $experiences): Collection
    {
        $roles = self::find($key)['roles'] ?? [];

        return $experiences
            ->filter(fn (WorkExperience $row) => array_key_exists($row->company, $roles))
            ->values();
    }

    /**
     * Projects sharing at least one stack name with the path, ignoring case.
     *
     * @param  Collection<int, Project>  $projects
     * @return Collection<int, Project>
     */
    public static function projects(string $key, Collection $projects): Collection
    {
        $stack = array_map(self::normalise(...), self::find($key)['stack'] ?? []);

        return $projects
            ->filter(fn (Project $project) => collect($project->tech_stack ?? [])
                ->contains(fn ($tech) => in_array(self::normalise((string) $tech), $stack, true)))
            ->values();
    }

    /**
     * "2024 — 2026" spanning the given experiences, or null when there are none.
     *
     * @param  Collection<int, WorkExperience>  $experiences
     */
    public static function years(Collection $experiences): ?string
    {
        if ($experiences->isEmpty()) {
            return null;
        }

        $start = $experiences->min(fn (WorkExperience $row) => $row->start_date->year);

        $end = $experiences->contains(fn (WorkExperience $row) => $row->is_current || $row->end_date === null)
            ? __('Present')
            : $experiences->max(fn (WorkExperience $row) => $row->end_date->year);

        return $start == $end ? (string) $start : $start.' — '.$end;
    }

    private static function normalise(string $name): string
    {
        return mb_strtolower(trim($name));
    }
}
