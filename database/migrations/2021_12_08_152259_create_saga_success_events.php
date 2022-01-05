<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSagaSuccessEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saga_success_events', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('connection', 50);
            $table->string('publisher', 100);
            $table->string('listener', 100);
            $table->string('exchange', 100);
            $table->string('routing_key', 100);
            $table->string('consumer', 100);
            $table->text('event_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saga_success_events');
    }
}
