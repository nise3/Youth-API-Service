<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
