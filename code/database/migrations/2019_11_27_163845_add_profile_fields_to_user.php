<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_users_to_add_me')->default(true);
            $table->boolean('receive_push_notifications')->default(true);
            $table->text('about_me')->nullable();
            $table->string('push_notification_key', 512)->nullable();
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
            $table->dropColumn('allow_users_to_add_me');
            $table->dropColumn('about_me');
            $table->dropColumn('receive_push_notifications');
            $table->dropColumn('push_notification_key');
        });
    }
}
