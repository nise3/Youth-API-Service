<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_certifications', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("certification_name", 600);
            $table->string("certification_name_en", 300)->nullable();
            $table->string("institute_name", 600);
            $table->string("institute_name_en", 300)->nullable();
            $table->string("location", 1000)->nullable();
            $table->string("location_en", 500)->nullable();
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
            $table->string("certificate_file_path", 600)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
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
        Schema::dropIfExists('youth_certifications');
    }
}
