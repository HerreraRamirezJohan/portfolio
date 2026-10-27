<?php

namespace App\Support;

use App\Models\User;

/**
 * Title, description and hreflang alternates for the public pages.
 *
 * Shared by the API (so React can update document.title on client-side
 * navigation) and by the Blade shell (so crawlers and link previews get
 * the right tags on the first response, before any JavaScript runs).
 */
class SiteMeta
{
    /**
     * @return array{title: string, description: string|null}
     */
    public static function home(User $user): array
    {
        return [
            'title' => $user->name.' - '.__('Websites, systems and SAP changes for your business'),
            'description' => __('Freelance developer. Web programming and SAP (ABAP) development by the hour, with a written estimate before we start.'),
        ];
    }

    /**
     * @return array{title: string, description: string|null}
     */
    public static function cv(User $user): array
    {
        $headline = $user->profile?->headline;

        return [
            'title' => filled($headline) ? $user->name.' — '.$headline : $user->name,
            'description' => null,
        ];
    }

    /**
     * @return array{title: string, description: string|null}
     */
    public static function path(string $key, User $user): array
    {
        $path = CareerPaths::find($key);

        return [
            'title' => __($path['title']).' — '.$user->name,
            'description' => __($path['lead']),
        ];
    }

    /**
     * The same page in every supported locale, for the language switch and
     * the hreflang links.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, string>
     */
    public static function alternates(string $route, array $parameters = []): array
    {
        return collect(config('portfolio.locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route($route, ['locale' => $locale] + $parameters)])
            ->all();
    }
}
