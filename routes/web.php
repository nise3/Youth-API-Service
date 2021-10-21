<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use App\Models\ExamDegree;
use Laravel\Lumen\Routing\Router;


$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);
    $customRouter()->resourceRoute('skills', 'SkillController')->render();

    $router->get('youths', ["as" => "youth.get-list", "uses" => "YouthController@getList"]);
    $router->get('youths/{id}', ["as" => "youth.read", "uses" => "YouthController@read"]);
    $router->get('youth-educations-exam-board-edugroup-subject', ["as" => "youth.education.basic.table.info", "uses" => "YouthController@youthEducationBasicInfos"]);

    /** youth registration */
    $router->post('youth-registration', ["as" => "youth.registration", "uses" => "YouthProfileController@youthRegistration"]);

    /** youth verification */
    $router->post('youth-profile-verification', ["as" => "youth-profile.verify", "uses" => "YouthProfileController@youthVerification"]);
    $router->post('youth-resend-verification-code', ["as" => "youth-profile.youth-resend-verify-code", "uses" => "YouthProfileController@resendVerificationCode"]);

    /** Youth update after course enrollment from institute service  */
    $router->post('youth-update-after-course-enrollment', ["as" => "youth-profile.youth-update-after-course-enrollment", "uses" => "YouthController@updateYouthAfterCourseEnrollment"]);

    /** freelance corner */
    //$router->get('freelancers', ["as" => "freelancers.get-all-freelancers", "uses" => "YouthFreelanceController@getAllFreelancers"]);

    /**
     * Cv download corner
     */
    $router->post('youth-cv-download/{id}', ["as" => "youth.cv-download", "uses" => "YouthProfileController@youthCvDownload"]);

    $customRouter()->resourceRoute('languages', 'LanguageController')->render();
});


//youth profile info/update group
$router->group(['prefix' => 'api/v1/', 'as' => 'api.v1', 'middleware' => "auth"], function () use ($router, $customRouter) {
    $customRouter()->resourceRoute('youth-portfolios', 'YouthPortfolioController')->render();
    $customRouter()->resourceRoute('youth-job-experiences', 'YouthJobExperienceController')->render();
    $customRouter()->resourceRoute('youth-references', 'YouthReferenceController')->render();
    $customRouter()->resourceRoute('youth-languages-proficiencies', 'YouthLanguagesProficiencyController')->render();
    $customRouter()->resourceRoute('youth-certifications', 'YouthCertificationController')->render();
    $customRouter()->resourceRoute('youth-educations', 'YouthEducationController')->render();
    $customRouter()->resourceRoute('youth-guardians', 'YouthGuardianController')->render();
    $customRouter()->resourceRoute('youth-addresses', 'YouthAddressController')->render();

    $router->get('youth-profile', ["as" => "youth-profile.get-profile", "uses" => "YouthProfileController@getYouthProfile"]);
    $router->put('youth-personal-info-update', ["as" => "youth-profile.update", "uses" => "YouthProfileController@youthProfileInfoUpdate"]);
    $router->put('youth-change-freelance-status', ["as" => "youth-profile.youth-change-freelance-status", "uses" => "YouthProfileController@setFreelanceStatus"]);

});

$router->get("code", function () {
    return ExamDegree::where("education_level_id", 2)->pluck('id');
});

