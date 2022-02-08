<?php

namespace App\Services\CommonServices;

use App\Events\SmsSendEvent;
use Illuminate\Support\Facades\Log;

class SmsService
{

    /**
     * @param string $recipient
     * @param string $message
     */
    public function sendSms(string $recipient, string $message)
    {
        $smsConfig = [
            "recipient" => $recipient,
            "message" => $message
        ];
        Log::info('SMS Payload'.json_encode($smsConfig));
        event(new SmsSendEvent($smsConfig));
    }

}
