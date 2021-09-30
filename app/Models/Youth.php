<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Null_;
use PHPUnit\Util\Json;

/**
 * Class Youth
 * @package App\Models
 * @property int id
 * @property string idp_user_id
 * @property string username
 * @property int user_name_type
 * @property string first_name
 * @property string last_name
 * @property int gender
* @property json skills
 * @property string mobile
 * @property string email
 * @property Date date_of_birth
 * @property int physical_disability_status
 * @property json physical_disabilities
 * @property string | null city
 * @property string | null zip_or_postal_code
 * @property string | null bio
 * @property string | null photo
 * @property string | null cv_path
 * @property string password
 * @property string email_verification_code
 * @property Carbon email_verified_at
 * @property Carbon sms_verification_code
 * @property Carbon sms_verified_at
 * @property Carbon send_verification_code_at
 * @property Carbon row_status
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Youth extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string
     */

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        'skills' => 'array',
        'physical_disabilities' => 'array'
    ];

    protected $hidden = [
        "password",
        "sms_verification_code",
        "email_verification_code"
    ];

    public function setPasswordAttribute($pass)
    {

        $this->attributes['password'] = Hash::make($pass);

    }
}
