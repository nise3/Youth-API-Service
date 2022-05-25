<?php

namespace App\Http\Controllers;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Youth;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CommonServices\MailService;
use App\Services\YouthManagementServices\YouthBulkImportForCourseEnrollmentService;
use App\Services\YouthManagementServices\youthBulkImportFromOldSystemService;
use App\Services\YouthManagementServices\YouthProfileService;
use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class  YouthController extends Controller
{
    public YouthService $youthService;
    public YouthBulkImportForCourseEnrollmentService $youthBulkImportForCourseEnrollmentService;
    public Carbon $startTime;


    public function __construct(YouthService $youthService, YouthBulkImportForCourseEnrollmentService $youthBulkImportForCourseEnrollmentService)
    {
        $this->youthService = $youthService;
        $this->youthBulkImportForCourseEnrollmentService = $youthBulkImportForCourseEnrollmentService;
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
        return Response::json($response, ResponseAlias::HTTP_OK);
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
                "code" => ResponseAlias::HTTP_OK,
                "started" => $this->startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
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
     * @return JsonResponse
     */
    /*public function updateYouthAfterCourseEnrollment(Request $request): JsonResponse
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

    }*/

    /**
     * @return JsonResponse
     */
    public function getYouthFeed(): JsonResponse
    {
        $authUser = Auth::user();
        $courses = ServiceToServiceCall::getRecentCoursesForYouthFeed($authUser->id);
        $jobs = ServiceToServiceCall::getRecentJobsForYouthFeed($authUser->id);

        $youthFeeds = array_merge($courses, $jobs);

        $youthFeedCollection = collect($youthFeeds);

        $youthFeedCollectionSorted = $youthFeedCollection->sortByDesc('feed_sort_date')->values();

        $response['data'] = $youthFeedCollectionSorted;
        $response['response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "message" => "Youth feed successfully fetched",
            "query_time" => $this->startTime->diffForHumans(Carbon::now())
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function youthCreateOrUpdateForCourseEnrollment(Request $request): JsonResponse
    {
        /** @var Youth $youth */
        $youth = Youth::where("username", $request->get("mobile"))->first();
        $id = $youth->id ?? null;
        DB::beginTransaction();
        $httpStatusCode = ResponseAlias::HTTP_OK;
        $validated = $this->youthBulkImportForCourseEnrollmentService->youthUpdateValidationForCourseEnrollmentBulkImport($request, $id)->validate();
        $validated['password'] = $this->youthBulkImportForCourseEnrollmentService->getPassword();
        $validated['code'] = CodeGeneratorService::getYouthCode();
        $youthIdpId = null;
        try {
            $idpUserPayLoad = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
                'username' => $validated['mobile'],
                'password' => $validated['password'],
                'user_type' => BaseModel::YOUTH_USER_TYPE,
                'account_disable' => false,
                'account_lock' => false
            ];
            Log::info("IDP" . json_encode($idpUserPayLoad, JSON_PRETTY_PRINT));
            /** Create New IDP User */
            if (empty($id)) {
                $idpResponse = app(YouthProfileService::class)->idpUserCreate($idpUserPayLoad);
                Log::info(json_encode($idpResponse, JSON_PRETTY_PRINT));

                if (!empty($idpResponse['code']) && $idpResponse['code'] == ResponseAlias::HTTP_CONFLICT) {
                    throw new RuntimeException('Idp user already exists', 409);
                }

                if (empty($idpResponse['data']['id'])) {
                    $response = [
                        '_response_status' => [
                            "success" => false,
                            "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                            "message" => "Youth registration is not done in idp",
                            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                        ]
                    ];
                    return Response::json($response, $response['_response_status']['code']);
                }

                $validated['idp_user_id'] = $youthIdpId = $idpResponse['data']['id'];
            }

            $validated['row_status'] = BaseModel::ROW_STATUS_ACTIVE;
            $validated['user_name_type'] = BaseModel::USER_NAME_TYPE_MOBILE_NUMBER;
            $youth = $this->youthBulkImportForCourseEnrollmentService->updateOrCreateYouth($validated);
            $this->youthBulkImportForCourseEnrollmentService->updateYouthAddresses($validated, $youth);
            $this->youthBulkImportForCourseEnrollmentService->updateYouthGuardian($validated, $youth);
            $this->youthBulkImportForCourseEnrollmentService->updateYouthEducations($validated, $youth);
            $this->youthBulkImportForCourseEnrollmentService->updateYouthPhysicalDisabilities($validated, $youth);

            /** Mail send after user registration */
            $to = array($youth->email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Youth Registration Information";
            $message = "Congratulation, You are successfully complete your registration . Username: " . $youth->username . " & Password: " . $validated['password'];
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            $response['data'] = $youth;
            $response['response_status'] = [
                "success" => true,
                "code" => $httpStatusCode,
                "message" => "Youth Successfully Create/Updated",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            if ($youthIdpId) {
                app(YouthProfileService::class)->idpUserDelete($youthIdpId);
            }
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);

    }

    /**
     * @throws Throwable
     */
    public function rollbackYouthById(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $httpStatusCode = ResponseAlias::HTTP_OK;
        try {
            $this->youthBulkImportForCourseEnrollmentService->rollbackYouthById($request->get("username"));
            $response['response_status'] = [
                "success" => true,
                "code" => $httpStatusCode,
                "message" => "Youth feed successfully fetched",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);
    }

}
