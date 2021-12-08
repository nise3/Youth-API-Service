<?php

namespace App\Listeners;

use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentListener implements ShouldQueue
{
    public YouthService $youthService;


    /**
     * @param YouthService $youthService
     */
    public function __construct(YouthService $youthService)
    {
        $this->youthService = $youthService;
    }

    /**
     * @param $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        $payload = json_decode(json_encode($event), true);
        $this->youthService->updateYouthProfileAfterCourseEnroll($payload);
    }
}
