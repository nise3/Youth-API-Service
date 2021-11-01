<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthGuardian
 *
 * @property int id
 * @property int youth_id
 * @property string name
 * @property string|null name_en
 * @property string|null nid
 * @property string|null mobile
 * @property Carbon|null date_of_birth
 * @property int relationship_type
 * @property string|null relationship_title
 * @property string|null relationship_title_en
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class YouthGuardian extends BaseModel
{
    public const  RELATIONSHIP_TYPE_FATHER = 1;
    public const  RELATIONSHIP_TYPE_MOTHER = 2;
    public const  RELATIONSHIP_TYPE_BROTHER = 3;
    public const  RELATIONSHIP_TYPE_SISTER = 4;
    public const  RELATIONSHIP_TYPE_UNCLE = 5;
    public const  RELATIONSHIP_TYPE_AUNT = 6;
    public const  RELATIONSHIP_TYPE_OTHER = 7;

    public const RELATIONSHIP_TYPES = [
        self::RELATIONSHIP_TYPE_FATHER,
        self::RELATIONSHIP_TYPE_MOTHER,
        self::RELATIONSHIP_TYPE_BROTHER,
        self::RELATIONSHIP_TYPE_SISTER,
        self::RELATIONSHIP_TYPE_UNCLE,
        self::RELATIONSHIP_TYPE_AUNT,
        self::RELATIONSHIP_TYPE_OTHER,
    ];

    use SoftDeletes, HasFactory;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $dates = ['mother_date_of_birth', 'father_date_of_birth'];

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
