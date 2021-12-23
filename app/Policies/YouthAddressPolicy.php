<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthAddress;
use Illuminate\Auth\Access\Response;

class YouthAddressPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any youthAddresses.
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthAddress.
     * @param Youth $youth
     * @param YouthAddress $youthAddress
     * @return Response
     */
    public function view(Youth $youth, YouthAddress $youthAddress): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthAddresses.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth)
    {
        return $this->isUserLoggedIn($youth);
    }

    /**
     * Determine whether the user can update the youthAddress.
     * @param Youth $youth
     * @param YouthAddress $youthAddress
     * @return bool
     */
    public function update(Youth $youth, YouthAddress $youthAddress)
    {
        return $this->isOwner($youth, $youthAddress->youth_id);
    }

    /**
     * Determine whether the user can delete the youthAddress.
     * @param Youth $youth
     * @param YouthAddress $youthAddress
     * @return bool
     */
    public function delete(Youth $youth, YouthAddress $youthAddress): bool
    {
        return $this->isOwner($youth, $youthAddress->youth_id);
    }
}
