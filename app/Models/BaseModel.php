<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{

    use HasFactory, SoftDeletes;

    public const COMMON_GUARDED_FIELDS_SIMPLE = ['id', 'created_at', 'updated_at'];
    public const COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_NON_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    /** Idp User */
    public const IDP_USERNAME = 'admin';
    public const IDP_USER_PASSWORD = 'admin';

    /** Client Url End Point Type*/
    public const ORGANIZATION_CLIENT_URL_TYPE = "ORGANIZATION";
    public const INSTITUTE_URL_CLIENT_TYPE = "INSTITUTE";
    public const CORE_CLIENT_URL_TYPE = "CORE";
    public const IDP_SERVER_CLIENT_URL_TYPE = "IDP_SERVER";

    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECT = 3;

    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';


    public const YOUTH_USER_TYPE = 4;


    public const TRUE = 1;
    public const FALSE = 0;

    public const FREELANCE_PROFILE_YES = 1;
    public const FREELANCE_PROFILE_NO = 1;

    public const PHYSICAL_DISABILITIES_STATUS = [
        self::TRUE,
        self::FALSE
    ];

    public const FREELANCE_PROFILE_STATUS=[
        self::FREELANCE_PROFILE_YES,
        self::FREELANCE_PROFILE_NO
    ];

    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHERS = 3;
    public const GENDER = [
        self::MALE,
        self::FEMALE,
        self::OTHERS
    ];

    /** LanguagesProficiency Level */
    public const EASY = 1;
    public const NOT_EASY = 0;
    public const FLUENTLY = 1;
    public const NOT_FLUENTLY = 0;

    public const BIRTHDATE_FORMAT = "Y-m-d";

    public const MOBILE_REGEX = 'regex: /^(01[3-9]\d{8})$/';


    /** Password Validation Rules */

    /** Password Regex
     * Regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
     * Means:
     * 1) Should have At least one Uppercase letter.
     * 2) At least one Lower case letter.
     * 3) Also,At least one numeric value.
     * 4) And, At least one special character.
     * 5) Must be more than 8 characters long.
     */

    public const PASSWORD_REGEX = "regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
    public const PASSWORD_TYPE = "string";
    public const PASSWORD_MIN_LENGTH = "min:8";
    public const PASSWORD_MAX_LENGTH = "max:50";

    public const PASSWORD_COMMON_RULES = [
        self::PASSWORD_TYPE,
        self::PASSWORD_MIN_LENGTH,
        self::PASSWORD_MAX_LENGTH,
        self::PASSWORD_REGEX
    ];

    public const USER_NAME_TYPE_EMAIL = 1;
    public const USER_NAME_TYPE_MOBILE_NUMBER = 2;
    public const USER_NAME_TYPE = [
        self::USER_NAME_TYPE_EMAIL,
        self::USER_NAME_TYPE_MOBILE_NUMBER
    ];

    public const CURRENTLY_WORKING = 1;
    public const CURRENTLY_NOT_WORKING = 0;

    public const DEFAULT_PAGE_SIZE = 10;

}
