<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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

        // Recupera i gruppi LDAP dell'utente
        $groups = $user->ldap_groups ?? [];

        // Decodifica JSON se salvato come stringa
        if (is_string($groups)) {
            $groups = json_decode($groups, true);
        }

        // Verifica se il gruppo richiesto è presente
        if (!is_array($groups) || !in_array($requiredGroup, $groups)) {
            Log::channel('auth')->warning('Accesso negato: gruppo LDAP mancante', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_group' => $requiredGroup,
                'user_groups' => $groups
            ]);

            return response()->json(['message' => 'Accesso negato.'], 403);
        }

        return $next($request);
    }
}
