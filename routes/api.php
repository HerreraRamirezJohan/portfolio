<?php

use App\Http\Controllers\Api\Admin\CostFactorController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\EducationController;
use App\Http\Controllers\Api\Admin\ExperienceController;
use App\Http\Controllers\Api\Admin\FaqController;
use App\Http\Controllers\Api\Admin\LanguageController;
use App\Http\Controllers\Api\Admin\ProcessStepController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\SkillGroupController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Middleware\FlushPublicContent;
use App\Http\Middleware\SetLocale;
use App\Support\CareerPaths;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*
|--------------------------------------------------------------------------
| Public content -- /api/{locale}/...
|--------------------------------------------------------------------------
|
| Read-only JSON for the React public site, translated into the locale in
| the prefix. There is deliberately no registration endpoint: this is a
| single-user site and the one account comes from CvSeeder.
|
| These answers are the same for everyone, so they skip Sanctum's session
| middleware (no cookie, no session write) and carry an ETag: a repeat
| request costs a 304 with no body.
|
*/

Route::prefix('{locale}')
    ->whereIn('locale', config('portfolio.locales'))
    ->middleware([SetLocale::class, 'cache.headers:public;max_age=0;must_revalidate;etag'])
    ->withoutMiddleware(EnsureFrontendRequestsAreStateful::class)
    ->group(function () {
        Route::get('home', [PortfolioController::class, 'home'])->name('api.home');
        Route::get('cv', [PortfolioController::class, 'cv'])->name('api.cv');

        Route::get('paths/{path}', [PortfolioController::class, 'path'])
            ->whereIn('path', CareerPaths::keys())
            ->name('api.path');
    });

/*
|--------------------------------------------------------------------------
| Session -- Sanctum SPA authentication
|--------------------------------------------------------------------------
|
| The React admin calls GET /sanctum/csrf-cookie first, then logs in here.
|
*/

Route::post('login', [SessionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [SessionController::class, 'show'])->name('api.user');
    Route::post('logout', [SessionController::class, 'destroy'])->name('api.logout');

    /*
    |----------------------------------------------------------------------
    | Admin -- /api/admin/...
    |----------------------------------------------------------------------
    */

    Route::prefix('admin')->name('api.admin.')->middleware(FlushPublicContent::class)->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::delete('profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

        $collections = [
            'experiences' => ExperienceController::class,
            'educations' => EducationController::class,
            'skill-groups' => SkillGroupController::class,
            'languages' => LanguageController::class,
            'projects' => ProjectController::class,
            'services' => ServiceController::class,
            'process-steps' => ProcessStepController::class,
            'faqs' => FaqController::class,
            'cost-factors' => CostFactorController::class,
        ];

        foreach ($collections as $name => $controller) {
            Route::apiResource($name, $controller)->except('show');

            $parameter = str_replace('-', '_', Str::singular($name));
            Route::patch("$name/{{$parameter}}/move", [$controller, 'move'])->name("$name.move");
        }
    });
});
