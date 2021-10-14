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
        try {
            $response = $this->youthService->getYouthProfileList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->youthService->getOneYouthProfile($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * data from examinations, boards , edu groups, major or subjects table
     * @return JsonResponse
     * @throws Throwable
     */

    public function youthEducationBasicInfos(): JsonResponse
    {
        try {
            $info = $this->youthService->getEducationBasicTablesInfos();

            $response['data'] = $info;
            $response['response_status'] = [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "List of educational information",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
