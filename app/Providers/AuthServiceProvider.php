<?php

namespace App\Providers;

use App\Models\Youth;
use App\Services\YouthManagementServices\YouthProfileService;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    private CONST _WSO2_KEY = '';
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
            //$header = explode(" ", $token);
            $token = trim(str_replace('Bearer', '', $token));

            //$jwtPayload = $this->decode($token);
            $idpServerId = $this->getIdpServerIdFromToken($token);
            Log::info("Auth idp user id-->".$idpServerId);
            $youthService = $this->app->make(YouthProfileService::class);
            if($idpServerId){
                $authUser = $youthService->getAuthYouth($idpServerId ?? null);
                if($authUser){
                    Auth::setUser($authUser);
                }
            }
            Log::info("userInfoWithIdpId:" . json_encode($authUser));

        }
    }

    private function decode($data, $verify = false)
   {
        $sections = explode('.', $data);
        if (count($sections) < 3) {
            throw new \Exception('Invalid number of sections of Tokens (<3)');
        }

        list($header, $claims, $signature) = $sections;

        $header = json_decode(base64_decode($header));
        $claims = json_decode(base64_decode($claims));

        $signature = json_decode(base64_decode($signature));
        $key =$this->getJwtKey();

        if ($verify === true) {
            if ($this->verify($key, $header, $claims, $signature) === false){
                throw new \Exception ('Signature did not verify');
            }
        }

        return $claims;
    }

    private function getIdpServerIdFromToken($data, $verify = false)
    {
        $sections = explode('.', $data);
        if (count($sections) < 3) {
            throw new \Exception('Invalid number of sections of Tokens (<3)');
        }

        list($header, $claims, $signature) = $sections;
        preg_match("/['\"]sub['\"]:['\"](.*?)['\"][,]/", base64_decode($claims), $matches);

        return count($matches)>1 ? $matches[1] : "";
    }

    protected function getJwtKey()
    {
        return self::_WSO2_KEY;
    }
    protected function verify()
    {
       //do some verification task
    }
}
