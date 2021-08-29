<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youths', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en',191);
            $table->string('name_bn',500);
            $table->string('mobile',20)->unique();
            $table->string('email',191)->unique();
            $table->string('father_name_en',191)->nullable();
            $table->string('father_name_bn',500)->nullable();
            $table->string('mother_name_en',191)->nullable();
            $table->string('mother_name_bn',500)->nullable();
            $table->string('guardian_name_en',191)->nullable();
            $table->string('guardian_name_bn',500)->nullable();
            $table->string('relation_with_guardian',150)->nullable();
            $table->unsignedTinyInteger('number_of_siblings')->nullable();
            $table->unsignedTinyInteger('gender')->comment('1=>male,2=>female,3=>others')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('birth_certificate_no',191)->nullable();
            $table->string('nid',25)->nullable();
            $table->string('passport_number',50)->nullable();
            $table->string('nationality',200);
            $table->unsignedTinyInteger('religion')->comment('1=>islam,2=>hindu,3=>buddhist,4=>Christians,5=>others')->nullable();
            $table->unsignedTinyInteger('marital_status')->comment('0=>unmarried,1=>married')->default(0);
            $table->unsignedTinyInteger('current_employment_status')->comment('1=>Yes,0=>No')->default(0);
            $table->string('main_occupation',255)->nullable();
            $table->string('other_occupation',255)->nullable();
            $table->unsignedDouble('personal_monthly_income')->nullable();
            $table->unsignedFloat('year_of_experience')->nullable();
            $table->unsignedTinyInteger('physical_disabilities_status')->comment('0=>No,1=>Yes')->default(0);
            $table->unsignedTinyInteger('freedom_fighter_status')->comment('1=>Yes,0=>No')->default(0);
            $table->unsignedMediumInteger('present_address_division_id')->nullable();
            $table->unsignedMediumInteger('present_address_district_id')->nullable();
            $table->unsignedMediumInteger('present_address_upazila_id')->nullable();
            $table->text('present_house_address')->nullable();
            $table->unsignedMediumInteger('permanent_address_division_id')->nullable();
            $table->unsignedMediumInteger('permanent_address_district_id')->nullable();
            $table->unsignedMediumInteger('permanent_address_upazila_id')->nullable();
            $table->text('permanent_house_address')->nullable();
            $table->unsignedTinyInteger('is_ethnic_group')->comment('1=>Yes,0=>No')->default(0);
            $table->string('photo',500)->nullable();
            $table->text('signature')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youths');
    }
}
