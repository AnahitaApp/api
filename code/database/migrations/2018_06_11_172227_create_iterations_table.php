<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIterationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iterations', function (Blueprint $table) {
            $table->increments('id');

            $table->text('content');

            $table->unsignedInteger('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iterations');
    }
}
