<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * Class Youth
 * @package App\Models
 * @property-read  int id
 * @property string name_en
 * @property string name_bn
 * @property string mobile
 * @property string email
 * @property string father_name_en
 * @property string father_name_bn
 * @property string mother_name_en
 * @property string mother_name_bn
 * @property string guardian_name_en
 * @property string guardian_name_bn
 * @property string relation_with_guardian
 * @property int number_of_siblings
 * @property int gender
 * @property Date date_of_birth
 * @property string birth_certificate_no
 * @property string nid
 * @property string passport_number
 * @property string nationality
 * @property int religion
 * @property int marital_status
 * @property int current_employment_status
 * @property string main_occupation
 * @property string other_occupation
 * @property double personal_monthly_income
 * @property int year_of_experience
 * @property int physical_disabilities_status
 * @property int freedom_fighter_status
 * @property int present_address_division_id
 * @property int present_address_district_id
 * @property int present_address_upazila_id
 * @property string present_house_address
 * @property int permanent_address_division_id
 * @property int permanent_address_district_id
 * @property int permanent_address_upazila_id
 * @property string permanent_house_address
 * @property int is_ethnic_group
 * @property string photo
 * @property string signature
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Youth extends BaseModel
{
    use SoftDeletes,HasFactory;
    /**
     * @var string
     */

    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
