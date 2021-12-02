<?php

namespace App\Events;

class SmsSendEvent
{
    public array $smsPayload;

    /**
     * Create a new SMS event instance.
     *
     * @return void
     */
    public function __construct(array $smsPayload)
    {
        $this->smsPayload = $smsPayload;
    }
}
