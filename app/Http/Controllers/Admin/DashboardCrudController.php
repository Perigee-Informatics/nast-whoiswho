<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\PtProject;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Base\BaseCrudController;
use App\Models\MstFedLocalLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Session;


/**
 * Class DashboardCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DashboardCrudController extends BaseCrudController
{
    protected $user;
    public function index()
    {
        $this->user = backpack_user();

        if($this->user->isClientUser()){
            // $this->getDashboardDataForClient();
        }
    return view('admin.dashboard',$this->data);
    }

      /**
     * default load
     */
    public function getNepalMapdata(Request $request){
        ini_set( 'precision', 17 );
        ini_set( 'serialize_precision', -1 );

        $db_data=DB::table('mst_fed_coordinates')->where('level',0)->get();
        
        $to_return_array=[];
        $to_return_array['type']='FeatureCollection';
        $features=[];
        foreach($db_data as $key=> $dt){
            $province=DB::table('mst_fed_province')->where('code',$dt->code)->first();
            $features_one['type']="Feature";
            $features_one['geometry']['type']="Polygon";
            $features_one['properties']['Province']=$province->id;
            $features_one['properties']['TARGET']=$province->name_en;
            $features_one['properties']['Level']=0;
            $features_one['properties']['PROVINCE_NAME']=$province->name_en;

            // string geo point to array
            $exploded_array=explode("],",$dt->coordinates);
            $formatted_gis_data=[];
            $remove_arr=array("[","]");
            foreach($exploded_array as $ea){
                $lat_long=explode(",",$ea);
                $lat=floatval(str_replace($remove_arr,"", $lat_long[0]));
                $long=floatval($lat_long[1]);
                array_push($formatted_gis_data,[$lat,$long]);
            }
            $features_one['geometry']['coordinates']=[$formatted_gis_data];

            array_push($features,$features_one);
        }
        $to_return_array['features']=$features;

        $to_return_array=json_encode($to_return_array);
        return response()->json($to_return_array);
    }

      /**
     * province data on click
     */
    public function getProvinceData(Request $request){
        ini_set( 'precision', 17 );
        ini_set( 'serialize_precision', -1 );

        $db_data=DB::table('mst_fed_coordinates')
                    ->leftjoin('mst_fed_district','mst_fed_district.code','mst_fed_coordinates.code')
                    ->where([['mst_fed_coordinates.level',1],['mst_fed_district.province_id',$request->id]])->get();
        $to_return_array=[];
        $to_return_array['type']='FeatureCollection';

        $features=[];
        foreach($db_data as $key=> $dt){
            $district=DB::table('mst_fed_district')->where('code',$dt->code)->first();

            $features_one['type']="Feature";
            $features_one['geometry']['type']="Polygon";
            $features_one['properties']['District']=$district->id;
            $features_one['properties']['TARGET']=$district->name_en;
            $features_one['properties']['Level']=1;
            $features_one['properties']['DISTRICT_NAME']=$district->name_en;

            // string geo point to array
            $exploded_array=explode("],",$dt->coordinates);
            $formatted_gis_data=[];
            $remove_arr=array("[","]");
            foreach($exploded_array as $ea){
                $lat_long=explode(",",$ea);
                $lat=floatval(str_replace($remove_arr,"", $lat_long[0]));
                $long=floatval($lat_long[1]);
                array_push($formatted_gis_data,[$lat,$long]);
            }
            $features_one['geometry']['coordinates']=[$formatted_gis_data];

            array_push($features,$features_one);
        }
        $to_return_array['features']=$features;

        $to_return_array=json_encode($to_return_array);
        return response()->json($to_return_array);
       
    }


    /**
     * district data on click
     */
    public function getDistrictData(Request $request){
        ini_set( 'precision', 17 );
        ini_set( 'serialize_precision', -1 );
        
        $db_data=DB::table('mst_fed_coordinates')
        ->leftjoin('mst_fed_local_level','mst_fed_local_level.code','mst_fed_coordinates.code')
        ->where([['mst_fed_coordinates.level',2],['mst_fed_local_level.district_id',$request->id]])->get();

        $to_return_array=[];
        $to_return_array['type']='FeatureCollection';

        $features=[];
        foreach($db_data as $key=> $dt){
            $local_level=DB::table('mst_fed_local_level')->where('code',$dt->code)->first();
            $features_one['type']="Feature";
            $features_one['geometry']['type']="Polygon";
            $features_one['properties']['Locallevel']=$local_level->id;
            $features_one['properties']['TARGET']=$local_level->name_en;
            $features_one['properties']['Level']=2;
            $features_one['properties']['LOCALLEVEL_NAME']=$local_level->name_en;

            // string geo point to array
            $exploded_array=explode("],",$dt->coordinates);
            $formatted_gis_data=[];
            $remove_arr=array("[","]");
            foreach($exploded_array as $ea){
                $lat_long=explode(",",$ea);
                $lat=floatval(str_replace($remove_arr,"", $lat_long[0]));
                $long=floatval($lat_long[1]);
                array_push($formatted_gis_data,[$lat,$long]);
            }
            $features_one['geometry']['coordinates']=[$formatted_gis_data];

            array_push($features,$features_one);
        }
        $to_return_array['features']=$features;
        $to_return_array=json_encode($to_return_array);
        return response()->json($to_return_array);

    }

    /**
     * project data on click
     */
    public function getMembersData(Request $request)
    {
        $level = $request->level;

        $province_clause = '1=1';
        $district_clause = '1=1';
        $local_level_clause = '1=1';

        if($level == '0'){
            $province_clause = 'm.province_id='.$request->area_id;
        }else if($level == '1'){
            $district_clause = 'mfd.id='.$request->area_id;
        }else if($level == '2'){
            $local_level_clause = 'mfll.id='.$request->area_id;
        }

     
        $members =  DB::table('members as m')
                        ->join('mst_fed_district as mfd', 'm.district_id','mfd.id')
                        ->join('mst_fed_province as mfp','m.province_id','mfp.id')
                        ->join('mst_gender as mg','m.gender_id','mg.id')
                        ->select('m.id','mfp.name_en as province','mfd.name_en as district','m.first_name','m.last_name','mg.name_en as gender',
                        'mfd.gps_lat as lat','mfd.gps_long as long','m.channel_wiw','m.channel_wsfn','channel_foreign')
                        ->whereRaw($province_clause)
                        ->whereRaw($district_clause)
                        ->whereRaw($local_level_clause)
                        ->get();

        $members = json_encode($members);

        return response()->json($members);
    }


    /**
     * getting data according to level
     */
    public function getGeoData(Request $request){

        $data=[];
        if($request->level=="-1"){
            $data=$this->getNepalGeoData($request);
        }
        else if($request->level=="0"){
            $data=$this->getProvinceGeoData($request);
        }
        else if($request->level=="1"){
            $data=$this->getDistrictGeoData($request);
        }
        else if($request->level=="2"){
            $data=$this->getLocalLevelGeoData($request);
        }
        else {
            $data=[];
        }
        return response()->json($data);
    }

    //get nepal data
    public function getNepalGeoData($request)
    {
        $data['level'] = -1;
        $districts =DB::table('mst_fed_local_level')->distinct('district_id')->pluck('district_id');
        $local_level=DB::table('mst_fed_local_level')->whereIn('district_id',$districts)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['districts_count']=$districts->count();
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=$count[2];
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

        $members = Member::all();
        //male female
        $gender_data_nepal =  DB::table('members as m')
                        ->leftJoin('mst_gender as mg','mg.id','m.gender_id')
                        ->select(DB::raw('count(m.gender_id) as gender_count'),'mg.name_en')
                        ->groupBy('m.gender_id','mg.name_en')
                        ->orderBy('m.gender_id')
                        ->get();

        $gender_data_province = DB::table('members as m')
                        ->leftJoin('mst_fed_province as mfp','mfp.id','m.province_id')
                        ->leftJoin('mst_gender as mg','mg.id','m.gender_id')
                        ->select('m.province_id','mfp.name_en',
                                DB::raw('count(case when gender_id = 1 then 1 end) as male'),
                                DB::raw('count(case when gender_id = 2 then 1 end) as female'),
                                DB::raw('count(m.gender_id) as total'))
                        ->groupBy('m.province_id','mfp.name_en')
                        ->orderBy('m.province_id')
                        ->get();

        $datas = [] ;
        $labels = [];
        //format data for charts
        foreach($gender_data_province as $row){
            $datas ['male'][] = $row->male;
            $datas ['female'][] = $row->female;
            $datas ['total'][] = $row->total;
            $labels[] = $row->name_en;
        }

        $data['gender_data']['main'] = $gender_data_province->toArray();                    
        $data['gender_data']['chart']['labels'] = $labels;                    
        $data['gender_data']['chart']['data'] = $datas;     

        unset($datas);
        unset($labels);


        //for agw wise distribution
        $datas = [] ;
        $labels = [];

        $datas['Below 30']=0;
        $datas['31-40']=0;
        $datas['41-50']=0;
        $datas['51-60']=0;
        $datas['60 & Above']=0;
        foreach($members as $member)
        {
            $member_age = Carbon::now()->diffInYears(Carbon::parse($member->dob_ad));

            if($member_age <= 30){
                $datas['Below 30']++;
            }
            if($member_age > 30 && $member_age <= 40){
                $datas['31-40']++;
            }
            if($member_age > 40 && $member_age <= 50){
                $datas['41-50']++;
            }
            if($member_age > 50 && $member_age <= 60){
                $datas['51-60']++;
            }
            if($member_age > 60){
                $datas['60 & Above']++;
            }
        }

        $data['age_group_data']['data'] = $datas;  
        $data['age_group_data']['chart']['labels'] = ['Below 30','31-40','41-50','51-60','60 & Above'];                    
        $data['age_group_data']['chart']['data'] = $datas;     

        unset($datas);
        unset($labels);
        return $data;
    }

    //get Province Data
    public function getProvinceGeoData($request)
    {
        $province_id = $request->id;
        $data['level'] = 0;

        $districts =DB::table('mst_fed_district')->whereProvinceId($province_id)->pluck('id');


        $local_level=DB::table('mst_fed_local_level')->whereIn('district_id',$districts)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['districts_count']=$districts->count();
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=$count[2];
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

        $gender_data_districts = DB::table('members as m')
                                    ->leftJoin('mst_fed_province as mfp','mfp.id','m.province_id')
                                    ->leftJoin('mst_fed_district as mfd','mfd.id','m.district_id')
                                    ->leftJoin('mst_gender as mg','mg.id','m.gender_id')
                                    ->select('m.district_id','mfp.id as province_id','mfd.name_en',
                                            DB::raw('count(case when gender_id = 1 then 1 end) as male'),
                                            DB::raw('count(case when gender_id = 2 then 1 end) as female'),
                                            DB::raw('count(m.gender_id) as total'))
                                    ->where('mfd.province_id',$province_id)
                                    ->groupBy('m.district_id','mfp.id','mfd.name_en')
                                    ->orderBy('m.district_id')
                                    ->get();



           $datas = [] ;
           $labels = [];


           //format data for charts
            foreach($gender_data_districts as $row){
               $datas ['male'][] = $row->male;
               $datas ['female'][] = $row->female;
               $datas ['total'][] = $row->total;
               $labels[] = $row->name_en;
            }
   
           $data['gender_data']['main'] = $gender_data_districts->toArray();                    
           $data['gender_data']['chart']['labels'] = $labels;                    
           $data['gender_data']['chart']['data'] = $datas;
           
           $data['province_name'] = MstFedProvince::find($province_id)->name_en;
   
           unset($datas);
           unset($labels);

            //for age wise distribution
        $members = Member::whereProvinceId($province_id)->get();

        $datas = [] ;
        $labels = [];

        $datas['Below 30']=0;
        $datas['31-40']=0;
        $datas['41-50']=0;
        $datas['51-60']=0;
        $datas['60 & Above']=0;
        foreach($members as $member)
        {
            $member_age = Carbon::now()->diffInYears(Carbon::parse($member->dob_ad));

            if($member_age <= 30){
                $datas['Below 30']++;
            }
            if($member_age > 30 && $member_age <= 40){
                $datas['31-40']++;
            }
            if($member_age > 40 && $member_age <= 50){
                $datas['41-50']++;
            }
            if($member_age > 50 && $member_age <= 60){
                $datas['51-60']++;
            }
            if($member_age > 60){
                $datas['60 & Above']++;
            }
        }

        $data['age_group_data']['data'] = $datas;  
        $data['age_group_data']['chart']['labels'] = ['Below 30','31-40','41-50','51-60','60 & Above'];                    
        $data['age_group_data']['chart']['data'] = $datas;     

        unset($datas);
        unset($labels);
   
           return $data;
    }

    //get District Data
    public function getDistrictGeoData($request)
    {
        $district_id = $request->id;
        $data['level'] = 1;


        $local_level=DB::table('mst_fed_local_level')->where('district_id',$district_id)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=array_key_exists(2,$count)?$count[2]:0;
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

        $gender_data_local_level = DB::table('members as m')
                                    ->leftJoin('mst_fed_province as mfp','mfp.id','m.province_id')
                                    ->leftJoin('mst_fed_district as mfd','mfd.id','m.district_id')
                                    ->leftJoin('mst_fed_local_level as mfll','mfll.id','m.local_level_id')
                                    ->leftJoin('mst_gender as mg','mg.id','m.gender_id')
                                    ->select('m.local_level_id','mfll.name_en',
                                            DB::raw('count(case when gender_id = 1 then 1 end) as male'),
                                            DB::raw('count(case when gender_id = 2 then 1 end) as female'),
                                            DB::raw('count(m.gender_id) as total'))
                                    ->where('mfd.district_id',$district_id)
                                    ->groupBy('m.local_level_id','mfll.name_en')
                                    ->orderBy('m.local_level_id')
                                    ->get();



           $datas = [] ;
           $labels = [];
           //format data for charts
           foreach($gender_data_local_level as $row){
               $datas ['male'][] = $row->male;
               $datas ['female'][] = $row->female;
               $labels[] = $row->name_en;
           }
   
           $data['gender_data']['main'] = $gender_data_local_level->toArray();                    
           $data['gender_data']['chart']['labels'] = $labels;                    
           $data['gender_data']['chart']['data'] = $datas;
           
           $data['district_name'] = MstFedDistrict::find($district_id)->name_en;
   
           unset($datas);
           unset($labels);
          

        return $data;
    }
    //get District Data
    public function getLocalLevelGeoData($request)
    {
        $fiscal_year_id = Session::get('fiscal_year_id');
        $local_level_id = $request->id;
        $data['level'] = 2;

        $app_client_id = DB::table('app_client')->where('fed_local_level_id',$local_level_id)->pluck('id')->first();
        $new_projects = DB::table('pt_project')->where('project_status_id',1)
                            ->where('client_id',$app_client_id)
                            ->where('deleted_uq_code',1)
                            ->get();

        $selected_projects = DB::table('pt_selected_project')->where('project_status_id',2)
                            ->where('client_id',$app_client_id)
                            ->where('deleted_uq_code',1)
                            ->get();

        $wip_projects = DB::table('pt_selected_project')->where('project_status_id',3)
                            ->where('client_id',$app_client_id)
                            ->where('deleted_uq_code',1)
                            ->get();

        $completed_projects = DB::table('pt_selected_project')->where('project_status_id',4)
                            ->where('client_id',$app_client_id)
                            ->where('deleted_uq_code',1)
                            ->get();

        if(isset($fiscal_year_id)){
            $new_projects = $new_projects->where('fiscal_year_id',$fiscal_year_id);
            $selected_projects = $selected_projects->where('fiscal_year_id',$fiscal_year_id);
            $wip_projects = $wip_projects->where('fiscal_year_id',$fiscal_year_id);
            $completed_projects = $completed_projects->where('fiscal_year_id',$fiscal_year_id);
        }

        $new_projects_cnt =  $new_projects->count();
        $selected_projects_cnt =  $selected_projects->count();
        $wip_projects_cnt =  $wip_projects->count();
        $completed_projects_cnt =  $completed_projects->count();

        $data['count']['new_projects_count'] = $new_projects_cnt;
        $data['count']['selected_projects_count'] = $selected_projects_cnt;
        $data['count']['wip_projects_count'] = $wip_projects_cnt;
        $data['count']['completed_projects_count'] = $completed_projects_cnt;
        
        if(isset($fiscal_year_id)){
            $fiscal_year_clause = "pp.fiscal_year_id = ".$fiscal_year_id;
        }

        //get province-wise-project-data
        $province_projects = DB::table('pt_selected_project as pp')->select(DB::raw('ROW_NUMBER() OVER(ORDER BY mfll.id ASC) AS sn'),'mfll.name_lc',DB::raw('count(mfll.id) as total_project'),DB::raw('sum(pp.source_federal_amount) as project_cost'))
                                ->join('app_client as ac','ac.id','pp.client_id')
                                ->join('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                                ->join('mst_fiscal_year as mfy','mfy.id','pp.fiscal_year_id')
                                ->whereIn('pp.project_status_id',[2,3,4])
                                ->where('pp.client_id',$app_client_id)
                                ->where('pp.deleted_uq_code',1)
                                ->whereRaw($fiscal_year_clause)
                                ->groupBy('mfll.id')
                                ->orderBy('mfll.id','ASC')
                                ->get();
        $datas = [] ;
        $labels = [];
        $costs = [];
        $total_project_cost = 0;

        //format data for charts
        foreach($province_projects as $row){
            $labels [] = $row->name_lc;
            $datas [] = $row->total_project;
            $costs [] = $row->project_cost;
            $total_project_cost += $row->project_cost;
        }

        $data['province_projects']['main'] = $province_projects->toArray();                    
        $data['province_projects']['chart']['labels'] = $labels;                    
        $data['province_projects']['chart']['data'] = $datas;     
        $data['province_projects']['chart']['cost'] = $costs;     
        $data['province_projects']['total_project_cost'] = $total_project_cost;     

        unset($datas);
        unset($labels);
        unset($costs);
        unset($total_project_cost);

        //get projects category-wise
        $category_projects = DB::table('pt_selected_project as pp')->select(DB::raw('ROW_NUMBER() OVER(ORDER BY mpc.id ASC) AS sn'),'mpc.id as category_id','mpc.name_lc',DB::raw('count(mpc.id) as total_project'),DB::raw('sum(pp.source_federal_amount) as category_cost'))
                                ->join('mst_project_category as mpc','mpc.id','pp.category_id')
                                ->join('mst_fiscal_year as mfy','mfy.id','pp.fiscal_year_id')
                                ->whereIn('pp.project_status_id',[2,3,4])
                                ->where('pp.client_id',$app_client_id)
                                ->where('pp.deleted_uq_code',1)
                                ->whereRaw($fiscal_year_clause)
                                ->groupBy('mpc.id')
                                ->orderBy('mpc.id','ASC')
                                ->get();

        $datas = [] ;
        $labels = [];
        $costs = [];
        $total_project_cost = 0;

        //format data for charts
        foreach($category_projects as $row){
            $labels [] = $row->name_lc;
            $datas [] = $row->total_project;
            $costs [] = $row->category_cost;
            $total_project_cost += $row->category_cost;
        }

        $data['category_projects']['main'] = $category_projects->toArray();                    
        $data['category_projects']['chart']['labels'] = $labels;                    
        $data['category_projects']['chart']['data'] = $datas;  
        $data['category_projects']['chart']['cost'] = $costs;     
        $data['category_projects']['total_project_cost'] = $total_project_cost;     

        return $data;
    }

}