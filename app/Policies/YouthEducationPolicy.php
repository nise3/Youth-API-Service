<?php

namespace App\Policies;

use App\Models\YouthEducation;
use App\Models\Youth;
use Illuminate\Auth\Access\Response;

class YouthEducationPolicy extends BasePolicy
{

    /**
     * Determine whether the user can view any youthEducations.
     *
     * @param Youth $user
     * @return Response
     */
    public function viewAny(Youth $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthEducation.
     *
     * @param Youth $user
     * @param YouthEducation $youthEducation
     * @return Response
     */
    public function view(Youth $user, YouthEducation $youthEducation): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthEducations.
     *
     * @param Youth $user
     * @return Response
     */
    public function create(Youth $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the youthEducation.
     *
     * @param Youth $user
     * @param YouthEducation $youthEducation
     * @return Response
     */
    public function update(Youth $user, YouthEducation $youthEducation): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the youthEducation.
     *
     * @param Youth $user
     * @param YouthEducation $youthEducation
     * @return Response
     */
    public function delete(Youth $user, YouthEducation $youthEducation): Response
    {
        return Response::allow();
    }
}
