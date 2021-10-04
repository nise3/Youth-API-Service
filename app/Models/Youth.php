<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
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
 * @property string last_name
 * @property int gender
 * @property json skills
 * @property string mobile
 * @property string email
 * @property Date date_of_birth
 * @property int physical_disability_status
 * @property json physical_disabilities
 * @property int loc_division_id
 * @property int loc_district_id
 * @property string | null city_or_town
 * @property string | null zip_or_postal_code
 * @property string | null address
 * @property string | null bio
 * @property string | null photo
 * @property string | null cv_path
 * @property string password
 * @property string verification_code
 * @property Carbon verification_code_verified_at
 * @property Carbon verification_code_sent_at
 * @property int row_status
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


    protected $hidden = [
        "pivot",
        "verification_code",
        "idp_user_id"
    ];


    public function skills():BelongsToMany
    {
        return $this->belongsToMany(Skill::class,'youth_skills');
    }

    public function physicalDisabilities():BelongsToMany
    {
        return $this->belongsToMany(PhysicalDisability::class,'youth_physical_disabilities');
    }

    public function jobExperiences():HasMany
    {
        return $this->hasMany(JobExperience::class,'youth_id','id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class,'youth_id','id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class,'youth_id','id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class,'youth_id','id');
    }

    public function portfolios():HasMany
    {
        return $this->hasMany(Portfolio::class,'youth_id','id');
    }


}
