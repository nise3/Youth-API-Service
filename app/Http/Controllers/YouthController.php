<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\YouthService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class  YouthController extends Controller
{
    public YouthService $youthService;
    public Carbon $startTime;


    public function __construct(YouthService $youthService)
    {
        $this->youthService = $youthService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {

        $filter = $this->youthService->filterValidator($request)->validate();
        $response = $this->youthService->getYouthProfileList($filter, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $youth = $this->youthService->getOneYouthProfile($id);

        $response = [
            "data" => $youth,
            "_response_status" => [
                "success" => true,
                "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                "started" => $this->startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];

        return Response::json($response);
    }


    /**
     * data from examinations, boards , edu groups, major or subjects table
     * @return JsonResponse
     * @throws Throwable
     */

    public function youthEducationBasicInfos(): JsonResponse
    {
        $info = $this->youthService->getEducationBasicTablesInfos();

        $response['data'] = $info;
        $response['response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "message" => "List of Education Related Information",
            "query_time" => $this->startTime->diffForHumans(Carbon::now())
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     */
    public function updateYouthAfterCourseEnrollment(Request $request)
    {
        $status = $this->youthService->updateYouthProfileAfterCourseEnroll($request);

        $code = $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
        $response['response_status'] = [
            "success" => $status,
            "code" => $code,
            "message" => $status ? "Successfully update youth after enrollment" : "Failed to update youth after enrollment",
            "query_time" => $this->startTime->diffForHumans(Carbon::now())
        ];

        return Response::json($response, $code);

    }

}
