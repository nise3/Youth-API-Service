<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;

class SagaEvent extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
