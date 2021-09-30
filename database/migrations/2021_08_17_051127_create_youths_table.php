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
            $table->string("idp_user_id")->nullable();
            $table->string("username")->unique()->comment("username is either email or mobile");
            $table->unsignedTinyInteger("user_name_type")->comment("1=>Email Type,2=>Mobile Number");
            $table->string('first_name',191);
            $table->string('last_name',500);
            $table->tinyInteger('gender')->comment('1=>male,2=>female,3=>others');
            $table->text("skills")->comment('[skill1,skill2........upto 10 skills]');
            $table->string('email',191)->unique();
            $table->string('mobile',20)->unique();
            $table->date('date_of_birth');
            $table->tinyInteger('physical_disability_status')->comment('0=>No,1=>Yes')->default(0);
            $table->text('physical_disabilities')->comment("[disability1,disability2.......disabilityn]")->nullable();
            $table->string("city")->nullable();
            $table->string("zip_or_postal_code")->nullable();
            $table->text("bio")->nullable();
            $table->string("password")->comment("Alpha-numeric character = 1LookUpTheWordAlphanumeric");
            $table->string('photo',255)->nullable();
            $table->string('cv_path',255)->nullable();
            $table->string("email_verification_code")->nullable();
            $table->dateTime("email_verified_at")->nullable();
            $table->string("sms_verification_code")->nullable();
            $table->dateTime("sms_verified_at")->nullable();
            $table->unsignedTinyInteger("row_status")->default(0);
            $table->timestamps();
            $table->softDeletes();
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
