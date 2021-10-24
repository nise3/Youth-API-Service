<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthCertification;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class YouthCertificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any youthCertifications.
     *
     * @param Youth $youth
     * @return bool
     */
    public function viewAny(Youth $youth)
    {
        Log::debug('-----------------------');
        Log::debug($youth);
        return true;
    }

    /**
     * Determine whether the user can view the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return bool
     */
    public function view(Youth $user, YouthCertification $youthCertification)
    {
        return true;
    }

    /**
     * Determine whether the user can create youthCertifications.
     *
     * @param Youth $user
     * @return bool
     */
    public function create(Youth $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return bool
     */
    public function update(Youth $user, YouthCertification $youthCertification)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the youthCertification.
     *
     * @param Youth $user
     * @param YouthCertification $youthCertification
     * @return bool
     */
    public function delete(Youth $user, YouthCertification $youthCertification)
    {
        return true;
    }
}
