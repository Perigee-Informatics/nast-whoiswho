<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPtProjectProgressManageFinancialPercent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update(DB::raw('UPDATE pt_project_progress
        SET
            financial_progress_percent = b.value_new
        FROM
            (
                select round(new_value::numeric,2) as value_new,id from
                (
                    select (ptp.financial_progress_amount/pp.source_federal_amount)*100 as new_value,ptp.id 
                    from pt_project_progress ptp
                    inner join pt_project pp on pp.id = ptp.project_id
                    where ptp.financial_progress_percent > 100 and pp.source_federal_amount > 0
                ) x
            ) b
        where 
            pt_project_progress.id = b.id'));
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
