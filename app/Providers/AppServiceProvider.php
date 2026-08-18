<?php

namespace App\Providers;

use App\Models\Utente;
use App\Policies\UtentePolicy;
use App\Models\Turno;
use App\Policies\TurnoPolicy;
use App\Models\Prenotazione;
use App\Policies\PrenotazionePolicy;
use App\Models\RichiestaCessione;
use App\Policies\RichiestaCessionePolicy;
use App\Models\Impostazioni;
use App\Policies\ImpostazioniPolicy;
use App\Models\Feedback;
use App\Policies\FeedbackPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Prenotazione::class, PrenotazionePolicy::class);
        Gate::policy(Utente::class, UtentePolicy::class);
        Gate::policy(Turno::class, TurnoPolicy::class);
        Gate::policy(RichiestaCessione::class, RichiestaCessionePolicy::class);
        Gate::policy(Feedback::class, FeedbackPolicy::class);
        Gate::policy(Impostazioni::class, ImpostazioniPolicy::class);
        Gate::define('viewStatistiche', function (User $user) {
            return $user->utente && $user->utente->amministratore !== null;
        });
    }
}