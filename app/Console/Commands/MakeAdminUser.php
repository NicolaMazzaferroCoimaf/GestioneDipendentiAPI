<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MakeAdminUser extends Command
{
    protected $signature = 'make:admin 
                            {--email=admin@example.com : Email dell\'admin} 
                            {--password=password : Password dell\'admin} 
                            {--name=Admin : Nome dell\'admin}';

    protected $description = 'Crea un utente admin con ruolo "admin"';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        if (User::where('email', $email)->exists()) {
            $this->error("❌ Utente con email $email già esistente.");
            return Command::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role = 'admin';
        $user->save();

        $this->info("✅ Utente admin creato con successo!");
        $this->line("Email: $email");
        $this->line("Password: $password");

        return Command::SUCCESS;
    }
}
