<?php

namespace App\Providers;

use App\Facade\AuthTokenUtility;
use App\Models\Youth;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     **/
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('token', function (Request $request) {

            $token = $request->bearerToken();

            if (!$token) {
                return null;
            }

            Log::info('Bearer Token: ' . $token);
            $authUser = null;
            $idpServerUserId = AuthTokenUtility::getIdpServerIdFromToken($token);
            Log::info("Auth idp user id-->" . $idpServerUserId);

            /** @var YouthProfileService $youthService */
            $youthService = $this->app->make(YouthProfileService::class);

            if ($idpServerUserId) {
                $authUser = $youthService->getAuthYouth($idpServerUserId);
                if ($authUser && $authUser->id) {
                    Log::info("Youth Auth User fetched:" . json_encode($authUser));
                } else {
                    Log::info("Youth Auth User Null");
                }
            }

            return $authUser;

        });

    }
}
