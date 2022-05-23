<?php
namespace App\Base\Traits;

use App\Models\AppSetting;
use App\Models\MstFiscalYear;
use App\Models\MstFedDistrict;
use App\Models\MstFedLocalLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


/**
 * To get combo filed from model
 */
trait PullData
{

    public function pullData()
    {
        $second_connection = DB::connection('pgsql_second');
        $this->clean_tables();
        $this->resetSequence();
        $mst_designation = $second_connection->table('mst_designation')->get();
        $mst_note_type = $second_connection->table('mst_note_type')->get();
        $mst_fed_local_level = $second_connection->table('mst_fed_local_level')->get();
        $mst_fiscal_year = $second_connection->table('mst_fiscal_year')->get();
        $mst_fed_district = $second_connection->table('mst_fed_district')->get();
        $mst_project_sub_category = $second_connection->table('mst_project_sub_category')->get();
        $mst_road_connectivity_type = $second_connection->table('mst_road_connectivity_type')->get();
        $cms_users = $second_connection->table('cms_users')->get();
        $mst_executing_entity = $second_connection->table('mst_executing_entity')->get();
        $mst_tmpp_related_staff = $second_connection->table('mst_tmpp_related_staff')->get();
        $pt_project = $second_connection->table('pt_project')->get();
        $pt_project_files = $second_connection->table('pt_project_files')->get();
        $pt_project_notes = $second_connection->table('pt_project_notes')->get();
        $pt_project_progress = $second_connection->table('pt_project_progress')->whereNotIn('project_id',[32,33,34,162,130,258,468,10153])->get();


        DB::beginTransaction();
            try {

                foreach($mst_designation as $designation){
                    $data = [
                        'id' => $designation->id,
                        'created_at' => $designation->created_at    ,
                        'updated_at' => $designation->updated_at,
                        'code' => $designation->code,
                        'name_en' => $designation->name_en,
                        'name_lc' => $designation->name_lc,
                        'remarks' => $designation->remarks,
                        'created_by' => $designation->created_by,
                        'updated_by' => $designation->updated_by,
                    ];
                    DB::table('mst_designation')->insert($data);
                }
        
        
                foreach($mst_note_type as $note_type){
                    $data = [
                        'id' => $note_type->id,
                        'created_at' => $note_type->created_at    ,
                        'updated_at' => $note_type->updated_at,
                        'code' => $note_type->code,
                        'name_en' => $note_type->name_en,
                        'name_lc' => $note_type->name_lc,
                        'remarks' => $note_type->remarks,
                        'created_by' => $note_type->created_by,
                        'updated_by' => $note_type->updated_by,
                    ];
                    DB::table('mst_note_type')->insert($data);
                }
        
        
        
                foreach($mst_project_sub_category as $project_sub_category){
                    $data = [
                        'id' => $project_sub_category->id,
                        'created_at' => $project_sub_category->created_at,
                        'updated_at' => $project_sub_category->updated_at,
                        'project_category_id' => $project_sub_category->project_category_id,
                        'code' => $project_sub_category->code,
                        'name_en' => $project_sub_category->name_en,
                        'name_lc' => $project_sub_category->name_lc,
                        'remarks' => $project_sub_category->remarks,
                        'created_by' => $project_sub_category->created_by,
                        'updated_by' => $project_sub_category->updated_by,
                    ];
                    DB::table('mst_project_sub_category')->insert($data);
                }
        
                foreach($mst_road_connectivity_type as $road_connectivity_type){
                    $data = [
                        'id' => $road_connectivity_type->id,
                        'created_at' => $road_connectivity_type->created_at,
                        'updated_at' => $road_connectivity_type->updated_at,
                        'code' => $road_connectivity_type->code,
                        'name_en' => $road_connectivity_type->name_en,
                        'name_lc' => $road_connectivity_type->name_lc,
                        'remarks' => $road_connectivity_type->remarks,
                        'created_by' => $road_connectivity_type->created_by,
                        'updated_by' => $road_connectivity_type->updated_by,
                    ];
                    DB::table('mst_road_connectivity_type')->insert($data);
                }
        
        
        
                foreach($mst_fiscal_year as $fiscal_year){
                    $data = [
                        'id' => $fiscal_year->id,
                        'created_at' => $fiscal_year->created_at,
                        'updated_at' => $fiscal_year->updated_at,
                        'code' => $fiscal_year->code,
                        'from_date_bs' => $fiscal_year->from_date_bs,
                        'from_date_ad' => $fiscal_year->from_date_ad,
                        'to_date_bs' => $fiscal_year->to_date_bs,
                        'to_date_ad' => $fiscal_year->to_date_ad,
                        'remarks' => $fiscal_year->remarks,
                        'created_by' => $fiscal_year->created_by,
                        'updated_by' => $fiscal_year->updated_by,
                        'is_current' => $fiscal_year->is_current,
                    ];
                    DB::table('mst_fiscal_year')->insert($data);
                }
        
        
        
                foreach($mst_fed_district as $fed_district){
                    $data = [
                        'id' => $fed_district->id,
                        'province_id' => $fed_district->province_id,
                        'code' => $fed_district->code,
                        'name_en' => $fed_district->name_en,
                        'name_lc' => $fed_district->name_lc,
                        'created_at' => $fed_district->created_at,
                        'updated_at' => $fed_district->updated_at,
                        'remarks' => $fed_district->remarks,
                        'created_by' => $fed_district->created_by,
                        'updated_by' => $fed_district->updated_by,
                    ];
                    DB::table('mst_fed_district')->insert($data);
                }
        
        
        
        
                foreach($mst_fed_local_level as $fed_local_level){
                    $data = [
                        'id' => $fed_local_level->id,
                        'district_id' => $fed_local_level->district_id,
                        'code' => $fed_local_level->code,
                        'name_en' => $fed_local_level->name_en,
                        'name_lc' => $fed_local_level->name_lc,
                        'level_type_id' => $fed_local_level->level_type_id,
                        'wards_count' => $fed_local_level->wards_count,
                        'is_tmpp_applicable' => $fed_local_level->is_tmpp_applicable,
                        'remarks' => $fed_local_level->remarks,
                        'gps_lat' => $fed_local_level->gps_lat,
                        'gps_long' => $fed_local_level->gps_long,
                        'created_at' => $fed_local_level->created_at,
                        'updated_at' => $fed_local_level->updated_at,
                        'created_by' => $fed_local_level->created_by,
                        'updated_by' => $fed_local_level->updated_by,
                        'lmbiscode' => $fed_local_level->lmbiscode,
                    ];
                    DB::table('mst_fed_local_level')->insert($data);
        
                }
        
        
        
                foreach($mst_fed_local_level as $app_client_seed){
                    $data = [
                        'id' => $app_client_seed->id,
                        'code' => $app_client_seed->code,
                        'name_en' => $app_client_seed->name_en, 
                        'name_lc' => $app_client_seed->name_lc,
                        'fed_local_level_id' => $app_client_seed->id,
                        'is_tmpp_applicable' => $app_client_seed->is_tmpp_applicable,
                        'lmbiscode' => $app_client_seed->lmbiscode,
                    ];
                    DB::table('app_client')->insert($data);
                    AppSetting::create(['fiscal_year_id'=>3,'client_id'=>$app_client_seed->id]);
                }
        
        
        
                foreach($cms_users as $users){
                    $data = [
                        'id' => $users->id,
                        'name' => $users->name,
                        'photo' => $users->photo,
                        'email' => $users->email,
                        'password' => $users->password,
                        'created_at' => $users->created_at,
                        'updated_at' => $users->updated_at,
                        'is_active' => $users->status === 'Active' ? true: false,
                        'mobile_no' => $users->mobile_no,
                        'client_id' => $users->client_id,
                    ];
                    DB::table('users')->insert($data);
                    DB::table('model_has_roles')->insert([
                        'role_id' => $users->id_cms_privileges,
                        'model_type' => 'App\Models\BackpackUser',
                        'model_id' => $users->id,
                    ]);
                }
        
        
        
                foreach($mst_executing_entity as $entity){
                    $data = [
                        'id' => $entity->id,
                        'client_id' => $entity->client_id,
                        'entity_type_id' => $entity->entity_type_id,
                        'code' => $entity->code,
                        'name_en' => $entity->name_en,
                        'name_lc' => $entity->name_lc,
                        'created_at' => $entity->created_at,
                        'updated_at' => $entity->updated_at,
                        'add_province_id' => $entity->add_province_id,
                        'add_district_id' => $entity->add_district_id,
                        'add_local_level_id' => $entity->add_local_level_id,
                        'add_ward_no' => $entity->add_ward_no,
                        'add_tole_name' => $entity->add_tole_name,
                        'add_house_number' => $entity->add_house_number,
                        'contact_person' => $entity->contact_person,
                        'contact_person_designation' => $entity->contact_person_designation,
                        'contact_person_phone' => $entity->contact_person_phone,
                        'contact_person_mobile' => $entity->contact_person_mobile,
                        'contact_person_email' => $entity->contact_person_email,
                        'uc_registration_number' => $entity->uc_registration_number,
                        'company_registration_number' => $entity->company_registration_number,
                        'remarks' => $entity->remarks,
                        'created_by' => $entity->created_by,
                        'updated_by' => $entity->updated_by,
                    ];
                    DB::table('mst_executing_entity')->insert($data);
                }
        
        
        
                foreach($mst_tmpp_related_staff as $tmpp_staff){
                    $data = [
                        'id' => $tmpp_staff->id,
                        'client_id' => $tmpp_staff->client_id,
                        'entity_type_id' => $tmpp_staff->entity_type_id,
                        'name_en' => $tmpp_staff->name_en,
                        'name_lc' => $tmpp_staff->name_lc,
                        'created_at' => $tmpp_staff->created_at,
                        'updated_at' => $tmpp_staff->updated_at,
                        'created_by' => $tmpp_staff->created_by,
                        'updated_by' => $tmpp_staff->updated_by,
                        'designation_id' => $tmpp_staff->designation_id,
                        'is_active' => $tmpp_staff->is_active,
                        'remarks' => $tmpp_staff->remarks,
                    ];
                    DB::table('mst_tmpp_related_staff')->insert($data);
                }
        
        
        
        
        
                foreach($pt_project as $projects){

                    switch($projects->project_status_id){
                        case "1":
                            $project_status_id = 1;
                            break;
                        case "3":
                            $project_status_id = 2;
                            break;
                        case "4":
                            $project_status_id = 5;
                            break;
                        case "5":
                        case "6":
                        case "7":
                        case "8":
                            $project_status_id = 3;
                            break;
                        case "9":
                            $project_status_id = 4;
                            break;        
                    }
                    $data = [
                        'id' => $projects->id,
                        'client_id' => $projects->client_id,
                        'code' => $projects->code,
                        'name_en' => $projects->name_en,
                        'name_lc' => $projects->name_lc,
                        'created_at' => $projects->created_at,
                        'updated_at' => $projects->updated_at,
                        'description_en' => $projects->description_en,
                        'description_lc' => $projects->description_lc,
                        'category_id' => $projects->category_id,
                        'sub_category_id' => $projects->sub_category_id,
                        'project_cost' => $projects->project_cost,
                        'source_federal_percent' => $projects->source_federal_percent,
                        'source_federal_amount' => $projects->source_federal_amount,
                        'source_local_level_percent' => $projects->source_local_level_percent,
                        'source_local_level_amount' => $projects->source_local_level_amount,
                        'source_donar_percent' => $projects->source_donar_percent,
                        'source_donar_amount' => $projects->source_donar_amount,
                        'contingency_percent' => $projects->contingency_percent,
                        'contingency_amount' => $projects->contingency_amount,
                        'project_affected_population' => $projects->project_affected_population,
                        'project_affected_ward_count' => $projects->project_affected_ward_count,
                        'project_affected_wards' => $projects->project_affected_wards,
                        'gps_lat' => $projects->gps_lat,
                        'gps_long' => $projects->gps_long,
                        'is_selected' => $projects->is_selected,
                        'selected_date_bs' => $projects->selected_date_bs,
                        'selected_date_ad' => $projects->selected_date_ad,
                        'proposed_start_date_bs' => $projects->proposed_start_date_bs,
                        'proposed_start_date_ad' => $projects->proposed_start_date_ad,
                        'proposed_duration_year' => $projects->proposed_duration_year,
                        'proposed_duration_months' => $projects->proposed_duration_months,
                        'proposed_end_date_bs' => $projects->proposed_end_date_bs,
                        'proposed_end_date_ad' => $projects->proposed_end_date_ad,
                        'project_status_id' => $project_status_id,
                        'fiscal_year_id' => $projects->fiscal_year_id,
                        'is_multi_fiscalyear_project' => $projects->is_multi_fiscalyear_project,
                        'actual_start_date_bs' => $projects->actual_start_date_bs,
                        'actual_start_date_ad' => $projects->actual_start_date_ad,
                        'actual_end_date_bs' => $projects->actual_end_date_bs,
                        'actual_end_date_ad' => $projects->actual_end_date_ad,
                        'actual_duration_year' => $projects->actual_duration_year,
                        'actual_duration_months' => $projects->actual_duration_months,
                        'actual_duration_days' => $projects->actual_duration_days,
                        'executing_entity_type_id' => $projects->executing_entity_type_id,
                        'executing_entity_id' => $projects->executing_entity_id,
                        'remarks' => $projects->remarks,
                        'quantity' => $projects->quantity,
                        'weightage' => $projects->weightage,
                        'unit_type' => $projects->unit_type,
                        'lmbiscode' => $projects->lmbiscode,
                    ];
                    DB::table('pt_project')->insert($data);

                    $app_setting_data = [
                        'incharge_name' => $projects->incharge_name,
                        'incharge_designation' => $projects->incharge_designation,
                        'incharge_phone' => $projects->incharge_phone,
                        'incharge_mobile' => $projects->incharge_mobile,
                        'incharge_email' => $projects->incharge_email,
                    ];

                    AppSetting::where('client_id',$projects->client_id)->update($app_setting_data);
                }
        
        
        
                foreach($pt_project_files as $project_files){
                    $file_path = NULL;
                    if($project_files->path != null){
                        $file_path = substr($project_files->path,8);
                    }
                    $data = [
                        'id' => $project_files->id,
                        'client_id' => $project_files->client_id,
                        'project_id' => $project_files->project_id,
                        'name_en' => $project_files->name_en,
                        'name_lc' => $project_files->name_lc,
                        'created_at' => $project_files->created_at,
                        'updated_at' => $project_files->updated_at,
                        'path' => $file_path,
                        'remarks' => $project_files->remarks,
                    ];
                    DB::table('pt_project_files')->insert($data);
                }
        
        
        
        
                foreach($pt_project_notes as $project_note){
                    $data = [
                        'id' => $project_note->id,
                        'client_id' => $project_note->client_id,
                        'project_id' => $project_note->project_id,
                        'note_type_id' => $project_note->note_type_id,
                        'date_bs' => $project_note->date_bs,
                        'date_ad' => $project_note->date_ad,
                        'created_at' => $project_note->created_at,
                        'updated_at' => $project_note->updated_at,
                        'created_by' => $project_note->created_by,
                        'updated_by' => $project_note->updated_by,
                        'note' => $project_note->note,
                    ];
                    DB::table('pt_project_notes')->insert($data);
                }
        
        
        
        
                foreach($pt_project_progress as $project_progress){
                    switch($project_progress->reporting_interval_id){
                        case "1":
                            if($project_progress->month_id === null || $project_progress->month_id === 4 
                            || $project_progress->month_id === 1 || $project_progress->month_id === 2 
                            || $project_progress->month_id === 3  || $project_progress->month_id === 5  || $project_progress->month_id === 6
                            || $project_progress->month_id === 7 || $project_progress->month_id === 9 || $project_progress->month_id === 10
                            || $project_progress->month_id === 12){
                                $reporting_interval_id = 3;
                            }elseif($project_progress->month_id === 8){
                                $reporting_interval_id = 1;
                            }elseif($project_progress->month_id === 11){
                                $reporting_interval_id = 2;
                            }
                            break;
                        case "2":
                            if($project_progress->month_id === 7){
                                $reporting_interval_id = 1;
                            }else{
                                $reporting_interval_id = 2;
                            }
                            break;
                        case "3":
                            $reporting_interval_id = 3;
                            break;
                        case "4":
                            $reporting_interval_id = 4;
                            break;
                    }
                    $data = [
                        'id' => $project_progress->id,
                        'client_id' => $project_progress->client_id,
                        'project_id' => $project_progress->project_id,
                        'reporting_interval_id' => $reporting_interval_id,
                        'date_bs' => $project_progress->date_bs,
                        'date_ad' => $project_progress->date_ad,
                        'created_at' => $project_progress->created_at,
                        'updated_at' => $project_progress->updated_at,
                        'created_by' => $project_progress->created_by,
                        'updated_by' => $project_progress->updated_by,
                        'financial_progress_percent' => $project_progress->financial_progress_percent,
                        'financial_progress_amount' => $project_progress->financial_progress_amount,
                        'physical_progress_percent' => $project_progress->physical_progress_percent,
                        'prepared_by' => $project_progress->prepared_by,
                        'submitted_by' => $project_progress->submitted_by,
                        'submitted_by_designation_id' => $project_progress->submitted_by_designation_id,
                        'approved_by' => $project_progress->approved_by,
                        'approved_by_designation_id' => $project_progress->approved_by_designation_id,
                        'physical_progress_amount' => $project_progress->physical_progress_amount,
                        'quantity' => $project_progress->quantity,
                        'weightage' => $project_progress->weightage,
                        'unit_type' => $project_progress->unit_type,
                        'fiscal_year_id' => $project_progress->fiscal_year_id,
                    ];
                    DB::table('pt_project_progress')->insert($data);
                }
        

                /// For Update is tmpp applicable true
                $is_tmpp_applicable = DB::select('update mst_fed_local_level set is_tmpp_applicable = false');

                $is_tmpp_applicable_client = DB::select('update app_client set is_tmpp_applicable = false where id <> 1000');

                $is_tmpp_applicable_true = DB::select(DB::raw('update mst_fed_local_level 
                set is_tmpp_applicable = true where id in (
                select distinct(client_id)
                from pt_project )'));
                
                $is_tmpp_applicable_true_client = DB::select(DB::raw('update app_client 
                set is_tmpp_applicable = true where id in (
                select distinct(client_id)
                from pt_project )'));

                ///Delete from users table when is_tmpp_applicable == false

                $users_delete = DB::select(DB::raw('
                delete from users where id in(
                select id from users where client_id not in (
                select id from app_client where is_tmpp_applicable = true
                ))'));

                AppSetting::create(['fiscal_year_id'=>3,'client_id'=>1000,'allow_new_project_demand'=>true]);

                $appsetting_delete = DB::select(DB::raw('
                                    delete from app_setting where client_id in(
                                    select id from app_client where is_tmpp_applicable = false
                                    )'));

                /// Delete from app_client where is_tmpp_applicable = false
                $app_client_delete = DB::select(DB::raw('delete from app_client where is_tmpp_applicable = false'));
                
                /// update client id = 1000 into null value
                $client_id = DB::select('update users set client_id = 1000 where client_id is null');

                ///update executing entity id
                $update_executing_entity_id = DB::select(DB::raw('UPDATE pt_project_progress ppp
                SET executing_entity_type_id = pp.executing_entity_type_id
                FROM pt_project pp
                WHERE pp.id = ppp.project_id'));

                DB::select(DB::raw('UPDATE pt_project_progress set executing_entity_type_id = 3 where executing_entity_type_id is null'));

                //update proposed month into pt project
                $update_proposed_month = DB::select(DB::raw('UPDATE pt_project set 
                proposed_duration_months = (proposed_duration_year * 12 +  proposed_duration_months)
                where proposed_duration_year is not null'));

                ///update into users table for client id
                DB::update("update users set client_id = 95 where name = 'Jhapa Rural Municipality'");
                DB::update("update users set client_id = 125 where name = 'Gadhi Rural Municipality'");
                DB::update("update users set client_id = 96 where name = 'Barhadashi Rural Municipality'");
                DB::update("update users set client_id = 90 where name = 'Shivasatakshi Municipality'");
                DB::update("update users set client_id = 129 where name = 'Barju Rural Municipality'");

                // Update Gps lat and long for null value
                DB::update("update mst_fed_local_level SET gps_lat = '27.3204994', gps_long = '84.6369019' WHERE code = '20801'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.5711994', gps_long = '86.5221024' WHERE code = '20108'");
                DB::update("update mst_fed_local_level SET gps_lat = '27.0471001', gps_long = '84.955101' WHERE code = '20705'");
                DB::update("update mst_fed_local_level SET gps_lat = '28.7054005', gps_long = '80.9417038' WHERE code = '70805'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.7775002', gps_long = '85.3173981' WHERE code = '20616'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.8451996', gps_long = '85.2991028' WHERE code = '20614'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.9256992', gps_long = '85.441597' WHERE code = '20513'");
                DB::update("update mst_fed_local_level SET gps_lat = '27.2000008', gps_long = '84.7343979' WHERE code = '20802'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.9871998', gps_long = '84.9101028' WHERE code = '20706'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.8733997', gps_long = '85.6561966' WHERE code = '20508'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.8006001', gps_long = '87.2770004' WHERE code = '11301'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.8584003', gps_long = '85.5094986' WHERE code = '20519'");
                DB::update("update mst_fed_local_level SET gps_lat = '27.0657005', gps_long = '84.7285995' WHERE code = '20810'");
                DB::update("update mst_fed_local_level SET gps_lat = '26.677',   gps_long = '85.9856033' WHERE code = '20316'");


                ///update project cost and federal amount
                DB::update("update pt_project set source_federal_amount = project_cost where 
                source_federal_amount = 0 and fiscal_year_id = 3 and project_status_id in (2,3,4)");

                DB::update("update pt_project set source_federal_amount = project_cost where 
                source_federal_amount = 0 and fiscal_year_id = 2 and project_status_id in (2,3,4)");

                DB::update("update pt_project set project_cost = (source_federal_amount + source_local_level_amount + source_donar_amount)
                where fiscal_year_id = 3 and project_status_id in (2,3,4)");

                DB::update("update pt_project set project_cost = (source_federal_amount + source_local_level_amount + source_donar_amount)
                where fiscal_year_id = 2 and project_status_id in (2,3,4)");

                DB::update("update pt_project set source_federal_amount = (source_federal_amount * 1000 )
                where CHAR_LENGTH(source_federal_amount::VARCHAR) < 5 and fiscal_year_id = 3 and project_status_id in (2,3,4)");

                DB::update("update pt_project set project_cost = (project_cost * 1000 )
                where CHAR_LENGTH(project_cost::VARCHAR) < 5 and fiscal_year_id = 3 and project_status_id in (2,3,4)");


                ///Delete un used client
                DB::select("DELETE from users where client_id = 534");
                DB::select("DELETE from app_setting where client_id = 534");
                DB::select("DELETE from pt_project where client_id = 534");
                DB::select("DELETE from app_client where id = 534");
                DB::update("UPDATE mst_fed_local_level set is_tmpp_applicable = false where id = 534");


                
                
                DB::commit();

                return back()->with('success', 'Insert successfully!');

            } catch (\Exception $th) {
                dd($th);
                DB::rollback();

                return back()->with('error',$th);
            }

    }

    private function clean_tables(){
        DB::table('pt_project_files')->delete();
        DB::table('pt_project_notes')->delete();
        DB::table('pt_project_progress')->delete();
        DB::table('pt_project')->delete();
        DB::table('mst_fiscal_year')->delete();
        DB::table('mst_fed_local_level')->delete();
        DB::table('mst_fed_district')->delete();
        DB::table('mst_designation')->delete();
        DB::table('mst_note_type')->delete();
        DB::table('mst_project_sub_category')->delete();
        DB::table('mst_road_connectivity_type')->delete();
        DB::table('users')->delete();
        DB::table('mst_executing_entity')->delete();
        DB::table('mst_tmpp_related_staff')->delete();
    }




    public function resetSequence(){
        DB::statement("ALTER SEQUENCE mst_fiscal_year_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_fed_district_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_fed_local_level_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_designation_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_note_type_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_project_sub_category_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_road_connectivity_type_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE app_client_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE users_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_executing_entity_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE mst_tmpp_related_staff_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE pt_project_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE pt_project_files_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE pt_project_notes_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE pt_project_progress_id_seq RESTART WITH 1");
    }
 

}
