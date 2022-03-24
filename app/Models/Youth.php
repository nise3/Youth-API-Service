<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
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
 * @property-read  Collection skills
 */
class Youth extends AuthBaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use SoftDeletes, HasFactory, Authenticatable, Authorizable;

    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];

    const YOUTH_NEARBY_FILTER_TRUE = 1;
    const YOUTH_NEARBY_FILTER_FALSE = 0;
    public const YOUTH_CODE_PREFIX = "Y";
    public const YOUTH_CODE_SIZE = "17";

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

    public const JOB_LEVEL_ENTRY = 1;
    public const JOB_LEVEL_MID = 2;
    public const JOB_LEVEL_TOP = 3;

    /** Youth Job Levels  */
    public const JOB_LEVELS = [
        self::JOB_LEVEL_ENTRY,
        self::JOB_LEVEL_MID,
        self::JOB_LEVEL_TOP,
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

    /** Profile Complete Fields */
    public const PROFILE_COMPLETE_FIELDS = ['email', 'mobile', 'identity_number', 'youthLanguagesProficiencies', 'youthPortfolios', 'youthEducations'];

    /**
     * @var string
     */

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $dates = ['date_of_birth', 'verification_code_sent_at', 'verification_code_verified_at'];

    // "idp_user_id" is removed from hidden array when implementing "Trainer User Profile". This "idp_user_id" needed to store in Core service "users" table
    protected $hidden = [
        "pivot",
        "verification_code",
//        "idp_user_id"
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
        return $this->hasMany(YouthLanguagesProficiency::class, 'youth_id', 'id')
            ->leftJoin('languages', 'languages.id', '=', 'youth_languages_proficiencies.language_id')
            ->select(['youth_languages_proficiencies.*',
                'languages.title as language_title',
                'languages.title_en as language_title_en'
            ]);
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
        return $this->hasMany(YouthEducation::class, 'youth_id', 'id')
            ->leftJoin('exam_degrees', 'exam_degrees.id', '=', 'youth_educations.exam_degree_id')
            ->leftJoin('edu_groups', 'edu_groups.id', '=', 'youth_educations.edu_group_id')
            ->leftJoin('edu_boards', 'edu_boards.id', '=', 'youth_educations.edu_board_id')
            ->leftJoin('education_levels', 'education_levels.id', '=', 'youth_educations.education_level_id')
            ->select(['youth_educations.*',
                'exam_degrees.title as exam_degree_title',
                'exam_degrees.title_en as exam_degree_title_en',
                'edu_groups.title as edu_group_title',
                'edu_groups.title_en as edu_group_title_en',
                'edu_boards.title as edu_board_title',
                'edu_boards.title_en as edu_board_title_en',
                'education_levels.title as education_level_title',
                'education_levels.title_en as education_level_title_en',
            ]);
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
        return $this->hasMany(YouthAddress::class, 'youth_id', 'id')
            ->leftJoin('loc_divisions', 'loc_divisions.id', '=', 'youth_addresses.loc_division_id')
            ->leftJoin('loc_districts', 'loc_districts.id', '=', 'youth_addresses.loc_district_id')
            ->leftJoin('loc_upazilas', 'loc_upazilas.id', '=', 'youth_addresses.loc_upazila_id')
            ->select(['youth_addresses.*',
                'loc_divisions.title as loc_division_title',
                'loc_divisions.title_en as loc_division_title_en',
                'loc_districts.title as loc_district_title',
                'loc_districts.title_en as loc_district_title_en',
                'loc_upazilas.title as loc_upazila_title',
                'loc_upazilas.title_en as loc_upazila_tile_en']);
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
