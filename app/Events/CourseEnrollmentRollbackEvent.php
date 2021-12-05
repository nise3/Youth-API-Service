<?php

namespace App\Events;

class CourseEnrollmentRollbackEvent
{
    private array $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
