<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Cookie-based login for the first-party React admin, as Sanctum's SPA
 * authentication expects: the client first GETs /sanctum/csrf-cookie, then
 * posts here, and the `web` guard's session authenticates every later call.
 * No API tokens are issued.
 */
class SessionController extends Controller
{
    /**
     * GET /api/user
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->user($request)]);
    }

    /**
     * POST /api/login
     */
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = 'login:'.md5($credentials['email'].$request->ip());

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, decaySeconds: 60);

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return response()->json(['data' => $this->user($request)]);
    }

    /**
     * POST /api/logout
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * @return array{id: int, name: string, email: string}
     */
    private function user(Request $request): array
    {
        $user = $request->user();

        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
