<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        // Cerca utente nel DB
        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();

                $this->info("✅ Utente esistente aggiornato a ruolo admin.");
            } else {
                $this->warn("ℹ️  L'utente con email $email è già admin.");
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
            }

            $user->save();

            $this->info("✅ Utente admin creato con successo!");
            $this->line("Email: $email");
            $this->line("Password: $password");
        }

        return Command::SUCCESS;
    }
}
