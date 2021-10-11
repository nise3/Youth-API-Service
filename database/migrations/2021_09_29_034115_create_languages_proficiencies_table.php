<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesProficienciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages_proficiencies', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->unsignedTinyInteger("language_id");
            $table->unsignedTinyInteger("reading_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->unsignedTinyInteger("writing_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->unsignedTinyInteger("speaking_proficiency_level")->comment("1=>Fluently,2=>Not Fluently");
            $table->unsignedTinyInteger("understand_proficiency_level")->comment("1=>Easy,2=>Not Easy");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('youth_id')
                ->references('id')
                ->on('youths')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
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
