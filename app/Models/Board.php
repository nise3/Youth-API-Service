<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends BaseModel
{
    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
