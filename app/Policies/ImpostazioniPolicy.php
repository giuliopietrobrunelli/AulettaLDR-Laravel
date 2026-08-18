<?php

namespace App\Policies;

use App\Models\Impostazioni;
use App\Models\User;
use App\Policies\Concerns\AutorizzaAdmin;

class ImpostazioniPolicy
{
    use AutorizzaAdmin;

    public function update(User $user, Impostazioni $impostazioni): bool
    {
        return $this->isAdmin($user);
    }
}