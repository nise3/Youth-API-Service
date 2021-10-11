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
 * @property int has_own_family_home
 * @property int has_own_family_land
 * @property int number_of_siblings
 * @property int recommended_by_any_organization
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthMiscellaneous extends BaseModel
{
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
