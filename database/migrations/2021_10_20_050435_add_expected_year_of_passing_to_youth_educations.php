<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddExpectedYearOfPassingToYouthEducations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youth_educations', function (Blueprint $table) {
            $table->year("expected_year_of_passing")->nullable();
            DB::statement('ALTER TABLE youth_educations MODIFY year_of_passing YEAR NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youth_educations', function (Blueprint $table) {
            $table->dropColumn('expected_year_of_passing');
        });
    }
}
