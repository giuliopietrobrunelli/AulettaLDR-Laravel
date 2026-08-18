<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AutorizzaAdmin;

class StatistichePolicy
{
    use AutorizzaAdmin;

    public function view(User $user): bool
    {
        return $this->isAdmin($user);
    }
}