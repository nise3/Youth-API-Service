<?php

namespace App\Extend;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Traits\ForwardsCalls;

class MessageBagExtended
{
    use ForwardsCalls;

    protected MessageBag $messageBag;

    public function __construct(MessageBag $messageBag)
    {
        $this->messageBag = $messageBag;
    }

    public function __call($method, $parameters)
    {
        Log::debug("From Extended Message Bag: " . $method);
        return $this->forwardCallTo($this->messageBag, $method, $parameters);
    }
}
