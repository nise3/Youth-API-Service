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
        Schema::create('educations', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("institute_name", 400);
            $table->string("institute_name_en", 400)->nullable();
            $table->unsignedTinyInteger("examination_id")->comment('PSC, JSC, SSC, HSC, Degree, Honours, Masters etc');
            $table->unsignedTinyInteger('board_id')->nullable()->comment('Only for PSC, JSC, SSC, HSC etc');
            $table->unsignedTinyInteger('edu_group_id')->nullable()->comment('1 => Science, 2 => Commerce, 3 => Arts (Only for PSC, JSC, SSC, HSC, Degree)');
            $table->unsignedTinyInteger('major_subject_id')->nullable()->comment('Only for Honours/Masters');
            $table->string('registration_number', 100)->nullable();
            $table->string('roll_number', 100);
            $table->unsignedTinyInteger('result_type')->comment("1 => Division, 2 => Grade point");
            $table->unsignedTinyInteger('division_type_result')->comment("1 => 1st Class, 2 => 2nd Class, 3 => 3rd Class")->nullable();
            $table->float("cgpa_gpa_max_value")->nullable();
            $table->float("received_cgpa_gpa")->nullable();
            $table->year('passing_year');
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
                ->onDelete("SET NULL")
                ->onUpdate("CASCADE");

            $table->foreign('major_subject_id')
                ->references('id')
                ->on('major_or_subjects')
                ->onDelete("SET NULL")
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
