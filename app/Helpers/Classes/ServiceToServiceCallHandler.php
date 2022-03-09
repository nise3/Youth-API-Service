<?php

namespace App\Helpers\Classes;

use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use App\Services\YouthManagementServices\YouthProfileService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
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

        $responseData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->post($url, $postField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("youth apply to job data:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * Youth service to organization service call to apply for job
     * @param array $requestData
     * @return mixed
     * @throws RequestException
     */
    public function youthRespondToJob(array $requestData): mixed
    {
        $jobId = $requestData['job_id'];
        $youthId = $requestData['youth_id'];
        $confirmationStatus = $requestData['confirmation_status'];

        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/respond-to-job';
        $postField = [
            "job_id" => $jobId,
            "youth_id" => $youthId,
            "confirmation_status" => $confirmationStatus,
        ];

        $responseData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->post($url, $postField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("youth respond to job data:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * Youth service to organization service call to apply for job
     * @param array $requestData
     * @return mixed
     * @throws RequestException
     */
    public function youthJobs(array $requestData): mixed
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/youth-jobs';

        $responseData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($url, $requestData)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("youth job list data:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param int $youthId
     * @param string $serviceName
     * @return array
     * @throws RequestException
     */
    public function getYouthFeedStatisticsData(int $youthId,string $serviceName): array
    {
        $url = clientUrl($serviceName) . 'service-to-service-call/youth-feed-statistics/' . $youthId;
        $skillIds = DB::table('youth_skills')->where('youth_id', $youthId)->pluck('skill_id')->toArray();
        $skillIds = implode(",", $skillIds);
        $urlWithSkillIds = $url . '?' . "skill_ids=" . $skillIds;
        Log::info($urlWithSkillIds);

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($urlWithSkillIds)
            ->throw(static function (Response $httpResponse, $httpException) use ($urlWithSkillIds) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $urlWithSkillIds . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    /**
     * @param int $youthId
     * @return array
     * @throws RequestException
     */
    public function getRecentCoursesForYouthFeed(int $youthId): array
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'service-to-service-call/youth-feed-courses?youth_id=' . $youthId;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');
    }

    /**
     * @param int $youthId
     * @return array
     * @throws RequestException
     */
    public function getRecentJobsForYouthFeed(int $youthId): array
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/youth-feed-jobs?youth_id=' . $youthId;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($url)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');
    }
}
