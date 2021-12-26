<?php

namespace App\Policies;

use App\Models\YouthGuardian;
use App\Models\Youth;
use Illuminate\Auth\Access\Response;

class YouthGuardianPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any youthGuardians.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthGuardian.
     *
     * @param Youth $youth
     * @param YouthGuardian $youthGuardian
     * @return Response
     */
    public function view(Youth $youth, YouthGuardian $youthGuardian): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthGuardians.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth): bool
    {
        return $this->isUserLoggedIn($youth);
    }

    /**
     * Determine whether the user can update the youthGuardian.
     *
     * @param Youth $youth
     * @param YouthGuardian $youthGuardian
     * @return bool
     */
    public function update(Youth $youth, YouthGuardian $youthGuardian): bool
    {
        return $this->isOwner($youth, $youthGuardian->youth_id);
    }

    /**
     * Determine whether the user can delete the youthGuardian.
     *
     * @param Youth $youth
     * @param YouthGuardian $youthGuardian
     * @return bool
     */
    public function delete(Youth $youth, YouthGuardian $youthGuardian): bool
    {
        return $this->isOwner($youth, $youthGuardian->youth_id);
    }
}
