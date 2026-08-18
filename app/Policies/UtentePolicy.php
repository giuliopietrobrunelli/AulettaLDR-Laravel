<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utente;
use App\Policies\Concerns\AutorizzaAdmin;

class UtentePolicy
{
    use AutorizzaAdmin;

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Utente $utente): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Utente $utente): bool
    {
        return $this->isAdmin($user) || $user->utente?->id_utente === $utente->id_utente;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}