<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Services\YouthManagementServices\YouthCertificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthCertificationController extends Controller
{
    /**
     * @var YouthCertificationService
     */
    public YouthCertificationService $certificationService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;


    /**
     * YouthCertificationController constructor.
     * @param YouthCertificationService $certificationService
     */
    public function __construct(YouthCertificationService $certificationService)
    {
        $this->certificationService = $certificationService;
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
        $filter = $this->certificationService->filterValidator($request)->validate();
        try {
            $response = $this->certificationService->getAllCertifications($filter, $this->startTime);
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
            $response = $this->certificationService->getOneCertification($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request['youth_id'] = Auth::id();
        $validated = $this->certificationService->validator($request)->validate();
        try {
            $certification = $this->certificationService->store($validated);
            $response = [
                'data' => $certification,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Certification added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request['youth_id'] = Auth::id();
        $certification = Certification::findOrFail($id);
        $validated = $this->certificationService->validator($request, $id)->validate();
        try {
            $certification = $this->certificationService->update($certification, $validated);
            $response = [
                'data' => $certification,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Certificate updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $certification = Certification::findOrFail($id);
        try {
            $this->certificationService->destroy($certification);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Certificate deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}