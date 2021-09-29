<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->unsignedInteger("examination_id");
            $table->string("institute_name");
            $table->unsignedInteger('board_id')->nullable();
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('result');
            $table->unsignedFloat("cgpa")->nullable();
            $table->date('passing_year');
            $table->unsignedTinyInteger("row_status")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('examination_id')
                ->references('id')
                ->on('examinations')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");


            $table->foreign('board_id')
                ->references('id')
                ->on('boards')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
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
        Schema::dropIfExists('education');
    }
}
