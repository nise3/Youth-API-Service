<?php

namespace App\Providers;

use App\Facade\AuthTokenUtility;
use App\Models\Youth;
use App\Models\YouthAddress;
use App\Models\YouthCertification;
use App\Models\YouthEducation;
use App\Models\YouthGuardian;
use App\Models\YouthJobExperience;
use App\Policies\YouthAddressPolicy;
use App\Policies\YouthCertificationPolicy;
use App\Policies\YouthEducationPolicy;
use App\Policies\YouthGuardianPolicy;
use App\Policies\YouthJobExperiencePolicy;
use App\Policies\YouthPolicy;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AuthServiceProvider extends ServiceProvider
{
    private array $policies = [
        Youth::class => YouthPolicy::class,
        YouthCertification::class => YouthCertificationPolicy::class,
        YouthJobExperience::class => YouthJobExperiencePolicy::class,
        YouthEducation::class => YouthEducationPolicy::class,
        YouthAddress::class => YouthAddressPolicy::class,
        YouthGuardian::class => YouthGuardianPolicy::class,
    ];

    /**
     * Register any application services.
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

        /** Registering Policies
         * @var string $modelName
         * @var string $policyName
         */
        foreach ($this->policies as $modelName => $policyName) {
            Gate::policy($modelName, $policyName);
        }

        $this->app['auth']->viaRequest('token', function (Request $request) {

            $token = $request->bearerToken();
            Log::info('Bearer Token: ' . $token);

            if (!$token) {
                return null;
            }


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
