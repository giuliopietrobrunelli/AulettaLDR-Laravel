<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;
use App\Policies\Concerns\AutorizzaAdmin;

class FeedbackPolicy
{
    use AutorizzaAdmin;

    public function create(User $user): bool
    {
        $utente = $user->utente;

        return $utente !== null && $utente->registrato;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Feedback $feedback): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $this->isAdmin($user);
    }
}