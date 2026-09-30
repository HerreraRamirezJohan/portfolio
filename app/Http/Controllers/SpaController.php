<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CareerPaths;
use App\Support\SiteMeta;
use Illuminate\Contracts\View\View;

/**
 * Serves the Blade shell that boots the React app.
 *
 * Only the <head> is rendered here -- title, description and hreflang, so
 * crawlers and link previews see them without running JavaScript. The page
 * content itself comes from the JSON API.
 */
class SpaController extends Controller
{
    public function portfolio(): View
    {
        $user = User::with('profile')->firstOrFail();

        return $this->shell(SiteMeta::home($user) + [
            'alternates' => SiteMeta::alternates('portfolio'),
        ]);
    }

    public function path(string $path): View
    {
        $user = User::with('profile')->firstOrFail();

        return $this->shell(SiteMeta::path($path, $user) + [
            'alternates' => SiteMeta::alternates('path', ['path' => $path]),
        ]);
    }

    public function admin(): View
    {
        return $this->shell(['title' => __('Admin'), 'description' => null, 'alternates' => []]);
    }

    /**
     * @param  array{title: string, description: string|null, alternates: array<string, string>}  $meta
     */
    private function shell(array $meta): View
    {
        return view('app', $meta + [
            'config' => [
                'locales' => config('portfolio.locales'),
                'defaultLocale' => config('app.locale'),
                'paths' => CareerPaths::keys(),
            ],
        ]);
    }
}
