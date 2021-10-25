<?php

namespace App\Policies;

use App\Models\Youth;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param Youth $user
     * @param $ability
     * @return null
     */
    public function before(Youth $user, $ability)
    {
        return null;
    }
}
