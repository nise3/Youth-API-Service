<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthAddress;
use Illuminate\Auth\Access\Response;

class YouthAddressPolicy extends BasePolicy
{


    /**
     * Determine whether the user can view any youthAddresses.
     *
     * @param Youth $user
     * @return Response
     */
    public function viewAny(Youth $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthAddress.
     *
     * @param Youth $user
     * @param YouthAddress $youthAddress
     * @return Response
     */
    public function view(Youth $user, YouthAddress $youthAddress)
    {

        // Example: return Response::deny('You do not own this post.');
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthAddresses.
     *
     * @param Youth $user
     * @return Response
     */
    public function create(Youth $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the youthAddress.
     *
     * @param Youth $user
     * @param YouthAddress $youthAddress
     * @return Response
     */
    public function update(Youth $user, YouthAddress $youthAddress)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the youthAddress.
     *
     * @param Youth $user
     * @param YouthAddress $youthAddress
     * @return Response
     */
    public function delete(Youth $user, YouthAddress $youthAddress): Response
    {
        return Response::allow();
    }
}
