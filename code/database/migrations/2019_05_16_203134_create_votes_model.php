<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ballots', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 128)->nullable();
            $table->string('type');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('ballot_subjects', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_id');
            $table->foreign('ballot_id')->references('id')->on('ballots');

            $table->unsignedInteger('subject_id');
            $table->string('subject_type');

            $table->integer('votes_cast')->default(0);
            $table->integer('vote_count')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('ballot_completions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_id');
            $table->foreign('ballot_id')->references('id')->on('ballots');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_subject_id');
            $table->foreign('ballot_subject_id')->references('id')->on('ballot_subjects');

            $table->unsignedInteger('ballot_completion_id');
            $table->foreign('ballot_completion_id')->references('id')->on('ballot_completions');

            $table->integer('result')->default(0);

            $table->softDeletes();
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
        Schema::dropIfExists('votes');
        Schema::dropIfExists('ballot_completions');
        Schema::dropIfExists('ballot_subjects');
        Schema::dropIfExists('ballots');
    }
}
