<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Middleware\EnsureApplicantSession;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'guest'])->group(function () {
                Route::get('kcdt-ilss-awards-login', [LoginController::class, 'create'])->name('admin.login');
                Route::post('kcdt-ilss-awards-login', [LoginController::class, 'store'])->name('admin.login.store');
            });

            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'superadmin' => EnsureSuperAdmin::class,
            'applicant' => EnsureApplicantSession::class,
        ]);

        // Guests hitting auth-protected admin routes get 404 (do not leak login URL).
        $middleware->redirectGuestsTo(fn () => abort(404));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
