<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Youth;
use App\Services\UserRolePermissionManagementServices\UserService;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
//        Log::info('booooooottttt');
//        //dd($this->app['auth']);
//        $this->app['auth']->viaRequest('token', function ($request) {
//            Log::info('reqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq');
//            dd('asdasdasd');
//        });

        $token = Request::capture()->header('Authorization');
        Auth::setUser(new Youth());

        $authUser = null;
        if ($token) {
            $header = explode(" ", $token);
            if (count($header) > 1) {
                if(isset($header[1])){
                    $tokenParts = explode(".", $header[1]);
                    if (count($tokenParts) == 3) {
                        $tokenPayload = base64_decode($tokenParts[1]);
                        $jwtPayload = json_decode($tokenPayload);
                        $youthService = $this->app->make(YouthProfileService::class);
                        $authUser = $youthService->getAuthYouth($jwtPayload->sub ?? null);
                        if($authUser){
                            Auth::setUser($authUser);
                        }
                    }
                    Log::info("userInfoWithIdpId:" . json_encode($authUser));
                }
            }

        }
    }
}
