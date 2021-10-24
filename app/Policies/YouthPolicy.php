<?php

namespace App\Policies;

use App\Models\Youth;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class YouthPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any youths.
     *
     * @param Youth $youth
     * @return bool
     */
    public function viewAny(Youth $youth)
    {
        Log::debug('-----------------------');
        Log::debug($youth);
    }

    /**
     * Determine whether the user can view the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return bool
     */
    public function view(Youth $user, Youth $youth)
    {
        return true;
    }

    /**
     * Determine whether the user can create youths.
     *
     * @param Youth $user
     * @return bool
     */
    public function create(Youth $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return bool
     */
    public function update(Youth $user, Youth $youth)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the youth.
     *
     * @param Youth $user
     * @param Youth $youth
     * @return bool
     */
    public function delete(Youth $user, Youth $youth)
    {
        return true;
    }
}
