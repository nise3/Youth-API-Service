<?php

namespace App\Listeners;

use App\Services\YouthManagementServices\YouthService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CourseEnrollmentListener implements ShouldQueue
{
    public YouthService $youthService;

    public function __construct(YouthService $youthService)
    {
        $this->youthService = $youthService;
    }

    public function handle($event)
    {
        $this->youthService->updateYouthProfileAfterCourseEnroll($event);
    }
}
