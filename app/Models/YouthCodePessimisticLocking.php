<?php

namespace App\Models;

/**
 * Class YouthCodePessimisticLocking
 * @property  int last_incremental_value
 */
class YouthCodePessimisticLocking extends BaseModel
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'last_incremental_value';
    protected $guarded = [];

    protected $casts = [
        'last_incremental_value' => 'integer'
    ];
}
