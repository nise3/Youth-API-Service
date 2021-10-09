<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PhysicalDisability extends BaseModel
{

    protected $hidden=[
        "pivot"
    ];

    public function youths(): BelongsToMany
    {
        return $this->belongsToMany(Youth::class, 'youth_physical_disabilities');
    }
}
