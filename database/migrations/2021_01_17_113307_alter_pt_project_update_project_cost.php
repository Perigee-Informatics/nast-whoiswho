<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPtProjectUpdateProjectCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        DB::update('update pt_project set source_local_level_amount = (source_local_level_amount * 1000 )
        where CHAR_LENGTH(source_local_level_amount::VARCHAR) < 5');

        DB::update('update pt_project set source_donar_amount = (source_donar_amount * 1000 )
        where CHAR_LENGTH(source_donar_amount::VARCHAR) < 5 and source_donar_amount > 0');

        DB::update('update pt_project set project_cost = (COALESCE(source_federal_amount,0) + COALESCE(source_local_level_amount,0) + COALESCE(source_donar_amount,0))
        where id in (10618,10617,10647,10619,10132,10266,10341,10357,10105,10214,10242,10241,10342,10131,10493,1817,796)');

        DB::update('update pt_project set source_federal_amount = project_cost where source_federal_amount = 0 and fiscal_year_id in (1,2,3) and project_status_id = 1');

        DB::update('update pt_project set project_cost = (COALESCE(source_federal_amount,0) + COALESCE(source_local_level_amount,0) + COALESCE(source_donar_amount,0))
        where fiscal_year_id in (1,2,3) and project_status_id = 1');
        
        DB::update('update pt_project set project_cost = (COALESCE(source_federal_amount,0) + COALESCE(source_local_level_amount,0) + COALESCE(source_donar_amount,0))
        where id in (10132,10266,10357,10105,10214,10241,10131,10493)');

        DB::update('update pt_project set project_cost = (COALESCE(source_federal_amount,0) + COALESCE(source_local_level_amount,0) + COALESCE(source_donar_amount,0))
        where fiscal_year_id = 2 and project_status_id = 2 and project_cost is null');

        DB::update('update pt_project set project_cost = (COALESCE(source_federal_amount,0) + COALESCE(source_local_level_amount,0) + COALESCE(source_donar_amount,0))
        where project_cost is null and project_status_id in (3,4)');
        
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
