<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cusco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_versions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->unsignedInteger('iteration_id');
            $table->foreign('iteration_id')->references('id')->on('iterations');

            $table->string('name', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /** @var \App\Models\Wiki\Article $article */
        foreach (\App\Models\Wiki\Article::all() as $article) {
            /** @var \App\Models\Wiki\Iteration $iteration */
            $iteration = $article->iterations()->limit(1)->get();

            $version = new \App\Models\Wiki\ArticleVersion([
                'iteration_id' => $iteration->id,
                'article_id' => $article->id,
                'name' => '1.0.0',
            ]);
            $version->save();
        }

        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->text('name')->nullable();
            $table->text('caption')->nullable();
            $table->string('url', 120);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('topic', 120)->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('subject_type', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('thread_user', function(Blueprint $table) {
            $table->unsignedInteger('thread_id');
            $table->foreign('thread_id')->references('id')->on('threads');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->primary(['thread_id', 'user_id']);
        });

        Schema::table('messages', function (Blueprint $table) {

            $table->string('email', 120)->nullable()->change();
            $table->string('subject', 256)->nullable()->change();
            $table->string('template', 32)->nullable()->change();
            $table->unsignedInteger('user_id')->nullable()->change();

            $table->json('via')->nullable();
            $table->string('action', 128)->nullable();

            $table->unsignedInteger('from_id')->nullable();
            $table->foreign('from_id')->references('id')->on('users');

            $table->unsignedInteger('thread_id')->nullable();
            $table->foreign('thread_id')->references('id')->on('threads');

            $table->dateTime('seen_at')->nullable();

            $table->renameColumn('user_id', 'to_id');
        });

        Schema::create('resources', function(Blueprint $table) {
            $table->increments('id');

            $table->text('content');

            $table->unsignedInteger('resource_id');
            $table->string('resource_type', 20);

            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('contacts', function(Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('initiated_by_id');
            $table->foreign('initiated_by_id')->references('id')->on('users');

            $table->unsignedInteger('requested_id');
            $table->foreign('requested_id')->references('id')->on('users');

            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('denied_at')->nullable();

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
        Schema::table('messages', function (Blueprint $table) {

            $table->dropForeign('messages_from_id_foreign');
            $table->dropForeign('messages_thread_id_foreign');

            $table->renameColumn('to_id', 'user_id');

            $table->dropColumn('via');
            $table->dropColumn('action');
            $table->dropColumn('seen_at');

            $table->dropColumn('thread_id');
            $table->dropColumn('from_id');
        });
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('article_versions');
        Schema::dropIfExists('thread_user');
        Schema::dropIfExists('threads');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('resources');
    }
}
