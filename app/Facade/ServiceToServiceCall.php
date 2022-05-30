<?php

namespace App\Facade;

use http\Encoding\Stream;
use Illuminate\Support\Facades\Facade;

/**
 * Class AuthUser
 * @package App\Facade
 * @method static array|mixed getAuthUserWithRolePermission(string $idpUserId)
 * @method static array|mixed youthApplyToJob(array $requestData)
 * @method static array|mixed youthRespondToJob(array $requestData)
 * @method static array|mixed youthJobs(array $requestData)
 * @method static array|mixed getYouthFeedStatisticsData(int $youthId,string $serviceName)
 * @method static array|mixed getYouthIssuedCertificate(int $youthId, int $issueId)
 * @method static array|mixed getRecentCoursesForYouthFeed(int $youthId)
 * @method static array|mixed getRecentJobsForYouthFeed(int $youthId)
 *
 *
 * @see \App\Helpers\Classes\ServiceToServiceCallHandler
 */
class ServiceToServiceCall extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'service_to_service_call';
    }
}
