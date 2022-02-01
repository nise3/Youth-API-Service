<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobLevelExpectedSalaryToYouthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youths', function (Blueprint $table) {
            $table->unsignedInteger("expected_salary")->after('username')->nullable();
            $table->unsignedTinyInteger("job_level")->after('username')->comment('1 => Entry,2 => Mid,3 => Top')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youths', function (Blueprint $table) {
            $table->dropColumn('expected_salary');
            $table->dropColumn('job_level');
        });
    }
}
