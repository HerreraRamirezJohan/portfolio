<?php

namespace App\Http\Middleware;

use App\Support\PublicContent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clears the cached public payloads after any successful admin write, so an
 * edit shows up on the site immediately.
 */
class FlushPublicContent
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethodSafe() && $response->isSuccessful()) {
            PublicContent::flush();
        }

        return $response;
    }
}
