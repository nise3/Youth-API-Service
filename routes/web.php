<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use Laravel\Lumen\Routing\Router;


$router->get('/', function () {
    return "Call api youth success";
});


$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);
    $customRouter()->resourceRoute('skills', 'SkillController')->render();
    $customRouter()->resourceRoute('youths', 'YouthController')->render();
    $customRouter()->resourceRoute('youth-portfolios', 'PortfolioController')->render();
    $customRouter()->resourceRoute('youth-job-experience', 'JobExperienceController')->render();
    $customRouter()->resourceRoute('youth-references', 'ReferenceController')->render();
    $customRouter()->resourceRoute('youth-languages-proficiencies', 'LanguagesProficiencyController')->render();
    $customRouter()->resourceRoute('youth-certifications', 'CertificationController')->render();
    $customRouter()->resourceRoute('youth-educations', 'EducationController')->render();

    /** youth registration */
    $router->post('youth-registration', ["as" => "youth.registration", "uses" => "YouthProfileController@youthRegistration"]);

    /** youth profile */
    $router->get('youth-profile', ["as" => "youth-profile.get-profile", "uses" => "YouthProfileController@getYouthProfile", "middleware" => 'auth']);
    $router->put('youth-personal-info-update', ["as" => "youth-profile.update", "uses" => "YouthProfileController@youthProfileUpdate", "middleware" => 'auth']);
    $router->put('youth-change-freelance-status', ["as" => "youth-profile.youth-change-freelance-status", "uses" => "YouthProfileController@setFreelanceStatus", "middleware" => 'auth']);

    /** youth verification */
    $router->post('youth-profile-verification', ["as" => "youth-profile.verify", "uses" => "YouthProfileController@youthVerification"]);
    $router->post('youth-resend-verification-code', ["as" => "youth-profile.youth-resend-verify-code", "uses" => "YouthProfileController@resendVerificationCode"]);

    /** freelance corner */
    $router->get('freelancers', ["as" => "freelancers.get-all-freelancers", "uses" => "FreelanceController@getAllFreelancers"]);

    /** Youth Job Experience */
//    $router->get('youth-job-experience-read[/{id}]', [
//        "as" => "youth-job-experience.read",
//        "uses" => "JobExperienceController@read"
//    ]);


});

