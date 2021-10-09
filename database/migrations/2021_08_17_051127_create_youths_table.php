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
            $table->string("idp_user_id", 100)->nullable();
            $table->string("is_freelance_profile", 100)
                ->default(0);
            $table->string("username", 100)->unique()
                ->comment("username is either email or mobile");
            $table->unsignedTinyInteger("user_name_type")
                ->comment("1=>Email Type,2=>Mobile Number");

            $table->string('first_name', 500);
            $table->string('first_name_en', 500)->nullable();
            $table->string('last_name', 500);
            $table->string('last_name_en', 500)->nullable();

            $table->unsignedTinyInteger('gender')
                ->comment('1=>male,2=>female,3=>others');

            $table->string('email', 191)->unique();
            $table->string('mobile', 20)->unique();

            $table->date('date_of_birth');

            $table->unsignedTinyInteger('physical_disability_status')
                ->comment('0=>No,1=>Yes')->default(0);

            $table->unsignedMediumInteger("loc_division_id");
            $table->unsignedMediumInteger("loc_district_id");
            $table->unsignedMediumInteger("loc_upazila_id")->nullable();

            $table->string("village_or_area", 500)->nullable();
            $table->string("village_or_area_en", 500)->nullable();
            $table->string("house_n_road", 500)->nullable();
            $table->string("house_n_road_en", 500)->nullable();
            $table->string("zip_or_postal_code", 10)->nullable();

            $table->text("bio")->nullable();
            $table->text("bio_en")->nullable();

            $table->string('photo', 300)->nullable();
            $table->string('cv_path', 300)->nullable();

            $table->string("verification_code", 10)->nullable()
                ->comment('Email Or SMS verification code');
            $table->dateTime("verification_code_sent_at")->nullable()
                ->comment('Email Or SMS verification code sent at');
            $table->dateTime("verification_code_verified_at")->nullable()
                ->comment('Email Or SMS verification code verified at');

            $table->unsignedTinyInteger("row_status")->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('loc_division_id')
                ->references('id')
                ->on('loc_divisions')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('loc_district_id')
                ->references('id')
                ->on('loc_districts')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");
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
