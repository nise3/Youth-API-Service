<?php

namespace App\Providers;

use App\Facade\AuthTokenUtility;
use App\Models\Youth;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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
     * @throws \Throwable
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

//        $token = Request::capture()->header('Authorization');
        $token =  request()->bearerToken();
        Auth::setUser(app(Youth::class));

        if ($token) {
            $idpServerId = AuthTokenUtility::getIdpServerIdFromToken($token);
            Log::info("Auth idp user id-->" . $idpServerId);
            /** @var YouthProfileService $youthService */
            $youthService = $this->app->make(YouthProfileService::class);
            if ($idpServerId) {
                $authUser = $youthService->getAuthYouth($idpServerId);

                if ($authUser) {
                    Log::info("Youth Auth User fetched:" . json_encode($authUser));
                    Auth::setUser($authUser);
                } else {
                    Log::info("Youth Auth User Null");
                }
            }
        }
        Log::info("userInfoWithIdpId:" . json_encode(Auth::user()));
    }


}
