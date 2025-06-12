<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use LdapRecord\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LdapAuthController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->input('username'); // es: mario.rossi
        $password = $request->input('password');
        $log = Log::channel('auth');
        $log->info('Login request', ['username' => $username]);

        try {
            // Cerca utente LDAP
            $ldapUser = LdapUser::where('samaccountname', '=', $username)->first();

            if (!$ldapUser) {
                $log->error("Utente non trovato: {$username}");
                return response()->json(['message' => 'Utente non trovato.'], 404);
            }

            // Tenta autenticazione bind
            $auth = Container::getDefaultConnection()->auth()->attempt($ldapUser->getDn(), $password);

            if (!$auth) {
                $log->warning("Password errata per {$username}");
                return response()->json(['message' => 'Credenziali non valide.'], 401);
            }

            $log->info("Autenticazione LDAP riuscita per {$username}");

            // Estrai gruppi LDAP
            $groupNames = collect($ldapUser->getAttribute('memberOf'))->map(function ($dn) {
                preg_match('/CN=([^,]+)/', $dn, $matches);
                return $matches[1] ?? null;
            })->filter()->values()->toArray();

            // Crea o aggiorna l'utente locale
            $email = $ldapUser->getFirstAttribute('mail');
            $name = $ldapUser->getFirstAttribute('cn');

            // Trova utente locale se esiste
            $localUser = User::where('email', $email)->first();

            if (!$localUser) {
                // Se non esiste, crealo come operator
                $localUser = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make($password),
                    'role' => 'operator',
                ]);

            } else {
                // Se esiste, aggiorna solo il nome (e lascia il ruolo invariato)
                $localUser->name = $name;
                $localUser->username = $username; 
            }

            // Salva sempre i gruppi aggiornati
            $localUser->ldap_groups = $groupNames;
            $localUser->save();

            // Genera token Sanctum
            $token = $localUser->createToken('ldap-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'name' => $localUser->name,
                    'email' => $localUser->email,
                    'username' => $username,
                    'groups' => $groupNames,
                ],
                'message' => 'Autenticazione riuscita',
            ]);
        } catch (\Exception $e) {
            $log->error("Errore LDAP: " . $e->getMessage());
            return response()->json(['message' => 'Errore di autenticazione.'], 500);
        }
    }
}
