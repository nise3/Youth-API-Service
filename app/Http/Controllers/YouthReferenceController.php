<?php

namespace App\Http\Controllers;

use App\Models\YouthReference;
use App\Services\YouthManagementServices\YouthReferenceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthReferenceController extends Controller
{

    public YouthReferenceService $referenceService;
    private Carbon $startTime;

    public function __construct(YouthReferenceService $referenceService)
    {
        $this->referenceService = $referenceService;
        $this->startTime = Carbon::now();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->referenceService->filterValidator($request)->validate();
        $response = $this->referenceService->getReferenceList($filter, $this->startTime);
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $response = $this->referenceService->getOneReference($id, $this->startTime);
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->filled('youth_id')) {
            $youthId = Auth::id();
            $request->offsetSet('youth_id', $youthId);
        }
        $validated = $this->referenceService->validator($request)->validate();
        $reference = $this->referenceService->store($validated);
        $response = [
            'data' => $reference,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "YouthReference added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $reference = YouthReference::findOrFail($id);
        if (!$request->filled('youth_id')) {
            $youthId = Auth::id();
            $request->offsetSet('youth_id', $youthId);
        }
        $validated = $this->referenceService->validator($request, $id)->validate();
        $reference = $this->referenceService->update($reference, $validated);
        $response = [
            'data' => $reference,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthReference updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $reference = YouthReference::findOrFail($id);
        $this->referenceService->destroy($reference);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthReference deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
