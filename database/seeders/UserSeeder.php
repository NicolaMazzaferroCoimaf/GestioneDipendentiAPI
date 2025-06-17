<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── ADMIN ───────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'        => 'Admin Demo',
                'username'    => 'super.admin',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'ldap_groups' => [
                    "GESTIONALE-Landing",
                    "GESTIONALE-ListaDipendenti",
                    "GESTIONALE-AlberoGiacenze",
                    "GESTIONALE-Presenze",
                    "GESTIONALE-Ordini",
                    "GESTIONALE-Attrezzaturefood",
                    "GESTIONALE-KPI",
                    "GESTIONALE-Bricocanali",
                    "GESTIONALE-Magazzino",
                    "GESTIONALE-SistemaGestione",
                    "GESTIONALE-Listini",
                    "GESTIONALE-Licenze",
                    "GESTIONALE-Utenze",
                    "GESTIONALE-Ordini_Passivi",
                    "GESTIONALE-Ordini_Attivi",
                    "GESTIONALE-Flotta",
                    "GESTIONALE-Sottoscorta",
                    "GESTIONALE-FPC",
                    "GESTIONALE-Impostazioni",
                    "GESTIONALE-DbMacchine",
                    "GESTIONALE-Ticket",
                    "GESTIONALE-DPI",
                    "GESTIONALE-Dipendenti",
                    "GESTIONALE-Scadenzario",
                ],
            ]
        );

        // ── OPERATOR ────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name'        => 'Mario Rossi',
                'username'    => 'mario.rossi',
                'password'    => Hash::make('password'),
                'role'        => 'operator',
                'ldap_groups' => ['GESTIONALE-Dipendenti'], // se vuoi simulare gruppi LDAP
            ]
        );
    }
}
