<?php

use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\DateSettingSeeder;
use Illuminate\Database\Schema\Blueprint;
use Database\Seeders\DataCoreSeederFromTmpp;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableAddClientId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('photo')->nullable();
            $table->string('mobile_no',10)->nullable();
            $table->unsignedSmallInteger('client_id')->nullable();

            $table->foreign('client_id','fk_users_client_id')->references('id')->on('app_client');
        });

        $role_seeder= new RolesSeeder();
        $role_seeder->run();

        $data_core_seeder= new DataCoreSeederFromTmpp();
        $data_core_seeder->run();

        $date_setting_seeder= new DateSettingSeeder();
        $date_setting_seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
