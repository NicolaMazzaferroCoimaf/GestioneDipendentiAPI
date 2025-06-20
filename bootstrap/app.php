<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\AdminOrLdap;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\EnsureUserInLdapGroup;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isAdmin' => IsAdmin::class,
            'ldap.group' => EnsureUserInLdapGroup::class, // Da usare solo se anche l' admin deve appartenere ad un gruppo LDAP
            'admin.or.ldap'  => AdminOrLdap::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('notify:expiring-documents')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
