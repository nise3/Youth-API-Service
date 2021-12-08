<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFreedomFighterDefaultValueToYouths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youths', function (Blueprint $table) {
            $table->integer('freedom_fighter_status')
                ->comment('1 => No, 2 => Yes, 3=> child of a freedom fighter, 4 => grand child of a freedom fighter')
                ->default(1)
                ->change();
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
            $table->dropColumn('freedom_fighter_status');
        });
    }
}
