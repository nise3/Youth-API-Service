<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends Model
{


    public function examDegrees():HasMany
    {
        return $this->hasMany(ExamDegree::class,"education_level_id");
    }
}
