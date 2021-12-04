<?php

namespace App\Listeners;

use App\Models\SagaEvent;
use App\Services\RabbitMQService;
use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class CourseEnrollmentInstituteToYouthListener implements ShouldQueue
{
    public YouthService $youthService;
    public RabbitMQService $rabbitMQService;

    /**
     * @param YouthService $youthService
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(YouthService $youthService, RabbitMQService $rabbitMQService)
    {
        $this->youthService = $youthService;
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @param $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        $alreadyConsumed = $this->rabbitMQService->checkWeatherEventAlreadyConsumed();
        if(!$alreadyConsumed){
            /** Create Saga Payload */
            $uuid = $this->rabbitMQService->getRabbitMqMessageUuid();
            $exchange = $this->rabbitMQService->getRabbitMqMessageExchange();
            $routingKey = $this->rabbitMQService->getRabbitMqMessageRoutingKey();
            $listener = get_class($this);
            $sagaPayload = [
                'uuid' => $uuid,
                'connection' => 'rabbitmq',
                'publisher' => SagaEvent::INSTITUTE_SERVICE,
                'listener' => $listener,
                'exchange' => $exchange,
                'routing_key' => $routingKey,
                'consumer' => SagaEvent::YOUTH_SERVICE,
                'payload' => json_encode($event)
            ];

            $eventData = json_decode(json_encode($event), true);
            $this->youthService->updateYouthProfileAfterCourseEnroll($eventData, $sagaPayload);
        }
    }
}
