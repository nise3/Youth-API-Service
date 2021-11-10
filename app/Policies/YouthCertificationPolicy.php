<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthCertification;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\Response;

class YouthCertificationPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any youthCertifications.
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        // Example: return Response::deny('You do not own this post.');
        Log::debug('-----------------------');
        Log::debug($youth);
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return Response
     */
    public function view(Youth $user, YouthCertification $youthCertification): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthCertifications.
     *
     * @param Youth $user
     * @return bool
     */
    public function create(Youth $user): bool
    {
        return $this->isUserLoggedIn($user);
    }

    /**
     * Determine whether the user can update the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return bool
     */
    public function update(Youth $user, YouthCertification $youthCertification) : bool
    {
        return $this->isOwner($user,$youthCertification->youth_id);
    }

    /**
     * Determine whether the user can delete the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return bool
     */
    public function delete(Youth $user, YouthCertification $youthCertification) : bool
    {
        return $this->isOwner($user,$youthCertification->youth_id);
    }
}
