<?php

namespace App\Http\Controllers;

use App\Support\CareerPaths;
use App\Support\PublicContent;
use App\Support\SiteMeta;
use Illuminate\Contracts\View\View;

/**
 * Serves the Blade shell that boots the React app.
 *
 * The <head> is rendered here -- title, description and hreflang, so
 * crawlers and link previews see them without running JavaScript -- and the
 * page's own API payload is inlined, so React renders a deep link without
 * waiting for a request.
 */
class SpaController extends Controller
{
    public function portfolio(): View
    {
        return $this->page(
            api: '/'.app()->getLocale().'/portfolio',
            json: PublicContent::home(),
            alternates: SiteMeta::alternates('portfolio'),
        );
    }

    public function path(string $path): View
    {
        return $this->page(
            api: '/'.app()->getLocale().'/paths/'.$path,
            json: PublicContent::path($path),
            alternates: SiteMeta::alternates('path', ['path' => $path]),
        );
    }

    public function admin(): View
    {
        return $this->shell(['title' => __('Admin'), 'description' => null, 'alternates' => []]);
    }

    /**
     * @param  array<string, string>  $alternates
     */
    private function page(string $api, string $json, array $alternates): View
    {
        return $this->shell(PublicContent::meta($json) + [
            'alternates' => $alternates,
            'bootstrap' => ['url' => $api, 'json' => $json],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function shell(array $data): View
    {
        return view('app', $data + [
            'config' => [
                'locales' => config('portfolio.locales'),
                'defaultLocale' => config('app.locale'),
                'paths' => CareerPaths::keys(),
            ],
        ]);
    }
}
