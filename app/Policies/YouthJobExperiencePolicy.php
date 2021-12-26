<?php

namespace App\Policies;

use App\Models\YouthJobExperience;
use App\Models\Youth;
use Illuminate\Auth\Access\Response;

class YouthJobExperiencePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any youthJobExperiences.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthJobExperience.
     *
     * @param Youth $youth
     * @param YouthJobExperience $youthJobExperience
     * @return Response
     */
    public function view(Youth $youth, YouthJobExperience $youthJobExperience): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthJobExperiences.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth): bool
    {
        return $this->isUserLoggedIn($youth);
    }

    /**
     * Determine whether the user can update the youthJobExperience.
     *
     * @param Youth $youth
     * @param YouthJobExperience $youthJobExperience
     * @return bool
     */
    public function update(Youth $youth, YouthJobExperience $youthJobExperience): bool
    {
        return $this->isOwner($youth, $youthJobExperience->youth_id);
    }

    /**
     * Determine whether the user can delete the youthJobExperience.
     *
     * @param Youth $youth
     * @param YouthJobExperience $youthJobExperience
     * @return bool
     */
    public function delete(Youth $youth, YouthJobExperience $youthJobExperience): bool
    {
        return $this->isOwner($youth, $youthJobExperience->youth_id);
    }
}
