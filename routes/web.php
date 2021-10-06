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
    $customRouter()->resourceRoute('youth-portfolios', 'PortfolioController')->render();
    $customRouter()->resourceRoute('youth-skills', 'SkillController')->render();
    $customRouter()->resourceRoute('youth-job-experience', 'JobExperienceController')->render();
    $customRouter()->resourceRoute('youth-references', 'ReferenceController')->render();
    $customRouter()->resourceRoute('youth-languages-proficiencies', 'LanguagesProficiencyController')->render();
    $customRouter()->resourceRoute('youth-certifications', 'CertificationController')->render();
    $customRouter()->resourceRoute('youth-educations', 'EducationController')->render();
    /** youth verification */
    $router->post('youths/{id}/verify', ["as" => "youths.verify", "uses" => "YouthController@youthVerification"]);

    /** youth profile */
    $router->get('youth-profile', ["as"=>"youth-profile.get-profile","uses"=>"YouthProfileController@getYouthProfile", "middleware" => 'auth']);
    $router->post('youth-registration', ["as"=>"youth.registration","uses"=>"YouthProfileController@youthRegistration"]);
    $router->put('youth-personal-info-update', ["as"=>"youth-profile.update","uses"=>"YouthProfileController@youthProfileUpdate", "middleware" => 'auth']);
    $router->post('youth-profile-verification', ["as"=>"youth-profile.verify","uses"=>"YouthProfileController@youthVerification"]);
    $router->post('youth-resend-verification-code', ["as"=>"youth-profile.youth-resend-verify-code","uses"=>"YouthProfileController@resendVerificationCode"]);
    $router->put('youth-change-freelance-status', ["as"=>"youth-profile.youth-change-freelance-status","uses"=>"YouthProfileController@setFreelanceStatus", "middleware" => 'auth']);

});

