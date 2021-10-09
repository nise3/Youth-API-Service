<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int | null description
 * @property int row_status
 * @property-read  Collection youths
 */
class PhysicalDisability extends BaseModel
{
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
