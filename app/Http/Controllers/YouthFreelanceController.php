<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\FreelanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthFreelanceController extends Controller
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
     * YouthFreelanceController constructor.
     * @param FreelanceService $freelanceService
     */
    public function __construct(FreelanceService $freelanceService)
    {
        $this->freelanceService = $freelanceService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function getAllFreelancers(Request $request): JsonResponse
    {
        $filter = $this->freelanceService->filterValidator($request)->validate();
        $response = $this->freelanceService->getAllFreelancerList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
