<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * App\Models\PhysicalDisability
 *
 * @property int id
 * @property int row_status
 * @property string code
 * @property string title
 * @property string title_en
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property-read  Collection youths
 */
class PhysicalDisability extends BaseModel
{
    use SoftDeletes, HasFactory;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var string[]
     */
    protected $hidden = [
        "pivot"
    ];

    /**
     * @return BelongsToMany
     */
    public function youths(): BelongsToMany
    {
        return $this->belongsToMany(Youth::class, 'youth_physical_disabilities');
    }
}
