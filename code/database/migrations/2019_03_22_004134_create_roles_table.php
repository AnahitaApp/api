<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 32);
            $table->timestamps();
            $table->softDeletes();
        });

        $roles = [
            ['id' => \App\Models\Role::APP_USER, 'name' => 'A Basic App User'],
            ['id' => \App\Models\Role::SUPER_ADMIN, 'name' => 'A Super Admin'],
            ['id' => \App\Models\Role::ARTICLE_VIEWER, 'name' => 'An Article Viewer'],
            ['id' => \App\Models\Role::ARTICLE_EDITOR, 'name' => 'An Article Editor'],
        ];

        DB::table('roles')->insert($roles);

        Schema::create('role_user', function(Blueprint $table)
        {
            $table->integer('role_id')->unsigned()->index('role_user_role_id_foreign');
            $table->integer('user_id')->unsigned()->index('role_user_user_id_foreign');
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
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
}
