<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Schema has been designed by following bdjobs */

        Schema::create('youth_educations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('youth_id');

            $table->unsignedTinyInteger('education_level_id') /** Available in nise3 config file with key 'education_levels' */
                ->comment('1=> PSC/5 Pass, 2=> JSC/JDC/8 Pass, 3=> Secondary, 4=> Higher Secondary, 5=> Diploma, 6=> Bachelor/Honors, 7=> Masters, 8=> PhD');

            $table->unsignedSmallInteger('exam_degree_id')->nullable();
            $table->string('exam_degree_name', 400)->nullable();
            $table->string('exam_degree_name_en', 200)->nullable();

            $table->string('major_or_concentration', 400)->nullable();
            $table->string('major_or_concentration_en', 400)->nullable();

            $table->unsignedTinyInteger('edu_group_id')->nullable();
            $table->unsignedTinyInteger('edu_board_id')->nullable();

            $table->string("institute_name", 800);
            $table->string("institute_name_en", 400)->nullable();
            $table->unsignedTinyInteger('is_foreign_institute')->default(0)->comment("0=>No,1=>Yes");
            $table->unsignedSmallInteger('foreign_institute_country_id')->nullable();

            $table->unsignedTinyInteger('result') /** available in nise3 config file with key 'exam_degree_results' */
                ->comment('1=> First Division / Class, 2=> Second  Division/Class, 3=> Third Division/Class, 4=> Grade, 5=> Appeared, 6=> Enrolled, 7=> Awarded, 8=> Do Not Mention, 9=> Pass');

            $table->float('marks_in_percentage', 6)
                ->nullable()->comment('Marks in percentage[ highest value 100, lowest value 0]');
            $table->unsignedTinyInteger('cgpa_scale')
                ->nullable()->comment('CGPA Scale (ie 4, 5 etc)');
            $table->float('cgpa', 6)->nullable();

            $table->year('year_of_passing');
            $table->year('expected_year_of_passing')->nullable();
            $table->unsignedTinyInteger('duration')->nullable()->comment('Duration in years');
            $table->string('achievements', 1000)->nullable();
            $table->string('achievements_en', 500)->nullable();

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
        Schema::dropIfExists('youth_education');
    }
}
