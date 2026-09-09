<?php

namespace App\Http\Middleware;

use App\Services\DemoSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoLimit
{
    /**
     * Before any POST store action in demo mode, check the 10-record cap.
     * If limit is hit, redirect back with a flash error — no DB write occurs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('POST')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';

        // Look up which model class this route creates
        $modelClass = DemoSession::ROUTE_MODEL_MAP[$routeName] ?? null;

        if ($modelClass === null) {
            // Route not in the limit map (e.g. void, update, delete) — allow
            return $next($request);
        }

        /** @var DemoSession $session */
        $session = $request->attributes->get('demo_session');

        if ($session && $session->limitReached($modelClass)) {
            $label = DemoSession::MODEL_LABELS[$modelClass] ?? 'records';

            return back()
                ->withInput()
                ->with('demo_limit_error', "Demo limit reached — you can add up to " . DemoSession::RECORD_LIMIT . " {$label} in demo mode. Open a new browser to start fresh.");
        }

        return $next($request);
    }
}
