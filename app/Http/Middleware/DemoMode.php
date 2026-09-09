<?php

namespace App\Http\Middleware;

use App\Services\DemoSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * Tag the request as demo, resolve/create the token cookie,
     * share $isDemoMode and $demoToken with all Blade views,
     * auto-seed default data on first visit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session = new DemoSession($request);
        $token   = $session->token();

        // Attach to request so controllers can read it
        $request->attributes->set('demo_token', $token);
        $request->attributes->set('demo_session', $session);
        $request->attributes->set('is_demo', true);

        // Share with every Blade template
        View::share('isDemoMode',  true);
        View::share('demoToken',   $token);
        View::share('demoSession', $session);

        // Auto-seed if this is a brand-new demo token
        if ($session->isFirstVisit()) {
            $this->seed($token);
        }

        /** @var Response $response */
        $response = $next($request);

        // Set the cookie on every demo response (refreshes TTL)
        $response->headers->setCookie(
            cookie(
                DemoSession::COOKIE_NAME,
                $token,
                DemoSession::COOKIE_TTL,
                '/',
                null,
                false,  // secure — set true when on HTTPS
                false,  // httpOnly false so JS can read it if needed
                false,
                'Lax'
            )
        );

        return $response;
    }

    /**
     * Seed one of each entity for brand-new demo tokens.
     */
    private function seed(string $token): void
    {
        try {
            \Database\Seeders\DemoSeeder::seedForToken($token);
        } catch (\Throwable) {
            // Silently fail — page still loads, just empty
        }
    }
}
