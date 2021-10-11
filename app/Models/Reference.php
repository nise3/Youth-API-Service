<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Role
 *
 * @property int id
 * @property int youth_id
 * @property string referrer_first_name
 * @property string|null referrer_first_name_en
 * @property string referrer_last_name
 * @property string|null referrer_last_name_en
 * @property string referrer_organization_name
 * @property string|null referrer_organization_name_en
 * @property string referrer_designation
 * @property string|null referrer_designation_en
 * @property string referrer_address
 * @property string|null referrer_address_en
 * @property string referrer_email
 * @property string referrer_mobile
 * @property string referrer_relation
 * @property string|null referrer_relation_en
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Reference extends BaseModel
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
