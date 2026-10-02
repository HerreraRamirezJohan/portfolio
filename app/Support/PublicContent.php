<?php

namespace App\Support;

use App\Http\Resources\CareerPathResource;
use App\Http\Resources\CareerPathSummaryResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SkillGroupResource;
use App\Http\Resources\WorkExperienceResource;
use App\Models\User;
use App\Models\WorkExperience;
use Closure;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

/**
 * The public pages' payloads as ready-to-send JSON strings, in the active
 * locale, cached until the admin changes something.
 *
 * The same string answers the API and is inlined into the Blade shell, so
 * the first paint of a deep link never waits for a second request.
 */
class PublicContent
{
    /**
     * Escapes <, >, & and quotes so the string is safe inside a <script>.
     */
    private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR;

    /**
     * GET /api/{locale}/portfolio -- the home page.
     */
    public static function home(): string
    {
        return self::remember('home', function () {
            $user = User::with([
                'profile',
                'workExperiences',
                'educations',
                'languages',
                'skillGroups.skills',
                'projects' => fn ($query) => $query->published(),
            ])->firstOrFail();

            return [
                'meta' => SiteMeta::home($user),
                'person' => new PersonResource($user),
                'paths' => self::paths($user),
                // Each path's flow, drawn as a pipeline row that links to the path page.
                'pipelines' => collect(CareerPaths::all())->map(fn (array $path, string $key) => [
                    'key' => $key,
                    'title' => __($path['card_title']),
                    'nodes' => CareerPathResource::nodes($path['flow']),
                ])->values(),
                'projects' => ProjectResource::collection($user->projects),
                'skills' => SkillGroupResource::collection($user->skillGroups),
                'experience' => [
                    'count' => $user->workExperiences->count(),
                    'years' => CareerPaths::years($user->workExperiences),
                    'items' => WorkExperienceResource::collection($user->workExperiences),
                ],
                'educations' => EducationResource::collection($user->educations),
                'languages' => LanguageResource::collection($user->languages),
            ];
        });
    }

    /**
     * GET /api/{locale}/paths/{path} -- one career path page.
     */
    public static function path(string $path): string
    {
        return self::remember('path.'.$path, function () use ($path) {
            $config = CareerPaths::find($path);

            $user = User::with([
                'profile',
                'workExperiences',
                'projects' => fn ($query) => $query->published(),
            ])->firstOrFail();

            $experiences = CareerPaths::experiences($path, $user->workExperiences);
            $next = CareerPaths::next($path);

            return [
                'meta' => SiteMeta::path($path, $user),
                'person' => new PersonResource($user),
                'paths' => self::paths($user),
                'path' => new CareerPathResource(['key' => $path] + $config),
                'years' => CareerPaths::years($experiences),
                'roles' => $experiences->map(fn (WorkExperience $row) => (new WorkExperienceResource($row))->resolve() + [
                    'blurb' => __($config['roles'][$row->company]),
                ]),
                'projects' => ProjectResource::collection(CareerPaths::projects($path, $user->projects)),
                'next' => new CareerPathSummaryResource(['key' => $next] + CareerPaths::find($next)),
            ];
        });
    }

    /**
     * The `meta` block of a payload, for the shell's <head>.
     *
     * @return array{title: string, description: string|null}
     */
    public static function meta(string $json): array
    {
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR)['data']['meta'];
    }

    /**
     * Drop every cached payload. Called after any admin write and by CvSeeder.
     */
    public static function flush(): void
    {
        foreach (config('portfolio.locales') as $locale) {
            Cache::forget(self::key($locale, 'home'));

            foreach (CareerPaths::keys() as $path) {
                Cache::forget(self::key($locale, 'path.'.$path));
            }
        }
    }

    /**
     * @param  Closure(): array<string, mixed>  $build
     */
    private static function remember(string $name, Closure $build): string
    {
        $encode = fn () => json_encode(['data' => $build()], self::JSON_FLAGS);
        $ttl = (int) config('portfolio.cache_ttl');

        return $ttl > 0
            ? Cache::remember(self::key(app()->getLocale(), $name), $ttl, $encode)
            : $encode();
    }

    private static function key(string $locale, string $name): string
    {
        return "public-content.{$locale}.{$name}";
    }

    /**
     * Every path with the years its roles span, for the nav and the cards.
     */
    private static function paths(User $user): AnonymousResourceCollection
    {
        return CareerPathSummaryResource::collection(
            collect(CareerPaths::all())->map(fn (array $path, string $key) => ['key' => $key] + $path + [
                'years' => CareerPaths::years(CareerPaths::experiences($key, $user->workExperiences)),
            ])->values(),
        );
    }
}
