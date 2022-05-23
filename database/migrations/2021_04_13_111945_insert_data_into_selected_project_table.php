<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDataIntoSelectedProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 611"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 696"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 853"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 143"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 5"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 963"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 55"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 127"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 161"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 190"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 349"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 367"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 242"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 70"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 357"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 630"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 2434"));
        DB::select(DB::raw("UPDATE pt_project set project_status_id = 2 where id = 10214"));



        DB::select(DB::raw("INSERT INTO pt_selected_project(id,project_id,client_id,code,name_en,name_lc,category_id,sub_category_id,project_cost,source_federal_percent,source_federal_amount,source_local_level_percent,source_local_level_amount,source_donar_percent,
        source_donar_amount,contingency_percent,contingency_amount,project_affected_population,project_affected_ward_count,
        project_affected_wards,gps_lat,gps_long,has_dpr,is_selected,selected_date_bs,selected_date_ad,proposed_start_date_bs,
        proposed_start_date_ad,proposed_duration_year,proposed_duration_months,proposed_end_date_bs,proposed_end_date_ad,
        project_status_id,fiscal_year_id,is_multi_fiscalyear_project,actual_start_date_bs,actual_start_date_ad,
        actual_end_date_bs,actual_end_date_ad,actual_duration_year,actual_duration_months,actual_duration_days,
        executing_entity_type_id,executing_entity_id,description_en,description_lc,remarks,quantity,weightage,unit_type,
        lmbiscode,created_at,updated_at,created_by,updated_by,deleted_at,deleted_by,is_deleted,deleted_uq_code)
        
        SELECT id,id as project_id,client_id,code,name_en,name_lc,category_id,sub_category_id,project_cost,
        source_federal_percent,source_federal_amount,source_local_level_percent,source_local_level_amount,source_donar_percent,source_donar_amount,
        contingency_percent,contingency_amount,project_affected_population,project_affected_ward_count,project_affected_wards,gps_lat,
        gps_long,has_dpr,is_selected,selected_date_bs,selected_date_ad,proposed_start_date_bs,proposed_start_date_ad,proposed_duration_year,
        proposed_duration_months,proposed_end_date_bs,proposed_end_date_ad,project_status_id,fiscal_year_id,is_multi_fiscalyear_project,
        actual_start_date_bs,actual_start_date_ad,actual_end_date_bs,actual_end_date_ad,actual_duration_year,actual_duration_months,
        actual_duration_days,executing_entity_type_id,executing_entity_id,description_en,description_lc,remarks,quantity,weightage,unit_type,
        lmbiscode,created_at,updated_at,created_by,updated_by,deleted_at,deleted_by,is_deleted,deleted_uq_code
        from pt_project where project_status_id > 1"));
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
