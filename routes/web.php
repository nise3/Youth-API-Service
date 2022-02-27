<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Router;


$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};

$router->get('/', function () use ($router) {
    Log::info('Info Log');
    Log::debug('debug Log');
    Log::channel('saga')->info('saga Log');
    Log::channel('mail_sms')->info('mail_sms Log');
    Log::channel('idp_user')->info('idp_user Log');
    Log::channel('ek_pay')->info('ek_pay Log');
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

//    /**
//     * Cv download corner
//     */
//    $router->post('youth-cv-download/{id}', ["as" => "youth.cv-download", "uses" => "YouthProfileController@youthCvDownload"]);

    $customRouter()->resourceRoute('languages', 'LanguageController')->render();

    $router->post('auth-youth-info', ["as" => "youth.auth-info", "uses" => "YouthProfileController@getAuthYouthInfoByIdpId"]);

    $router->group(['prefix' => 'service-to-service-call', 'as' => 'service-to-service-call'], function () use ($router) {
        /** bulk youth profile details */
        $router->post("youth-profiles", ["as" => "service-to-service-call.youth-profiles", "uses" => "YouthProfileController@youthProfiles"]);

        /** Get user by username */
        $router->get("user-by-username/{username}", ["as" => "service-to-service-call.user-by-username", "uses" => "YouthProfileController@getByUsername"]);

        /** create or get trainer youth info */
        $router->post("trainer-youth-registration", ["as" => "service-to-service-call.trainer-youth-registration", "uses" => "YouthProfileController@trainerYouthRegistration"]);

        /** rollback trainer youth info */
        $router->post("rollback-trainer-youth-user", ["as" => "service-to-service-call.rollback-trainer-youth-user", "uses" => "YouthProfileController@rollbackTrainerYouthRegistration"]);
    });

    $router->get("nise-statistics", ["as" => "nise-statistics", "uses" => "StatisticsController@niseStatistics"]);

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
    $router->get('youth-my-courses', ["as" => "youth-profile.get-my-courses", "uses" => "YouthProfileController@getYouthEnrollCourses"]);
    $router->put('youth-personal-info-update', ["as" => "youth-profile.update", "uses" => "YouthProfileController@youthProfileInfoUpdate"]);
    $router->put('youth-change-freelance-status', ["as" => "youth-profile.youth-change-freelance-status", "uses" => "YouthProfileController@setFreelanceStatus"]);
    $router->get('youth-feed-statistics', ["as" => "youth-profile.feed-statistics", "uses" => "YouthProfileController@getYouthFeedStatistics"]);
    $router->post('apply-job', ["as" => "youth-profile.youth-apply-to-job", "uses" => "YouthProfileController@youthApplyToJob"]);
    $router->get('my-jobs', ["as" => "youth-profile.youth-my-jobs", "uses" => "YouthProfileController@youthJobs"]);
    $router->put('youth-career-info', ["as" => "youth-career-info-update", "uses" => "YouthProfileController@youthCareerInfoUpdate"]);
    $router->put('youth-set-default-cv-template', ['as' => 'youth-profile.set-default-cv-template', 'uses' => 'YouthProfileController@setDefaultCvTemplate']);

});



