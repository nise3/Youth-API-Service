<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthPhysicalDisabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_physical_disabilities', function (Blueprint $table) {
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('physical_disability_id');

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('physical_disability_id')
                ->references('id')
                ->on('physical_disabilities')
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
        Schema::dropIfExists('youth_physical_disabilities');
    }
}
