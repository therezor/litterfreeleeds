<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // In production this app is only ever reached through Cloudflare and
        // then nginx, both of which forward the original scheme in a header.
        // Without trusting them Laravel reads every request as plain http.
        //
        // That is not cosmetic here: verification links are signed URLs, and a
        // signature is computed over the whole URL. The link in the email is
        // generated from APP_URL (https) but would be validated against an
        // http request — so every volunteer following a valid link would get
        // "Invalid signature" and could never verify. Same for the password
        // step that hangs off it.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
