<?php

use App\Http\Middleware\CheckMonthly;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ClearAnswerSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web( [
            ClearAnswerSession::class,
        ]);
        $middleware->appendToGroup('checkMonthly', [
            CheckMonthly::class,
        ]);
        $middleware->appendToGroup('checkRole', [
            CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
