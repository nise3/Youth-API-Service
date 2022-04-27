<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthGuardianTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_guardian_temp', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('youth_id');
            $table->string('name', 500);
            $table->string('name_en', 250)->nullable();
            $table->string('nid', 30)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('relationship_type')->comment("1 => Father, 2 => Mother, 3 => Brother, 4 => Sister, 5 => Uncle, 6 => Aunt, 7 => Other");
            $table->string('relationship_title')->nullable();
            $table->string('relationship_title_en')->nullable();

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
