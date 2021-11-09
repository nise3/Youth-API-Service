<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthCertification;
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

    public function isOwner(Youth $user, int $youthId)
    {
        return $user->id == $youthId;
    }

    public function isUserLoggedIn(Youth $user)
    {
        return $user != null;
    }
}
