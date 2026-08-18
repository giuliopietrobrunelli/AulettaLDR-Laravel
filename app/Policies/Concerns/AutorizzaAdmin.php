<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AutorizzaAdmin
{
    protected function isAdmin(User $user): bool
    {
        return $user->utente && $user->utente->amministratore !== null;
    }
}