<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $utente = $request->user()?->utente;

        if (!$utente || $utente->amministratore === null) {
            abort(403, 'Accesso riservato agli amministratori.');
        }

        return $next($request);
    }
}