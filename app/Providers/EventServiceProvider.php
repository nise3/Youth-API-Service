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
        \App\Events\CourseEnrollmentSuccessEvent::class => [
            \App\Listeners\CourseEnrollmentSuccessYouthToInstituteListener::class
        ],
        \App\Events\CourseEnrollmentRollbackEvent::class => [
            \App\Listeners\CourseEnrollmentRollbackToInstituteListener::class
        ],
        \App\Events\MailSendEvent::class => [
            \App\Listeners\MailSendListener::class
        ],
        \App\Events\SmsSendEvent::class => [
            \App\Listeners\SmsSendListener::class
        ],
    ];
}
