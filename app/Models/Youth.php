<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property int gender
 * @property string mobile
 * @property string email
 * @property Date date_of_birth
 * @property int physical_disability_status
 * @property json physical_disabilities
 * @property string | null bio
 * @property string | null bio_en
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
 * @property-read  Collection skills
 */
class Youth extends AuthBaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string
     */

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    protected $hidden = [
        "pivot",
        "verification_code",
        "idp_user_id"
    ];

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'youth_skills');
    }

    /**
     * @return BelongsToMany
     */
    public function physicalDisabilities(): BelongsToMany
    {
        return $this->belongsToMany(PhysicalDisability::class, 'youth_physical_disabilities');
    }

    /**
     * @return HasMany
     */
    public function jobExperiences(): HasMany
    {
        return $this->hasMany(JobExperience::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function LanguagesProficiencies(): HasMany
    {
        return $this->hasMany(LanguagesProficiency::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function references(): HasMany
    {
        return $this->hasMany(Reference::class, 'youth_id', 'id');
    }
}
