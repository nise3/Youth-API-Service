<?php

namespace App\Facade;

use http\Encoding\Stream;
use Illuminate\Support\Facades\Facade;

/**
 * Class AuthUser
 * @package App\Facade
 * @method static array|mixed getAuthUserWithRolePermission(string $idpUserId)
 * @method static array|mixed youthApplyToJob(String $jobId)
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
