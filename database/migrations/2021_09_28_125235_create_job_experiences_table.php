<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_experiences', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("company_name_en", 300);
            $table->string("company_name_bn", 500);
            $table->string("position", 150);
            $table->unsignedInteger("employment_type_id")->comment("1=>Full Time,2=Part Time,3=>Casual,4=>Apprentice or Trainee");
            $table->string("location", 500);
            $table->text("job_description")->nullable();
            $table->date("start_date");
            $table->date("end_date")->nullable();
            $table->unsignedTinyInteger("is_currently_work")->comment("1=>Yes,0=>No")->default(0);
            $table->unsignedTinyInteger("row_status")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('employment_type_id')
                ->references('id')
                ->on('employment_types')
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
        Schema::dropIfExists('job_experiences');
    }
}
