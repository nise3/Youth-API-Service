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


    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];

    const YOUTH_NEARBY_FILTER_TRUE = 1;
    const YOUTH_NEARBY_FILTER_FALSE = 0;

    /** Marital Statuses */
    public const MARITAL_STATUS_SINGLE = 1;
    public const MARITAL_STATUS_MARRIED = 2;
    public const MARITAL_STATUS_WIDOWED = 3;
    public const MARITAL_STATUS_DIVORCED = 4;

    public const MARITAL_STATUSES = [
        self::MARITAL_STATUS_SINGLE,
        self::MARITAL_STATUS_MARRIED,
        self::MARITAL_STATUS_WIDOWED,
        self::MARITAL_STATUS_DIVORCED
    ];

    /** Religions Mapping  */
    public const RELIGION_ISLAM = 1;
    public const RELIGION_HINDUISM = 2;
    public const RELIGION_CHRISTIANITY = 3;
    public const RELIGION_BUDDHISM = 4;
    public const RELIGION_JUDAISM = 5;
    public const RELIGION_SIKHISM = 6;
    public const RELIGION_ETHNIC = 7;
    public const RELIGION_AGNOSTIC_ATHEIST = 8;
    public const RELIGIONS = [
        self::RELIGION_ISLAM,
        self::RELIGION_HINDUISM,
        self::RELIGION_CHRISTIANITY,
        self::RELIGION_BUDDHISM,
        self::RELIGION_JUDAISM,
        self::RELIGION_SIKHISM,
        self::RELIGION_ETHNIC,
        self::RELIGION_AGNOSTIC_ATHEIST
    ];


    /** Freedom fighter statuses */
    public const NON_FREEDOM_FIGHTER = 1;
    public const FREEDOM_FIGHTER = 2;
    public const CHILD_OF_FREEDOM_FIGHTER = 3;
    public const GRAND_CHILD_OF_FREEDOM_FIGHTER = 4;
    public const FREEDOM_FIGHTER_STATUSES = [
        self::NON_FREEDOM_FIGHTER,
        self::FREEDOM_FIGHTER,
        self::CHILD_OF_FREEDOM_FIGHTER,
        self::GRAND_CHILD_OF_FREEDOM_FIGHTER
    ];

    /**  Identity Number Type  */
    public const NID = 1;
    public const BIRTH_CARD = 2;
    public const PASSPORT = 3;
    public const IDENTITY_TYPES = [
        self::NID,
        self::BIRTH_CARD,
        self::PASSPORT
    ];

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
