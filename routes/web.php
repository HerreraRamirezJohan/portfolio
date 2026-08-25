<?php

use App\Http\Middleware\SetLocale;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EducationManager;
use App\Livewire\Admin\ExperienceManager;
use App\Livewire\Admin\LanguageManager;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\ProfileForm;
use App\Livewire\Admin\ProjectManager;
use App\Livewire\Admin\SkillGroupManager;
use App\Livewire\PublicSite\Portfolio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
|
| Locale-prefixed: /es and /en. "/" redirects to the default locale.
| There is deliberately no registration route -- this is a single-user site
| and the one account comes from CvSeeder.
|
*/

Route::redirect('/', '/'.config('app.locale'));

Route::prefix('{locale}')
    ->whereIn('locale', config('portfolio.locales'))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', Portfolio::class)->name('portfolio');
    });

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', Login::class)->name('login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('profile', ProfileForm::class)->name('profile');
        Route::get('experience', ExperienceManager::class)->name('experience');
        Route::get('education', EducationManager::class)->name('education');
        Route::get('skills', SkillGroupManager::class)->name('skills');
        Route::get('languages', LanguageManager::class)->name('languages');
        Route::get('projects', ProjectManager::class)->name('projects');

        Route::post('logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('admin.login');
        })->name('logout');
    });
});
