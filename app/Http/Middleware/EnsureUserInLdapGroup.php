<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserInLdapGroup
{
    public function handle(Request $request, Closure $next, $requiredGroup): Response
    {
        $user = Auth::user();

        // Carica i gruppi
        $groups = $user->ldap_groups ?? [];

        // Se per sicurezza è ancora JSON string
        if (is_string($groups)) {
            $groups = json_decode($groups, true);
        }

        if (!is_array($groups) || !in_array($requiredGroup, $groups)) {
            return response()->json(['message' => 'Accesso negato.'], 403);
        }

        return $next($request);
    }
}