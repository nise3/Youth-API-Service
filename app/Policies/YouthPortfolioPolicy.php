<?php

namespace App\Policies;

use App\Models\Youth;
use App\Models\YouthPortfolio;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class YouthPortfolioPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any youthPortfolios.
     *
     * @param Youth $youth
     * @return Response
     */
    public function viewAny(Youth $youth)
    {
        return Response::allow();

    }

    /**
     * Determine whether the user can view the youthPortfolio.
     *
     * @param Youth $youth
     * @param YouthPortfolio $youthPortfolio
     * @return Response
     */
    public function view(Youth $youth, YouthPortfolio $youthPortfolio)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create youthPortfolios.
     *
     * @param Youth $youth
     * @return bool
     */
    public function create(Youth $youth)
    {
        return $this->isUserLoggedIn($youth);

    }

    /**
     * Determine whether the user can update the youthPortfolio.
     *
     * @param Youth $youth
     * @param YouthPortfolio $youthPortfolio
     * @return bool
     */
    public function update(Youth $youth, YouthPortfolio $youthPortfolio)
    {
        return $this->isOwner($youth, $youthPortfolio->youth_id);

    }

    /**
     * Determine whether the user can delete the youthPortfolio.
     *
     * @param Youth $youth
     * @param YouthPortfolio $youthPortfolio
     * @return bool
     */
    public function delete(Youth $youth, YouthPortfolio $youthPortfolio)
    {
        return $this->isOwner($youth, $youthPortfolio->youth_id);

    }
}
