<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectBamako extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations');

            $table->string('name');

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->string('region')->nullable();
            $table->string('country');

            $table->float('latitude');
            $table->float('longitude');

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
        });
        Schema::table('requested_items', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedInteger('parent_requested_item_id')->nullable();
            $table->foreign('parent_requested_item_id')->references('id')->on('requested_items');
            $table->integer('available_quantity')->nullable();
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign('assets_user_id_foreign');
            $table->renameColumn('user_id', 'owner_id');
            $table->string('owner_type')->default('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign('requests_location_id_foreign');
            $table->dropColumn('location_id');
        });
        Schema::table('requested_items', function (Blueprint $table) {
            $table->dropForeign('requested_items_location_id_foreign');
            $table->dropColumn('location_id');
            $table->dropForeign('requested_items_parent_requested_item_id_foreign');
            $table->dropColumn('parent_requested_item_id');
            $table->dropColumn('available_quantity');
        });
        Schema::dropIfExists('locations');
    }
}
