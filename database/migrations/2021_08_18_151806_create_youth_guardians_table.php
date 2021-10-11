<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthGuardiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_guardians', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('youth_id');
            $table->string('father_name', 500);
            $table->string('father_name_en', 250)->nullable();
            $table->string('father_nid', 30)->nullable();
            $table->string('father_mobile', 20)->nullable();
            $table->date('father_date_of_birth')->nullable();

            $table->string('mother_name', 500);
            $table->string('mother_name_en', 250)->nullable();
            $table->string('mother_nid', 30)->nullable();
            $table->string('mother_mobile', 20)->nullable();
            $table->date('mother_date_of_birth')->nullable();

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
        Schema::dropIfExists('youth_guardians');
    }
}
