<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class RemoveAdminRole extends Command
{
    protected $signature = 'remove:admin 
                            {--email= : Email dell\'utente da declassare}';

    protected $description = 'Rimuove il ruolo admin da un utente (lo imposta a "operator")';

    public function handle()
    {
        $email = $this->option('email');

        if (!$email) {
            $this->error('❌ Devi specificare un\'email con --email=');
            return Command::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Nessun utente trovato con email: $email");
            return Command::FAILURE;
        }

        if ($user->role !== 'admin') {
            $this->info("ℹ️ L'utente $email non è admin. Nessuna modifica effettuata.");
            return Command::SUCCESS;
        }

        $user->role = 'operator';
        $user->save();

        $this->info("✅ Ruolo admin rimosso da $email. Ora è un semplice operatore.");

        return Command::SUCCESS;
    }
}
