<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthAddress
 *
 * @property int id
 * @property int youth_id
 * @property int address_type
 * @property int loc_division_id
 * @property int loc_district_id
 * @property int loc_upazila_id
 * @property string|null village_n_area
 * @property string|null village_or_area_en
 * @property string|null house_n_road
 * @property string|null house_n_road_en
 * @property string zip_or_postal_code
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthAddress extends BaseModel
{
    public const ADDRESS_TYPE_PRESENT = 1;
    public const ADDRESS_TYPE_PERMANENT = 2;
    public const ADDRESS_TYPE_OTHERS = 3;

    public const ADDRESS_TYPES = [
        self::ADDRESS_TYPE_PRESENT,
        self::ADDRESS_TYPE_PERMANENT,
        self::ADDRESS_TYPE_OTHERS,
    ];

    use SoftDeletes, HasFactory;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
