<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthJobExperienceAreaOfExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_job_experience_area_of_experiences', function (Blueprint $table) {
            $table->increments('id');
            $table->string("youth_id")->index('index_area_bus_job_id');
            $table->string("youth_job_experience_id")->index('indx_area_bus_exp_id');
            $table->integer("area_of_experience_id")->index('index_youth_area_exp_area_busi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_job_experience_area_of_experiences');
    }
}
