<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->unsignedInteger("language_info_id");
            $table->unsignedTinyInteger("reading_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->unsignedTinyInteger("writing_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->unsignedTinyInteger("speaking_proficiency_level")->comment("1=>Fluently,2=>Not Fluently");
            $table->unsignedTinyInteger("understand_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->unsignedTinyInteger("row_status")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('language_info_id')
                ->references('id')
                ->on('language_infos')
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
        Schema::dropIfExists('languages');
    }
}
