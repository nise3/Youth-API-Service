<?php

namespace App\Listeners\RplApplication;

use App\Events\RplApplication\RplApplicationRollbackEvent;
use App\Events\RplApplication\RplApplicationSuccessEvent;
use App\Facade\RabbitMQFacade;
use App\Models\BaseModel;
use App\Models\Youth;
use App\Services\RabbitMQService;
use App\Services\YouthManagementServices\YouthService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RplApplicationInstituteToYouthListener implements ShouldQueue
{
    private YouthService $youthService;
    private RabbitMQService $rabbitMQService;

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
                if (!empty($data['youth_id'])) {
                    $youth = Youth::findOrFail($data['youth_id']);

                    Log::info($data);
                    $this->youthService->storeRplApplicationYouthInfo($data, $youth);
                    $this->youthService->updateRplApplicationYouthAddresses($data, $youth);
                    $this->youthService->updateRplApplicationYouthEducations($data, $youth);
                    $this->youthService->updateRplApplicationYouthGuardian($data, $youth);

                    DB::commit();
                    /** Trigger EVENT to Institute Service via RabbitMQ */
                    event(new RplApplicationSuccessEvent($data));


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
                event(new RplApplicationRollbackEvent($data));

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
