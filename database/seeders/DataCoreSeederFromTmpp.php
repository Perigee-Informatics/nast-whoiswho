<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataCoreSeederFromTmpp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->clean_tables();
        $this->mst_nepali_month();
        $this->mst_fed_province();
        $this->mst_fed_local_level_type();
        $this->app_client();
        $this->mst_project_status_seeder();
        $this->mst_project_category();
        $this->mst_unit_seeder();
        $this->add_user();
        $this->add_executing_entity_type();
        $this->mst_reporting_interval();
    }


    private function clean_tables(){
        DB::table('mst_nepali_month')->delete();
        DB::table('mst_fed_province')->delete();
        DB::table('mst_fed_local_level_type')->delete();
        DB::table('app_client')->delete();
        DB::table('mst_unit')->delete();
        DB::table('mst_project_status')->delete();
        DB::table('mst_project_category')->delete();
        DB::table('mst_executing_entity_type')->delete();
        DB::table('mst_reporting_interval')->delete();
    }
   
    private function mst_nepali_month()
    {
        DB::table('mst_nepali_month')->insert(
            [
                array('id' => 1,'code' => '01','name_en' => 'Baishakh','name_lc' => 'बैशाख','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 2,'code' => '02','name_en' => 'Jestha','name_lc' => 'जेठ','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 3,'code' => '03','name_en' => 'Ashad','name_lc' => 'आषाध','is_quarterly' => 1,'is_yearly' => 1,'is_halfyearly'=>1),
                array('id' => 4,'code' => '04','name_en' => 'Shrawan','name_lc' => 'श्रावन','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 5,'code' => '05','name_en' => 'Bhadra','name_lc' => 'भाद्र','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 6,'code' => '06','name_en' => 'Ashwin','name_lc' => 'आश्विन','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 7,'code' => '07','name_en' => 'Kartik','name_lc' => 'कार्तिक','is_quarterly' => 1,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 8,'code' => '08','name_en' => 'Mangshir','name_lc' => 'मंग्सिर','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 9,'code' => '09','name_en' => 'Poush','name_lc' => 'पुष','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>1),
                array('id' => 10,'code' => '10','name_en' => 'Magh','name_lc' => 'माघ','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 11,'code' => '11','name_en' => 'Falgun','name_lc' => 'फागुन','is_quarterly' => 1,'is_yearly' => null,'is_halfyearly'=>null),
                array('id' => 12,'code' => '12','name_en' => 'Chaitra','name_lc' => 'चैत','is_quarterly' => null,'is_yearly' => null,'is_halfyearly'=>null),
            ]);
        DB::statement("SELECT SETVAL('mst_nepali_month_id_seq',1000)");

    }


    private function mst_fed_province(){
        DB::table('mst_fed_province')->insert([
            array('id' => 1,'code' => '1','name_en' => 'Province 1','name_lc' => 'प्रदेश १'),
            array('id' => 2,'code' => '2','name_en' => 'Province 2','name_lc' => 'प्रदेश २'),
            array('id' => 3,'code' => '3','name_en' => 'Bagmati','name_lc' => 'बागमती'),
            array('id' => 4,'code' => '4','name_en' => 'Gandaki','name_lc' => 'गण्डकी'),
            array('id' => 5,'code' => '5','name_en' => 'Lumbini','name_lc' => 'लुम्बिनी'),
            array('id' => 6,'code' => '6','name_en' => 'Karnali','name_lc' => 'कर्णाली'),
            array('id' => 7,'code' => '7','name_en' => 'SudurPashchim','name_lc' => 'सुदूरपश्चिम'),

        ]);
        DB::statement("SELECT SETVAL('mst_fed_province_id_seq',1000)");

    }

    private function mst_fed_local_level_type(){
        DB::table('mst_fed_local_level_type')->insert([
            array('id' => 1,'code' => '1','name_en' => 'Rural Municipal','name_lc' => 'गाउँपालिका'),
            array('id' => 2,'code' => '2','name_en' => 'Municipal','name_lc' => 'नगरपालिका'),
            array('id' => 3,'code' => '3','name_en' => 'Sub-Metropolitan City','name_lc' => 'उपमहानगरपालिका'),
            array('id' => 4,'code' => '4','name_en' => 'Metropolitan city','name_lc' => 'महानगरपालिका'),

        ]);
        DB::statement("SELECT SETVAL('mst_fed_local_level_type_id_seq',1000)");

    }

    private function app_client(){
        DB::table('app_client')->insert([
            array('id' => 1000,'code' => '1000','name_en' => 'System','name_lc' => 'सिस्टम','is_tmpp_applicable' => true)
        ]);
    }

    private function mst_project_category(){
        DB::table('mst_project_category')->insert([
            array('id' => 1,'code' => 'HSE','name_en' => 'Communal Housing Building','name_lc' => 'सामुहिक आवास भवन'),
            array('id' => 2,'code' => 'INFRA','name_en' => 'Community Infrastructure','name_lc' => 'सामुदायिक पूर्वाधार'),
            array('id' => 3,'code' => 'ROAD','name_en' => 'Road','name_lc' => 'सडक'),
            array('id' => 4,'code' => 'AGRI','name_en' => '	Agricultural Development','name_lc' => 'कृषि विकाश'),
            array('id' => 5,'code' => 'DWS','name_en' => 'Drinking Water and Sanitation','name_lc' => 'खानेपानी तथा सरसफाई'),
            array('id' => 6,'code' => 'IRR','name_en' => 'Irrigation and River Control','name_lc' => 'सिँचाई तथा नदी नियन्त्रण'),
            array('id' => 7,'code' => 'ENE','name_en' => 'Energy','name_lc' => 'उर्जा'),
            array('id' => 8,'code' => 'DRI','name_en' => 'Disaster Reduction Infrastructure','name_lc' => 'विपद न्युनिकरण पूर्वाधार'),
            array('id' => 1001,'code' => 'TRN','name_en' => 'Training','name_lc' => 'तालिम'),

        ]);
    }

    private function mst_unit_seeder(){
        DB::table('mst_unit')->insert([
            array('id' => 1,'code' => 'hm', 'category_id' => 1, 'name_en' => 'home','name_lc' => 'घर'),
            array('id' => 2,'code' => 'm', 'category_id' => 3, 'name_en' => 'meter','name_lc' => 'मिटर'),
            array('id' => 3,'code' => 'km', 'category_id' => 3, 'name_en' => 'kilo-meter','name_lc' => 'कि.मि'),
            array('id' => 4,'code' => 'वटा', 'category_id' => 1, 'name_en' => 'वटा','name_lc' => 'वटा'),
            array('id' => 5,'code' => 'वटा', 'category_id' => 2, 'name_en' => 'वटा','name_lc' => 'वटा'),
            array('id' => 6,'code' => 'वटा', 'category_id' => 7, 'name_en' => 'वटा','name_lc' => 'वटा'),
            array('id' => 7,'code' => 'वटा', 'category_id' => 8, 'name_en' => 'वटा','name_lc' => 'वटा'),
            array('id' => 8,'code' => 'तल्ला', 'category_id' => 2, 'name_en' => 'तल्ला','name_lc' => 'तल्ला'),
            array('id' => 9,'code' => 'm', 'category_id' => 5, 'name_en' => 'meter','name_lc' => 'मिटर'),
            array('id' => 10,'code' => 'm', 'category_id' => 6, 'name_en' => 'meter','name_lc' => 'मिटर'),
            array('id' => 11,'code' => 'm', 'category_id' => 7, 'name_en' => 'meter','name_lc' => 'मिटर'),
            array('id' => 12,'code' => 'संख्या', 'category_id' => 4, 'name_en' => 'संख्या','name_lc' => 'संख्या'),
        ]);
    }

    private function mst_project_status_seeder()
    {
        DB::table('mst_project_status')->insert([
            array('id' => 1,'code' => 'REQ','name_en' => 'Requested','name_lc' => 'माग गरियो','is_active' => true),
            array('id' => 2,'code' => 'SE','name_en' => 'Selected','name_lc' => 'स्वीकृत','is_active' => true),
            array('id' => 3,'code' => 'WIP','name_en' => 'Work-In-Progress','name_lc' => 'कार्य सुचारु','is_active' => true),
            array('id' => 4,'code' => 'C','name_en' => 'Project Completed','name_lc' => 'कार्य सम्पन्न','is_active' => true),
            array('id' => 5,'code' => 'RE','name_en' => 'Rejected','name_lc' => 'अस्वीकृत','is_active' => true),
        ]);
    }

    private function add_user()
    {
        DB::table('users')->insert([
            'id' => 1,
            'client_id' => 1000,
            'name' => 'superadmin',
            'email' => 'super@admin.com',
            'password' => bcrypt('123456'),
        ]); 
    }

    public function add_executing_entity_type()
    {
        DB::table('mst_executing_entity_type')->insert([
            array('id' => 1,'code' => 'UC','name_en' => 'User Committee','name_lc' => 'उपभोक्ता समिति'),
            array('id' => 2,'code' => 'C','name_en' => 'Contract','name_lc' => 'ठेक्का'),
            array('id' => 3,'code' => 'O','name_en' => 'Other','name_lc' => 'अन्य')
        ]);
    }

    public function mst_reporting_interval()
    {
        DB::table('mst_reporting_interval')->insert([
            array('id' => 1,'code' => 'FQ','name_en' => 'First Quarterly','name_lc' => 'प्रथम चौमासिक (कार्तिक मसान्त)'),
            array('id' => 2,'code' => 'SQ','name_en' => 'Second Quarterly','name_lc' => 'दोस्रो चौमासिक (फागुन मसान्त)'),
            array('id' => 3,'code' => 'Y','name_en' => 'Yearly','name_lc' => 'बार्षिक (असार मसान्त)'),
            array('id' => 4,'code' => 'HY','name_en' => 'Half Yearly','name_lc' => 'मध्यावधि (मंग्सिर मसान्त)'),
        ]);
    }
}
