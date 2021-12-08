<?php

namespace App\Http\Controllers;

use App\Events\CourseEnrollmentSuccessEvent;
use App\Events\MailSendEvent;
use App\Models\YouthCertification;
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
        $this->authorize('viewAny', YouthCertification::class);

        $filter = $this->certificationService->filterValidator($request)->validate();
        $returnedData = $this->certificationService->getAllCertifications($filter, $this->startTime);

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

    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $certification = $this->certificationService->getOneCertification($id);
        $response = [
            "data" => $certification ?: [],
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
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
        $this->authorize('create', YouthCertification::class);

        $validated = $this->certificationService->validator($request)->validate();
        $certification = $this->certificationService->store($validated);

        /** Trigger event to RabbitMQ */
        event(new CourseEnrollmentSuccessEvent($validated));
        event(new MailSendEvent($validated));

        $response = [
            'data' => $certification,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "YouthCertification added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
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
        if (!$request->filled('youth_id')) {
            $youthId = Auth::id();
            $request->offsetSet('youth_id', $youthId);
        }

        $certification = YouthCertification::findOrFail($id);

        $this->authorize('update', $certification);

        $validated = $this->certificationService->validator($request, $id)->validate();

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
        return Response::json($response, ResponseAlias::HTTP_CREATED);

    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        // TODO: Check Policy so that an youth can not delete other youth's data
        $certification = YouthCertification::findOrFail($id);
        $this->certificationService->destroy($certification);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
}
