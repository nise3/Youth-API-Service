<?php

namespace App\Policies;

use App\Models\Youth;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class YouthPolicy  extends BasePolicy
{

    /**
     * Determine whether the user can view any youths.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return Response
     */
    public function view(Youth $user, Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youths.
     *
     * @param Youth $user
     * @return Response
     */
    public function create(Youth $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return Response
     */
    public function update(Youth $user, Youth $youth): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return Response
     */
    public function delete(Youth $user, Youth $youth): Response
    {
        return Response::allow();
    }
}
