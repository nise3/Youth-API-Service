<?php

namespace App\Http\Controllers;

use App\Models\YouthAddress;
use App\Services\YouthManagementServices\YouthAddressService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthAddressController extends Controller
{
    public YouthAddressService $youthAddressService;
    private Carbon $startTime;

    public function __construct(YouthAddressService $youthAddressService)
    {
        $this->youthAddressService = $youthAddressService;
        $this->startTime = Carbon::now();
    }

    public function getList(Request $request): JsonResponse
    {

        $filter = $this->youthAddressService->filterValidator($request)->validate();
        try {
            $returnedData = $this->youthAddressService->getAddressList($filter, $this->startTime);

            $response = [
                'order' => $returnedData['order'],
                'data' => $returnedData['data'],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    'query_time' => $returnedData['query_time']
                ]
            ];

            if (isset($returnedData['total_page'])) {
                $response['total'] = $returnedData['total'];
                $response['current_page'] = $returnedData['current_page'];
                $response['total_page'] = $returnedData['total_page'];
                $response['page_size'] = $returnedData['page_size'];
            }

            return Response::json($response, ResponseAlias::HTTP_OK);

        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->youthAddressService->getOneYouthAddress($id, $this->startTime);
            $response['_response_status'] = [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $response['query_time']
            ];
            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $request['youth_id'] = $request['youth_id'] ?? Auth::id();
        $validated = $this->youthAddressService->validator($request)->validate();
        try {
            $data = $this->youthAddressService->store($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Youth Address Added Successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];

            return Response::json($response, ResponseAlias::HTTP_CREATED);

        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $guardian = YouthAddress::findOrFail($id);
        $request['youth_id'] = Auth::id();
        $validated = $this->youthAddressService->validator($request, $id)->validate();

        try {
            $data = $this->youthAddressService->update($guardian, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Youth Address Updated Successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_CREATED);
        } catch (Throwable $e) {
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $guardian = YouthAddress::findOrFail($id);
        try {
            $this->youthAddressService->destroy($guardian);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Guardian deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }

    }
}
