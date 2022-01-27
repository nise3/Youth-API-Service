<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthJobExperienceAreaOfBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_job_experience_area_of_businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->string("youth_id")->index('index_area_bus_job_id');
            $table->integer("area_of_business_id")->index('index_youth_area_busi_area_busi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_experience_area_of_businesses');
    }
}
