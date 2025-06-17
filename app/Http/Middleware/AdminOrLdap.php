<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrLdap
{
    public function handle(Request $request, Closure $next, string $requiredGroup): Response
    {
        /* se è admin → passa */
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        /* altrimenti ri-usa EnsureUserInLdapGroup */
        return app(EnsureUserInLdapGroup::class)
            ->handle($request, $next, $requiredGroup);
    }
}
