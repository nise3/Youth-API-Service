<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthMiscellaneousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_miscellaneouses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('youth_id');
            $table->unsignedTinyInteger('has_own_family_home');
            $table->unsignedTinyInteger('has_own_family_land');
            $table->unsignedTinyInteger('number_of_siblings');
            $table->unsignedTinyInteger('recommended_by_any_organization')->default(0);
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
        Schema::dropIfExists('youth_miscellaneouses');
    }
}
