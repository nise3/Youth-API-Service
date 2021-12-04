<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;

class SagaEvent extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;

    public const CORE_SERVICE = 'core_service';
    public const INSTITUTE_SERVICE = 'institute_service';
    public const ORGANIZATION_SERVICE = 'organization_service';
    public const YOUTH_SERVICE = 'youth_service';
    public const CMS_SERVICE = 'cms_service';
    public const MAIL_SMS_SERVICE = 'mail_sms_service';
}
