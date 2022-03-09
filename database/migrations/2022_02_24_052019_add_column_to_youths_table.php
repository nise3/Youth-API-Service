<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToYouthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youths', function (Blueprint $table) {
            $table->json('admin_access_type')->nullable()->comment('1 => Trainer user');
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
            $table->dropColumn('admin_access_type');
        });
    }
}
