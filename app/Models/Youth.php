<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Psy\Util\Json;

/**
 * Class Youth
 * @package App\Models
 * @property int id
 * @property string idp_user_id
 * @property string username
 * @property int user_name_type
 * @property string first_name
 * @property string first_name_en
 * @property string last_name
 * @property string last_name_en
 * @property int gender
 * @property string mobile
 * @property string email
 * @property Date date_of_birth
 * @property int physical_disability_status
 * @property Json physical_disabilities
 * @property int loc_division_id
 * @property int loc_district_id
 * @property string | null village_or_area
 * @property string | null village_or_area_en
 * @property string | null zip_or_postal_code
 * @property string | null house_n_road
 * @property string | null house_n_road_en
 * @property string | null bio
 * @property string | null bio_en
 * @property string | null photo
 * @property string | null cv_path
 * @property string password
 * @property string verification_code
 * @property Carbon verification_code_sent_at
 * @property Carbon verification_code_verified_at
 * @property Carbon row_status
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Youth extends AuthBaseModel
{
    use SoftDeletes, HasFactory;
    /**
     * @var string
     */

    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        'physical_disabilities' => 'array'
    ];

    protected $hidden = [
        "password",
        "verification_code"
    ];

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class,'youth_id','id');
    }

}
