<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 120);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('organization_managers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations');

            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->softDeletes();
            $table->timestamps();
        });
        $organizationAdmin = new \App\Models\Role([
            'name' => 'Organization ADmin',
        ]);
        $organizationAdmin->id = \App\Models\Role::ORGANIZATION_ADMIN;
        $organizationAdmin->save();
        $organizationManager = new \App\Models\Role([
            'name' => 'Organization Manager',
        ]);
        $organizationManager->id = \App\Models\Role::ORGANIZATION_MANAGER;
        $organizationManager->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_managers');
        Schema::dropIfExists('organizations');
    }
}
