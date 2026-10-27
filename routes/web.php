<?php

use App\Http\Controllers\SpaController;
use App\Http\Middleware\SetLocale;
use App\Support\CareerPaths;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| React shell
|--------------------------------------------------------------------------
|
| Every page is rendered by the React app in resources/js, which reads its
| data from routes/api.php. These routes exist so that deep links load the
| shell with the right <head>, and so that unknown URLs still 404 on the
| server: an unlisted locale or path never matches.
|
*/

Route::redirect('/', '/'.config('app.locale'));

Route::prefix('{locale}')
    ->whereIn('locale', config('portfolio.locales'))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [SpaController::class, 'home'])->name('home');
        Route::get('cv', [SpaController::class, 'cv'])->name('cv');

        Route::get('{path}', [SpaController::class, 'path'])
            ->whereIn('path', CareerPaths::keys())
            ->name('path');
    });

/*
|--------------------------------------------------------------------------
| Admin shell
|--------------------------------------------------------------------------
|
| Guarded by the session here too, so a signed-out visitor never sees the
| admin UI flash before the API answers 401. React Router takes over from
| the shell; the data itself is protected by auth:sanctum in routes/api.php.
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [SpaController::class, 'admin'])->middleware('guest')->name('login');

    Route::middleware('auth')->group(function () {
        Route::get('/', [SpaController::class, 'admin'])->name('dashboard');
        Route::get('{section}', [SpaController::class, 'admin'])
            ->whereIn('section', ['profile', 'experience', 'education', 'skills', 'languages', 'projects', 'services', 'process', 'faq', 'costs'])
            ->name('section');
    });
});
