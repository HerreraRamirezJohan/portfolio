<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the {locale} route segment to the app, and strips it from the
 * route parameters so controllers and components never have to accept it.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (in_array($locale, config('portfolio.locales'), true)) {
            app()->setLocale($locale);
        }

        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
