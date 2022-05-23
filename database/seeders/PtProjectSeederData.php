<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PtProjectSeederData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pt_project')->insert(
            [
                array('id' => 10941,'client_id' => 150,'code' => '8022221378','name_lc' => 'सखडा सडक देखि नरसिंह मन्दिर सम्म नाला तथा आरसीसी ढलान कार्य, रावि-१६','category_id' => 3,'project_cost' => 3750919,'source_federal_amount'=>3000000,'source_local_level_amount' => 750919,'project_affected_population'=>1500,'project_affected_ward_count'=>2,'project_affected_wards'=>'2','gps_lat'=>'0','gps_long'=>'0','has_dpr'=>true,'proposed_duration_months'=>4,'project_status_id'=>1,'fiscal_year_id'=>5,'description_lc'=>'राजविराज नगरपालिका-16','quantity'=>'150','unit_type'=>2,'lmbiscode'=>'80102104','file_upload'=>'','created_by'=>'264','updated_by'=>'264'),
                array('id' => 10927,'client_id' => 150,'code' => '8022221364','name_lc' => 'विधुत विस्तार तथा ६ इन्ची डिप बोरिङ समर्शियल मोटर जडान, रावि-१५','category_id' => 4,'project_cost' => 8625625,'source_federal_amount'=>8000000,'source_local_level_amount' => 625625,'project_affected_population'=>4000,'project_affected_ward_count'=>3,'project_affected_wards'=>'3','gps_lat'=>'0','gps_long'=>'0','has_dpr'=>true,'proposed_duration_months'=>4,'project_status_id'=>1,'fiscal_year_id'=>5,'description_lc'=>'राजविराज नगरपालिका-15','quantity'=>'1','unit_type'=>12,'lmbiscode'=>'80102104','file_upload'=>'','created_by'=>'264','updated_by'=>'264'),
                array('id' => 10922,'client_id' => 150,'code' => '8022221362','name_lc' => 'अधुरो आरसीसी नाला देखि पूर्व नाला सम्म नाला तथा आरसीी सडक ढलान कार्य, राजविराज-१','category_id' => 3,'project_cost' => 3500000,'source_federal_amount'=>2500000,'source_local_level_amount' => 1000000,'project_affected_population'=>3000,'project_affected_ward_count'=>2,'project_affected_wards'=>'2','gps_lat'=>'0','gps_long'=>'0','has_dpr'=>true,'proposed_duration_months'=>4,'project_status_id'=>1,'fiscal_year_id'=>5,'description_lc'=>'राजविराज नगरपालिका-१','quantity'=>'110','unit_type'=>2,'lmbiscode'=>'80102104','file_upload'=>'','created_by'=>'264','updated_by'=>'264'),
            ]);
           DB::statement("SELECT SETVAL('pt_project_id_seq',12773)");
    }
}
