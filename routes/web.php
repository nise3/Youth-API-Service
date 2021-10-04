<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use Laravel\Lumen\Routing\Router;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);
    $customRouter()->resourceRoute('youths', 'YouthController')->render();
    $customRouter()->resourceRoute('portfolios', 'PortfolioController')->render();
    $customRouter()->resourceRoute('skills', 'SkillController')->render();
    $customRouter()->resourceRoute('job-experience', 'JobExperienceController')->render();

    /** youth profile */
    $router->get('youth-profiles', ["as"=>"youth-profiles.get-profile","uses"=>"YouthProfileController@getYouthProfile"]);
    $router->post('youth-profiles', ["as"=>"youth-profiles.create","uses"=>"YouthProfileController@youthRegister"]);
    $router->post('youth-profiles-update', ["as"=>"youth-profiles.update","uses"=>"YouthProfileController@youthProfileUpdate"]);
    $router->post('youth-profile-verify', ["as"=>"youth-profiles.verify","uses"=>"YouthProfileController@youthVerification"]);
    $router->post('youth-resend-verify-code', ["as"=>"youth-profiles.youth-resend-verify-code","uses"=>"YouthProfileController@resendVerificationCode"]);

});

