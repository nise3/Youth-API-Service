<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Services\YouthManagementServices\CertificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CertificationController extends Controller
{
    public CertificationService $certificationService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;


    /**
     * certificationController constructor.
     * @param CertificationService $certificationService
     */
    public function __construct(CertificationService $certificationService)
    {
        $this->certificationService = $certificationService;
        $this->startTime = Carbon::now();
    }


    public function getList(Request $request)
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
     * Display the specified resource.
     *
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->certificationService->getOneCertification($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
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
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
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
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
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
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
