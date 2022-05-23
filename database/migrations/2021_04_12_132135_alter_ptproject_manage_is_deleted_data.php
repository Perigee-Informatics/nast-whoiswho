<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPtprojectManageIsDeletedData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select(DB::raw('update pt_project set 
        deleted_uq_code = 1,
        is_deleted = null,
        deleted_by = null,
        deleted_at = null
        where id in (10922,10927,10941)'));
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
