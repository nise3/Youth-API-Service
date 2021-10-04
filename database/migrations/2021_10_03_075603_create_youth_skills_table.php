<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_skills', function (Blueprint $table) {
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('skill_id');

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('skill_id')
                ->references('id')
                ->on('skills')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_skills');
    }
}
