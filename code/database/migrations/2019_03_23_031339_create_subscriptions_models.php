<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 40);

            $table->string('duration', 20);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('membership_plan_rates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('membership_plan_id');
            $table->foreign('membership_plan_id')->references('id')->on('membership_plans');

            $table->float('cost');

            $table->boolean('active');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('membership_plan_rate_id');
            $table->foreign('membership_plan_rate_id')->references('id')->on('membership_plan_rates');

            $table->unsignedInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamp('last_renewed_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->boolean('recurring')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
        });
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('membership_plan_rates');
        Schema::dropIfExists('membership_plans');
    }
}
