<?php

namespace App\Helpers\Classes;

use App\Models\BaseModel;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceToServiceCallHandler
{

    public YouthProfileService $youthProfileService;

    public function __construct(YouthProfileService $youthProfileService)
    {
        $this->youthProfileService = $youthProfileService;
    }

    /**
     * Youth service to organization service call to apply for job
     * @param array $requestData
     * @return mixed
     * @throws RequestException
     */
    public function youthApplyToJob(array $requestData): mixed
    {
        $jobId = $requestData['job_id'];

        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/apply-to-job';
        $postField = [
            "job_id" => $jobId,
            "expected_salary" => $requestData['expected_salary'] ?? null,
            "youth_data" => $this->youthProfileService->getYouthProfile()->toArray()
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])->post($url, $postField)->throw(function ($response, $e) use ($url) {
            Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
            return $e;
        })->json('data');

        Log::info("youth info with job data:" . json_encode($youthData));

        return $youthData;
    }
}
