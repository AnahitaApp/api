<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JenguInitialProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('requested_by_id');
            $table->foreign('requested_by_id')->references('id')->on('users');

            $table->unsignedInteger('completed_by_id')->nullable();
            $table->foreign('completed_by_id')->references('id')->on('users');

            $table->float('latitude');
            $table->float('longitude');

            $table->text('description')->nullable();
            $table->text('drop_off_location')->nullable();

            $table->boolean('completed')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('asset_request', function (Blueprint $table) {
            $table->unsignedInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->primary(['asset_id', 'request_id']);
        });
        Schema::create('safety_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->unsignedInteger('reporter_id');
            $table->foreign('reporter_id')->references('id')->on('users');

            $table->text('description');
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('line_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('asset_id')->nullable();
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');

            $table->string('name', 250);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('identification_card_id')->nullable();
            $table->foreign('identification_card_id')->references('id')->on('assets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_identification_card_id_foreign');
            $table->dropColumn('identification_card_id');
        });
        Schema::dropIfExists('line_items');
        Schema::dropIfExists('safety_reports');
        Schema::dropIfExists('asset_request');
        Schema::dropIfExists('requests');
    }
}
