<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthReference;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class YouthReferencePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any youthReferences.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the youthReference.
     *
     * @param Youth $youth
     * @param YouthReference $youthReference
     * @return Response
     */
    public function view(Youth $youth, YouthReference $youthReference)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthReferences.
     *
     * @param Youth $youth
     * @return mixed
     */
    public function create(Youth $youth)
    {
        return $this->isUserLoggedIn($youth);
    }

    /**
     * Determine whether the user can update the youthReference.
     *
     * @param Youth $youth
     * @param YouthReference $youthReference
     * @return bool
     */
    public function update(Youth $youth, YouthReference $youthReference)
    {
        return $this->isOwner($youth, $youthReference->youth_id);
    }

    /**
     * Determine whether the user can delete the youthReference.
     *
     * @param Youth $youth
     * @param YouthReference $youthReference
     * @return bool
     */
    public function delete(Youth $youth, YouthReference $youthReference)
    {
        return $this->isOwner($youth, $youthReference->youth_id);
    }
}
