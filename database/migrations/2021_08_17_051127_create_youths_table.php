<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('youths', function (Blueprint $table) {
            $table->increments('id');
            $table->string("idp_user_id", 100)->nullable();
            $table->unsignedTinyInteger("is_freelance_profile")
                ->default(0)
                ->comment('1 => Yes, 0 => No');

            $table->string("username", 100)->unique()
                ->comment("username is either email or mobile");
            $table->unsignedTinyInteger("user_name_type")
                ->comment("1=>Email Type,2=>Mobile Number");

            $table->string('first_name', 300);
            $table->string('first_name_en', 150)->nullable();
            $table->string('last_name', 300);
            $table->string('last_name_en', 150)->nullable();

            $table->unsignedMediumInteger("loc_division_id")
                ->nullable()->index('youth_division_id_inx');
            $table->unsignedMediumInteger("loc_district_id")
                ->nullable()->index('youth_district_id_inx');

            $table->unsignedMediumInteger("loc_upazila_id")
                ->nullable()->index('youth_upazila_id_inx');

            $table->date('date_of_birth');
            $table->unsignedTinyInteger('gender')
                ->comment('1=>male,2=>female,3=>others');

            $table->unsignedTinyInteger('religion')->nullable()
                ->comment('1 => Islam, 2 => Hinduism, 3 => Christianity, 4 => Buddhism, 5 => Judaism, 6 => Sikhism, 7 => Ethnic, 8 => Agnostic/Atheist');

            $table->unsignedTinyInteger('marital_status')->nullable()
                ->comment('1 => single, 2 => married, 3 => widowed, 4 => divorced');

            $table->unsignedSmallInteger('nationality')->default(1);
            /** Coming from nise3 config file */

            $table->string('email', 255)->nullable();
            $table->string('mobile', 20)->nullable();

            $table->unsignedTinyInteger('identity_number_type')
                ->nullable()->comment('Nid => 1, Birth Certificate => 2, Passport => 3');

            $table->string('identity_number', 100)->nullable();

            $table->unsignedTinyInteger('freedom_fighter_status')
                ->comment('1 => No, 2 => Yes, 3=> child of a freedom fighter, 4 => grand child of a freedom fighter')
                ->default(0);

            $table->unsignedTinyInteger('physical_disability_status')
                ->comment('0=>No, 1=>Yes')
                ->default(0);

            $table->unsignedTinyInteger('does_belong_to_ethnic_group')
                ->comment('0=>No, 1=>Yes')
                ->default(0);

            $table->text("bio")->nullable();
            $table->text("bio_en")->nullable();

            $table->string('photo', 600)->nullable();
            $table->string('cv_path', 600)->nullable();
            $table->string('signature_image_path', 600)->nullable();

            $table->string("verification_code", 10)->nullable()
                ->comment('Email Or SMS verification code');
            $table->dateTime("verification_code_sent_at")->nullable()
                ->comment('Email Or SMS verification code sent at');
            $table->dateTime("verification_code_verified_at")->nullable()
                ->comment('Email Or SMS verification code verified at');

            $table->unsignedTinyInteger("row_status")->default(0)
                ->comment('0=>inactive, 1=>active, 2=>pending, 3=>rejected');

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
