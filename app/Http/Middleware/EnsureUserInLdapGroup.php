<?php

namespace App\Http\Middleware;

use Closure;
use LdapRecord\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\User;
use Symfony\Component\HttpFoundation\Response;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class EnsureUserInLdapGroup
{
    /**
     * Controlla che l'utente appartenga al gruppo LDAP richiesto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $requiredGroup
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $requiredGroup): Response
    {
        $user = Auth::user();

        /* ✅  salta tutti i controlli se è admin */
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        /* --- sotto rimane invariato --- */
        $email = $user->email;
        $ldapUser = LdapUser::where('mail', '=', $email)->first();

        if (!$ldapUser) {
            Log::channel('auth')->warning(
                '⚠️  Utente non trovato in LDAP. Gruppi non sincronizzati.',
                ['email' => $email]
            );
            return response()->json(['message' => 'Accesso negato. Nessun gruppo LDAP trovato.'], 403);
        }

        $groupNames = collect($ldapUser->getAttribute('memberOf'))
            ->map(function ($dn) {
                preg_match('/CN=([^,]+)/', $dn, $m);
                return $m[1] ?? null;
            })
            ->filter()->values()->toArray();

        $user->ldap_groups = $groupNames;

        $groups = is_string($groupNames) ? json_decode($groupNames, true) : $groupNames;

        if (!in_array($requiredGroup, $groups, true)) {
            Log::channel('auth')->warning('Accesso negato: gruppo LDAP mancante', [
                'user_id'        => $user->id,
                'user_email'     => $user->email,
                'required_group' => $requiredGroup,
                'user_groups'    => $groups,
            ]);
            return response()->json(['message' => 'Accesso negato.'], 403);
        }

        return $next($request);
    }
}
