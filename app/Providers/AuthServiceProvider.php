<?php

namespace App\Providers;

use App\Models\Youth;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    private const _WSO2_KEY = '';

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
            $idpServerId = $this->getIdpServerIdFromToken($token);
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

    //TODO: Shift this method from here to any helper or Service Class
    /**
     * @param $data
     * @param false $verify
     * @return mixed
     * @throws \Throwable
     */
    private function decode($data, bool $verify = false): mixed
    {
        $sections = explode('.', $data);
        throw_if((count($sections) < 3), AuthenticationException::class, 'Invalid number of sections of Tokens (<3)',);
/*
        if (count($sections) < 3) {
            throw new \Exception('Invalid number of sections of Tokens (<3)');
        }
*/

        list($header, $claims, $signature) = $sections;

        $header = json_decode(base64_decode($header));
        $claims = json_decode(base64_decode($claims));

        $signature = json_decode(base64_decode($signature));
        $key = $this->getJwtKey();

        if ($verify === true) {
            throw_if($this->verify($key, $header, $claims, $signature), AuthenticationException::class,'Signature could not be verified');
        }

        return $claims;
    }

    /** TODO: Shift this method from here to any helper or Service Class */
    /**
     * @throws \Throwable
     */
    private function getIdpServerIdFromToken($data, $verify = false)
    {
        $sections = explode('.', $data);

        throw_if((count($sections) < 3), AuthenticationException::class, 'Invalid number of sections of Tokens (<3)',);

        list($header, $claims, $signature) = $sections;

        preg_match("/['\"]sub['\"]:['\"](.*?)['\"][,]/", base64_decode($claims), $matches);

        return count($matches) > 1 ? $matches[1] : "";
    }

    /** TODO: Shift this method from here to any helper or Service Class */
    protected function getJwtKey(): string
    {
        return self::_WSO2_KEY;
    }

    /** TODO: Shift this method from here to any helper or Service Class */
    /**
     * Verify Signature
     */
    protected function verify(): bool
    {
        return true;
    }
}
