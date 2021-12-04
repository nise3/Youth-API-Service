<?php

namespace App\Listeners;

use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Consumer;
use VladimirYuldashev\LaravelQueueRabbitMQ\Helpers\RabbitMQ;

class CourseEnrollmentListener implements ShouldQueue
{

    public YouthService $youthService;


    /**
     * @param YouthService $youthService
     */
    public function __construct(YouthService $youthService)
    {
        $this->youthService = $youthService;
    }

    /**
     * @param $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        throw;
        $rabbitMq = app(RabbitMQ::class);
        $rabbitMqJob = $rabbitMq->getRabbitMqJob();
        $message = $rabbitMqJob->getRabbitMQMessage();
        Log::info(json_encode($message));
        Log::info("--------++++");
        $payload = json_decode(json_encode($event), true);
        $this->youthService->updateYouthProfileAfterCourseEnroll($payload);
    }
}
