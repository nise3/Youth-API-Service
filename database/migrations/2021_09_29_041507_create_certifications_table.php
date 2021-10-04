<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certification', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("certification_name", 300);
            $table->string("certification_name_en", 300)->nullable();
            $table->string("institute_name", 300);
            $table->string("institute_name_en", 300)->nullable();
            $table->string("location", 500)->nullable();
            $table->string("location_en", 500)->nullable();
            $table->date("start_date");
            $table->date("end_date");
            $table->string("certificate_file_path", 500);
            $table->unsignedTinyInteger('row_status')->default(1);
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
        Schema::dropIfExists('certifications');
    }
}
