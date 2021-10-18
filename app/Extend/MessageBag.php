<?php

namespace App\Extend;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag as CoreMessageBag;
use Illuminate\Support\Traits\ForwardsCalls;

class MessageBag
{
    use ForwardsCalls;

    protected CoreMessageBag $messageBag;

    public function __construct(CoreMessageBag $messageBag)
    {
        $this->messageBag = $messageBag;
    }

    public function __call($method, $parameters)
    {
        Log::debug("From Extended Message Bag: " . $method);
        return $this->forwardCallTo($this->messageBag, $method, $parameters);
    }
}
