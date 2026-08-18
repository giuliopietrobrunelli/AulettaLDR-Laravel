<?php

namespace App\Policies;

use App\Models\Turno;
use App\Models\User;
use App\Policies\Concerns\AutorizzaAdmin;

class TurnoPolicy
{
    use AutorizzaAdmin;

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Turno $turno): bool
    {
        return $this->isAdmin($user);
    }

    public function deactivate(User $user, Turno $turno): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Turno $turno): bool
    {
        return $this->isAdmin($user);
    }
}