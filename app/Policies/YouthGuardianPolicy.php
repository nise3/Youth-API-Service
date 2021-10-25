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
     * @param Youth $user
     * @return Response
     */
    public function viewAny(Youth $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthGuardian.
     *
     * @param Youth $user
     * @param YouthGuardian $youthGuardian
     * @return Response
     */
    public function view(Youth $user, YouthGuardian $youthGuardian): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthGuardians.
     *
     * @param Youth $user
     * @return Response
     */
    public function create(Youth $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the youthGuardian.
     *
     * @param Youth $user
     * @param YouthGuardian $youthGuardian
     * @return Response
     */
    public function update(Youth $user, YouthGuardian $youthGuardian): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the youthGuardian.
     *
     * @param Youth $user
     * @param YouthGuardian $youthGuardian
     * @return Response
     */
    public function delete(Youth $user, YouthGuardian $youthGuardian): Response
    {
        return Response::allow();
    }
}
