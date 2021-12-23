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
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthEducation.
     *
     * @param Youth $youth
     * @param YouthEducation $youthEducation
     * @return Response
     */
    public function view(Youth $youth, YouthEducation $youthEducation): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthEducations.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth): bool
    {
        return $this->isUserLoggedIn($youth);
    }

    /**
     * Determine whether the user can update the youthEducation.
     *
     * @param Youth $youth
     * @param YouthEducation $youthEducation
     * @return bool
     */
    public function update(Youth $youth, YouthEducation $youthEducation): bool
    {
        return $this->isOwner($youth, $youthEducation->youth_id);
    }

    /**
     * Determine whether the user can delete the youthEducation.
     *
     * @param Youth $youth
     * @param YouthEducation $youthEducation
     * @return bool
     */
    public function delete(Youth $youth, YouthEducation $youthEducation): bool
    {
        return $this->isOwner($youth, $youthEducation->youth_id);
    }
}
