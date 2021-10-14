<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_references', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("youth_id");
            $table->string("referrer_first_name",300);
            $table->string("referrer_first_name_en",150)->nullable();
            $table->string("referrer_last_name",300);
            $table->string("referrer_last_name_en",150)->nullable();
            $table->string("referrer_organization_name",600);
            $table->string("referrer_organization_name_en",300)->nullable();
            $table->string("referrer_designation",500);
            $table->string("referrer_designation_en",250)->nullable();
            $table->string("referrer_address",1200);
            $table->string("referrer_address_en",600)->nullable();
            $table->string("referrer_email",191);
            $table->string("referrer_mobile",20);
            $table->string("referrer_relation",600);
            $table->string("referrer_relation_en",300)->nullable();

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
        Schema::dropIfExists('youth_references');
    }
}
