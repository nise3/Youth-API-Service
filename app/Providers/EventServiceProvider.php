<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\CourseEnrollment\CourseEnrollmentSuccessEvent::class => [
            \App\Listeners\CourseEnrollment\CourseEnrollmentSuccessYouthToInstituteListener::class
        ],
        \App\Events\CourseEnrollment\CourseEnrollmentRollbackEvent::class => [
            \App\Listeners\CourseEnrollment\CourseEnrollmentRollbackToInstituteListener::class
        ],
        \App\Events\MailSendEvent::class => [
            \App\Listeners\MailSendListener::class
        ],
        \App\Events\SmsSendEvent::class => [
            \App\Listeners\SmsSendListener::class
        ],
    ];
}
