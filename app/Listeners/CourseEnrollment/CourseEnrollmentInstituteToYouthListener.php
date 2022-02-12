<?php

namespace App\Listeners\CourseEnrollment;

use App\Events\CourseEnrollment\CourseEnrollmentRollbackEvent;
use App\Events\CourseEnrollment\CourseEnrollmentSuccessEvent;
use App\Models\BaseModel;
use App\Models\Youth;
use App\Services\RabbitMQService;
use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class CourseEnrollmentInstituteToYouthListener implements ShouldQueue
{
    public YouthService $youthService;
    public RabbitMQService $rabbitMQService;

    /**
     * @param YouthService $youthService
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(YouthService $youthService, RabbitMQService $rabbitMQService)
    {
        $this->youthService = $youthService;
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @param $event
     * @return void
     * @throws Exception|Throwable
     */
    public function handle($event)
    {
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'] ?? [];
        try {
            $this->rabbitMQService->receiveEventSuccessfully(
                BaseModel::SAGA_INSTITUTE_SERVICE,
                BaseModel::SAGA_YOUTH_SERVICE,
                get_class($this),
                json_encode($event)
            );

            $alreadyConsumed = $this->rabbitMQService->checkEventAlreadyConsumed();
            if (!$alreadyConsumed) {
                DB::beginTransaction();
                if (!empty($data["physical_disabilities"])) {
                    $data["physical_disabilities"] = isset($data['physical_disabilities']) && is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
                }
                if (!empty($data['youth_id'])) {
                    $youth = Youth::findOrFail($data['youth_id']);
                    $youth->fill($data);
                    $youth->save();

                    $this->youthService->updateYouthAddresses($data, $youth);
                    $this->youthService->updateYouthGuardian($data, $youth);
                    $this->youthService->updateYouthEducations($data, $youth);
                    $this->youthService->updateYouthPhysicalDisabilities($data, $youth);

                    DB::commit();

                    /** Trigger EVENT to MailSms Service to send Mail via RabbitMQ */
                    //$this->youthService->sendMailCourseEnrollmentSuccess($data);

                    /** Trigger EVENT to Institute Service via RabbitMQ */
                    event(new CourseEnrollmentSuccessEvent($data));

                    /** Store the event as a Success event into Database */
                    $this->rabbitMQService->sagaSuccessEvent(
                        BaseModel::SAGA_INSTITUTE_SERVICE,
                        BaseModel::SAGA_YOUTH_SERVICE,
                        get_class($this),
                        json_encode($data)
                    );
                } else {
                    throw new Exception("youth_id not provided!");
                }
            }
        } catch (Throwable $e) {
            if ($e instanceof QueryException && $e->getCode() == BaseModel::DATABASE_CONNECTION_ERROR_CODE) {
                /** Technical Recoverable Error Occurred. RETRY mechanism with DLX-DLQ apply now by sending a rejection */
                throw new Exception("Database Connectivity Error");
            } else {
                /** Trigger EVENT to Institute Service via RabbitMQ to Rollback */
                $data['publisher_service'] = BaseModel::SAGA_YOUTH_SERVICE;
                event(new CourseEnrollmentRollbackEvent($data));

                /** Technical Non-recoverable Error "OR" Business Rule violation Error Occurred. Compensating Transactions apply now */
                /** Store the event as an Error event into Database */
                $this->rabbitMQService->sagaErrorEvent(
                    BaseModel::SAGA_INSTITUTE_SERVICE,
                    BaseModel::SAGA_YOUTH_SERVICE,
                    get_class($this),
                    json_encode($data),
                    $e
                );
            }
        }
    }
}
