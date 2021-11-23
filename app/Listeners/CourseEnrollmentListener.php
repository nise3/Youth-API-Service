<?php

namespace App\Listeners;

use App\Services\YouthManagementServices\YouthService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentListener implements ShouldQueue
{
    public string $connection = 'rabbitmq';
    public YouthService $youthService;

    public function __construct(YouthService $youthService)
    {
        Log::info("pppppppppppppppppppppp course enrollment controller");
        $this->youthService = $youthService;
    }

    public function handle($event)
    {
        $this->youthService->updateYouthProfileAfterCourseEnroll($event);
    }
}
