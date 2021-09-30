<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @property-read int id
 */
abstract class AuthBaseModel extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use HasFactory, Authenticatable, Authorizable;
}
