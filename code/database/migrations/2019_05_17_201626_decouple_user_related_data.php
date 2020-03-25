<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DecoupleUserRelatedData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_user_id_foreign');
            $table->renameColumn('user_id', 'subscriber_id');
            $table->string('subscriber_type', 20)->default('user');
        });
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropForeign('payment_methods_user_id_foreign');
            $table->renameColumn('user_id', 'owner_id');
            $table->string('owner_type', 20)->default('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('subscriber_type');
            $table->renameColumn('subscriber_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('owner_type');
            $table->renameColumn('owner_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
