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
    $customRouter()->resourceRoute('references', 'ReferenceController')->render();
    $customRouter()->resourceRoute('languages', 'LanguageController')->render();
    $customRouter()->resourceRoute('certifications', 'CertificationController')->render();
    $customRouter()->resourceRoute('educations', 'EducationController')->render();
    /** youth verification */
    $router->post('youths/{id}/verify', ["as"=>"youths.verify","uses"=>"YouthController@youthVerification"]);

    /** youth profile */
    $router->get('youth-profile', ["as"=>"youth-profile.get-profile","uses"=>"YouthProfileController@getYouthProfile"]);
    $router->post('youth-registration', ["as"=>"youth.registration","uses"=>"YouthProfileController@youthRegistration"]);
    $router->put('youth-personal-info-update', ["as"=>"youth-profile.update","uses"=>"YouthProfileController@youthProfileUpdate"]);
    $router->post('youth-profile-verification', ["as"=>"youth-profile.verify","uses"=>"YouthProfileController@youthVerification"]);
    $router->post('youth-resend-verification-code', ["as"=>"youth-profile.youth-resend-verify-code","uses"=>"YouthProfileController@resendVerificationCode"]);
    $router->post('youth-change-freelance-status', ["as"=>"youth-profile.youth-set-freelance-status","uses"=>"YouthProfileController@setFreelanceStatus"]);

});

