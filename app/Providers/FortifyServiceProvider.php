<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Utente;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── rate limiter di default generati da Jetstream/Fortify ─────────
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ── login: accetta email O numero tessera nello stesso campo ──────
        // equivalente di setupLoginForm() in auth.js: se contiene "@" è
        // un'email, altrimenti si cerca l'Utente per numero_tessera, e si
        // procede solo se quell'Utente risulta "registrato"
        Fortify::authenticateUsing(function (Request $request) {
            $identifier = $request->input('email');

            if (str_contains($identifier, '@')) {
                $user = User::where('email', $identifier)->first();
            } else {
                if (!ctype_digit((string) $identifier)) {
                    return null; // stessa validazione di auth.js: solo numeri per la tessera
                }

                $utente = Utente::where('numero_tessera', $identifier)
                    ->where('registrato', true)
                    ->first();

                $user = $utente?->user;
            }

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        // ── testi/etichette delle view di Fortify ──────────────────────────
        Fortify::loginView(function () {
            return view('auth.login');
        });
    }
}