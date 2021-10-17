<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected $dates = ['date_of_birth', 'verification_code_sent_at', 'verification_code_verified_at'];

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
    public function youthJobExperiences(): HasMany
    {
        return $this->hasMany(YouthJobExperience::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function youthLanguagesProficiencies(): HasMany
    {
        return $this->hasMany(YouthLanguagesProficiency::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function youthCertifications(): HasMany
    {
        return $this->hasMany(YouthCertification::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function youthEducations(): HasMany
    {
        return $this->hasMany(YouthEducation::class, 'youth_id', 'id');
    }


    /**
     * @return HasMany
     */
    public function youthPortfolios(): HasMany
    {
        return $this->hasMany(YouthPortfolio::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function youthReferences(): HasMany
    {
        return $this->hasMany(YouthReference::class, 'youth_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function youthAddresses(): HasMany
    {
        return $this->hasMany(YouthAddress::class, 'youth_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function youthMiscellaneous(): HasOne
    {
        return $this->hasOne(YouthMiscellaneous::class);
    }

    /**
     * @return HasOne
     */
    public function youthGuardians(): HasOne
    {
        return $this->hasOne(YouthGuardian::class);
    }
}
