<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\PublicContent;
use Illuminate\Http\Response;

/**
 * Read-only data for the public site, in the locale named by the
 * `{locale}` prefix (applied by SetLocale). The payloads are built and
 * cached by PublicContent.
 */
class PortfolioController extends Controller
{
    /**
     * GET /api/{locale}/home -- the freelance landing page.
     */
    public function home(): Response
    {
        return $this->json(PublicContent::home());
    }

    /**
     * GET /api/{locale}/cv -- the full CV.
     */
    public function cv(): Response
    {
        return $this->json(PublicContent::cv());
    }

    /**
     * GET /api/{locale}/paths/{path} -- one career path page.
     */
    public function path(string $path): Response
    {
        return $this->json(PublicContent::path($path));
    }

    private function json(string $body): Response
    {
        return response($body, 200, ['Content-Type' => 'application/json']);
    }
}
