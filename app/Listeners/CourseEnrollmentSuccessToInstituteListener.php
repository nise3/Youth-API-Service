<?php

namespace App\Listeners;

use App\Models\BaseModel;
use App\Services\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class CourseEnrollmentSuccessToInstituteListener implements ShouldQueue
{
    private RabbitMQConnector $connector;
    private RabbitMQService $rabbitmqService;

    /**
     * @throws Exception
     */
    public function __construct(RabbitMQConnector $connector, RabbitMQService $rabbitmqService)
    {
        $this->connector = $connector;
        $this->rabbitmqService = $rabbitmqService;
        $this->publishEvent();
    }

    /**
     * @throws Exception
     * @return void
     */
    private function publishEvent(): void
    {
        $config = config('queue.connections.rabbitmq');
        $queue = $this->connector->connect($config);

        /** Exchange Queue related variables */
        $exchange = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.name');
        $type = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.type');
        $durable = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.durable');
        $autoDelete = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.autoDelete');
        $queueName = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.queue.courseEnrollment.name');
        $binding = config('nise3RabbitMq.exchanges.'.BaseModel::SELF_EXCHANGE.'.queue.courseEnrollment.binding');

        /** Create all common entities in RabbitMQ server If they don't exist */
        $this->rabbitmqService->createRabbitMQCommonEntities($queue);

        /** Create Queue for CourseEnrollment Event */
        $payload = [
            'exchange' => $exchange,
            'queueName' => $queueName,
            'binding' => $binding,
            'durable' => $durable,
            'autoDelete' => $autoDelete
        ];
        $this->rabbitmqService->createQueueAndBindWithoutRetry($queue, $payload);

        /** Set Config to publish the event message */

//        Artisan::call('cache:clear');
//        Artisan::call('php artisan env:set EXCHANGE_ROUTING_KEY aaaaaaaaaaaaaaaaaaaaa');

        config([
            'queue.connections.rabbitmq.options.exchange.name' => $exchange,
            'queue.connections.rabbitmq.options.queue.exchange' => $exchange,
            'queue.connections.rabbitmq.options.exchange.type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);

            Log::info(config('queue.connections.rabbitmq.options.exchange.name'));
            Log::info(config('queue.connections.rabbitmq.options.queue.exchange'));
            Log::info(config('queue.connections.rabbitmq.options.exchange.type'));
            Log::info(config('queue.connections.rabbitmq.options.queue.exchange_type'));
            Log::info(config('queue.connections.rabbitmq.options.queue.exchange_routing_key'));
    }

    public function handle($event)
    {

    }
}
