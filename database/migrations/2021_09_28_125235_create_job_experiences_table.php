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
            $table->unsignedTinyInteger("employment_type_id");
            $table->string("company_name", 600);
            $table->string("company_name_en", 300)->nullable();
            $table->string("position", 300);
            $table->string("position_en", 150)->nullable();

            $table->string("location", 600);
            $table->string("location_en", 300)->nullable();

            $table->text("job_responsibilities")->nullable();
            $table->text("job_responsibilities_en")->nullable();

            $table->date("start_date");
            $table->date("end_date")->nullable();

            $table->unsignedTinyInteger("is_currently_working")
                ->comment("1=>Yes,0=>No")->default(0);
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
