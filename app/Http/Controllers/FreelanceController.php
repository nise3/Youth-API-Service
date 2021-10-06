<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\FreelanceService;
use App\Services\YouthManagementServices\JobExperienceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Throwable;

class FreelanceController extends Controller
{


    /**
     * @var FreelanceService
     */
    public FreelanceService $freelanceService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * FreelanceController constructor.
     * @param FreelanceService $freelanceService
     */
    public function __construct(FreelanceService $freelanceService)
    {
        $this->freelanceService = $freelanceService;
        $this->startTime = Carbon::now();
    }

    public function getAllFreelancers(Request $request)
    {
        $filter = $this->freelanceService->filterValidator($request)->validate();
        try {
            $response = $this->freelanceService->getAllFreelancerList($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

}
