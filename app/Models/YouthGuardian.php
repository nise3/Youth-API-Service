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
 * @property string father_name
 * @property string|null father_name_en
 * @property string|null father_nid
 * @property string|null father_mobile
 * @property Carbon|null father_date_of_birth
 * @property string mother_name
 * @property string mother_name_en
 * @property string mother_nid
 * @property string mother_mobile
 * @property Carbon mother_date_of_birth
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthGuardian extends BaseModel
{
    public const  RELATIONSHIP_TYPE_FATHER = 1;
    public const  RELATIONSHIP_TYPE_MOTHER = 2;
    public const  RELATIONSHIP_TYPE_UNCLE = 3;
    public const  RELATIONSHIP_TYPE_AUNT = 4;
    public const  RELATIONSHIP_TYPE_OTHER = 5;

    public const RELATIONSHIP_TYPES = [
        self::RELATIONSHIP_TYPE_FATHER,
        self::RELATIONSHIP_TYPE_MOTHER,
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
