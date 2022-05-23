<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectTableChangeForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pt_project_progress', function (Blueprint $table) {
            $table->dropForeign('fk_pt_project_progress_project_id');
            $table->renameColumn('project_id', 'selected_project_id');

            $table->foreign('selected_project_id','fk_pt_project_progress_selected_project_id')->references('id')->on('pt_selected_project');
        });

        DB::statement("SELECT SETVAL('pt_selected_project_id_seq',10660)");
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
