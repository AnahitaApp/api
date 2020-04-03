<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments');

            $table->unsignedInteger('item_id')->nullable();
            $table->string('item_type', 20);

            $table->float('amount');

            $table->softDeletes();
            $table->timestamps();
        });
        foreach (\App\Models\Payment\Payment::all() as $payment) {
            $model = new \App\Models\Payment\LineItem();
            $model->item_id = $payment->subscription_id;
            $model->item_type = 'subscription';
            $model->payment_id = $payment->id;
            $subscription = \App\Models\Subscription\Subscription::whereId($payment->subscription_id)->first();
            $model->amount = $subscription ? $subscription->membershipPlanRate->cost : 0;
            $model->save();
            // Add any other needed data migration here
        }
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_subscription_id_foreign');
            $table->dropColumn('subscription_id');
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
            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
        Schema::dropIfExists('purchased_items');
    }
}
