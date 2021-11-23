<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class BatchListener  implements ShouldQueue
{
    public string $connection = 'rabbitmq';

    public function __construct($rrrr)
    {

    }

    public function handle($a)
    {
        Log::info("zzzzzzzzzzzzzz Batch Listener");
        Log::info(json_encode($a));
    }
}
