<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PersistentSession
{
    public function handle(Request $request, Closure $next)
    {
        // StartSession refreshes both the cookie and server expiry on each visit.
        config(['session.lifetime' => 60 * 24 * 30, 'session.expire_on_close' => false]);

        return $next($request);
    }
}
