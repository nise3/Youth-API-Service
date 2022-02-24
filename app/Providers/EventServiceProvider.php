<?php

namespace App\Providers;

use App\Events\CourseEnrollment\CourseEnrollmentRollbackEvent;
use App\Events\CourseEnrollment\CourseEnrollmentSuccessEvent;
use App\Events\DbSync\DbSyncSkillUpdateEvent;
use App\Events\MailSendEvent;
use App\Events\SmsSendEvent;
use App\Listeners\CourseEnrollment\CourseEnrollmentRollbackToInstituteListener;
use App\Listeners\CourseEnrollment\CourseEnrollmentSuccessYouthToInstituteListener;
use App\Listeners\DbSync\DbSyncSkillUpdateYouthToTspListener;
use App\Listeners\MailSendListener;
use App\Listeners\SmsSendListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CourseEnrollmentSuccessEvent::class => [
            CourseEnrollmentSuccessYouthToInstituteListener::class
        ],
        CourseEnrollmentRollbackEvent::class => [
            CourseEnrollmentRollbackToInstituteListener::class
        ],
        MailSendEvent::class => [
            MailSendListener::class
        ],
        SmsSendEvent::class => [
            SmsSendListener::class
        ],
        DbSyncSkillUpdateEvent::class => [
            DbSyncSkillUpdateYouthToTspListener::class
        ]
    ];
}
