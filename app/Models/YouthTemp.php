<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use PHPUnit\Util\Json;

/**
 * Class Youth
 * @package App\Models
 * @property int id
 * @property string idp_user_id
 * @property int is_freelance_profile
 * @property string username
 * @property int user_name_type
 * @property string first_name
 * @property string first_name_en
 * @property string last_name
 * @property string last_name_en
 * @property int identity_number_type
 * @property string identity_number
 * @property int gender
 * @property int nationality
 * @property int religion
 * @property int expected_salary
 * @property int job_level
 * @property string mobile
 * @property string email
 * @property Date date_of_birth
 * @property int physical_disability_status
 * @property json physical_disabilities
 * @property string | null bio
 * @property string | null bio_en
 * @property string | null photo
 * @property string | null cv_path
 * @property string | null default_cv_template
 * @property string password
 * @property string verification_code
 * @property Carbon verification_code_verified_at
 * @property Carbon verification_code_sent_at
 * @property int row_status
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthTemp extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    protected $table = "youth_temp";
}
