<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Generate monthly invoices daily at 1:00 AM
        $schedule->command('invoices:generate-monthly')->dailyAt('01:00');

        // Check for overdue schools daily at 2:00 AM
        $schedule->command('schools:deactivate-overdue')->dailyAt('02:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'school_admin' => \App\Http\Middleware\SchoolAdminMiddleware::class,
            'issuer' => \App\Http\Middleware\IssuerMiddleware::class,
            'student.auth' => \App\Http\Middleware\StudentAuth::class,
            'student.guest' => \App\Http\Middleware\RedirectIfStudentAuthenticated::class,
        ]);

        // Add school active check to web middleware group (runs on all web routes)
        $middleware->web(append: [
            \App\Http\Middleware\CheckSchoolActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
