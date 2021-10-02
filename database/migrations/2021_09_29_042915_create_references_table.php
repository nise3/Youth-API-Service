<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('references', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("referrer_first_name",150);
            $table->string("referrer_last_name",150);
            $table->string("referrer_organization_name",300);
            $table->string("referrer_designation",200);
            $table->string("referrer_address",600);
            $table->string("referrer_email",191);
            $table->string("referrer_mobile",15);
            $table->string("referrer_relation",300);
            $table->unsignedTinyInteger("row_status")->default(1);
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
        Schema::dropIfExists('references');
    }
}
