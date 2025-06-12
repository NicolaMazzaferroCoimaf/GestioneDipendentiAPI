<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class MakeAdminUser extends Command
{
    protected $signature = 'make:admin 
                            {--email=admin@example.com : Email dell\'admin} 
                            {--name=Admin : Nome dell\'admin}';

    protected $description = 'Crea o aggiorna un utente admin con ruolo "admin", password casuale e gruppi LDAP se presenti';

    public function handle()
    {
        $email = $this->option('email');
        $name = $this->option('name');
        $password = Str::random(24);
        $log = Log::channel('audit');

        // Cerca utente nel DB
        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->role !== 'admin') {
                $oldRole = $user->role;
                $user->role = 'admin';
                $user->save();

                $this->info("✅ Utente esistente aggiornato a ruolo admin.");
                $log->info("Ruolo aggiornato a 'admin'", [
                    'email' => $email,
                    'precedente_ruolo' => $oldRole,
                    'comando' => 'make:admin',
                ]);
            } else {
                $this->warn("ℹ️  L'utente con email $email è già admin.");
                $log->info("Tentativo di assegnare ruolo admin a utente già admin", [
                    'email' => $email,
                    'comando' => 'make:admin',
                ]);
            }
        } else {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->role = 'admin';

            // Prova sincronizzazione LDAP
            $ldapUser = LdapUser::where('mail', '=', $email)->first();
            if ($ldapUser) {
                $groupNames = collect($ldapUser->getAttribute('memberOf'))->map(function ($dn) {
                    preg_match('/CN=([^,]+)/', $dn, $matches);
                    return $matches[1] ?? null;
                })->filter()->values()->toArray();

                $user->ldap_groups = $groupNames;
            } else {
                $this->warn("⚠️  Utente non trovato in LDAP. Gruppi non sincronizzati.");
                $log->warning("LDAP: utente non trovato", ['email' => $email]);
            }

            $user->save();

            $this->info("✅ Utente admin creato con successo!");
            $this->line("Email: $email");
            $this->line("Password: $password");

            $log->info("Nuovo utente admin creato", [
                'email' => $email,
                'gruppi_ldap' => $user->ldap_groups ?? [],
                'comando' => 'make:admin',
            ]);
        }

        return Command::SUCCESS;
    }
}
