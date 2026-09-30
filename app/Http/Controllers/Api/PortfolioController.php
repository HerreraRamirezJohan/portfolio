<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerPathResource;
use App\Http\Resources\CareerPathSummaryResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\WorkExperienceResource;
use App\Models\User;
use App\Models\WorkExperience;
use App\Support\CareerPaths;
use App\Support\SiteMeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Read-only data for the public site, in the locale named by the
 * `{locale}` prefix (applied by SetLocale).
 */
class PortfolioController extends Controller
{
    /**
     * GET /api/{locale}/portfolio -- the home page.
     */
    public function home(): JsonResponse
    {
        $user = User::with(['profile', 'workExperiences', 'educations', 'languages'])->firstOrFail();

        return response()->json(['data' => [
            'meta' => SiteMeta::home($user),
            'person' => new PersonResource($user),
            'paths' => $this->paths($user),
            'experience' => [
                'count' => $user->workExperiences->count(),
                'years' => CareerPaths::years($user->workExperiences),
                'items' => WorkExperienceResource::collection($user->workExperiences),
            ],
            'educations' => EducationResource::collection($user->educations),
            'languages' => LanguageResource::collection($user->languages),
        ]]);
    }

    /**
     * GET /api/{locale}/paths/{path} -- one career path page.
     */
    public function path(string $path): JsonResponse
    {
        $config = CareerPaths::find($path);

        $user = User::with([
            'profile',
            'workExperiences',
            'projects' => fn ($query) => $query->published(),
        ])->firstOrFail();

        $experiences = CareerPaths::experiences($path, $user->workExperiences);
        $next = CareerPaths::next($path);

        return response()->json(['data' => [
            'meta' => SiteMeta::path($path, $user),
            'person' => new PersonResource($user),
            'paths' => $this->paths($user),
            'path' => new CareerPathResource(['key' => $path] + $config),
            'years' => CareerPaths::years($experiences),
            'roles' => $experiences->map(fn (WorkExperience $row) => (new WorkExperienceResource($row))->resolve() + [
                'blurb' => __($config['roles'][$row->company]),
            ]),
            'projects' => ProjectResource::collection(CareerPaths::projects($path, $user->projects)),
            'next' => new CareerPathSummaryResource(['key' => $next] + CareerPaths::find($next)),
        ]]);
    }

    /**
     * Every path with the years its roles span, for the nav and the cards.
     */
    private function paths(User $user): AnonymousResourceCollection
    {
        return CareerPathSummaryResource::collection(
            collect(CareerPaths::all())->map(fn (array $path, string $key) => ['key' => $key] + $path + [
                'years' => CareerPaths::years(CareerPaths::experiences($key, $user->workExperiences)),
            ])->values(),
        );
    }
}
