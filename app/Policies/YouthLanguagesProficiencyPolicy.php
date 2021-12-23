<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthLanguagesProficiency;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class YouthLanguagesProficiencyPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the Youth can view any youthLanguagesProficiencies.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth)
    {
        return Response::allow();

    }

    /**
     * Determine whether the Youth can view the youthLanguagesProficiency.
     *
     * @param Youth $youth
     * @param YouthLanguagesProficiency $youthLanguagesProficiency
     * @return Response
     */
    public function view(Youth $youth, YouthLanguagesProficiency $youthLanguagesProficiency)
    {
        return Response::allow();

    }

    /**
     * Determine whether the Youth can create youthLanguagesProficiencies.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth)
    {
        return $this->isUserLoggedIn($youth);

    }

    /**
     * Determine whether the Youth can update the youthLanguagesProficiency.
     *
     * @param Youth $youth
     * @param YouthLanguagesProficiency $youthLanguagesProficiency
     * @return bool
     */
    public function update(Youth $youth, YouthLanguagesProficiency $youthLanguagesProficiency)
    {
        return $this->isOwner($youth, $youthLanguagesProficiency->youth_id);
    }

    /**
     * Determine whether the Youth can delete the youthLanguagesProficiency.
     *
     * @param Youth $youth
     * @param YouthLanguagesProficiency $youthLanguagesProficiency
     * @return bool
     */
    public function delete(Youth $youth, YouthLanguagesProficiency $youthLanguagesProficiency)
    {
        return $this->isOwner($youth, $youthLanguagesProficiency->youth_id);
    }
}
