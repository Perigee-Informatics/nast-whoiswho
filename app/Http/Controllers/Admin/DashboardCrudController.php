<?php

namespace App\Http\Controllers\Admin;

use App\Models\PtProject;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Models\MstFedDistrict;
use App\Base\BaseCrudController;
use App\Models\MstFedLocalLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\Charts\ProjectByStatusChartController;
use App\Http\Controllers\Admin\Charts\ProjectByCategoryChartController;
use App\Http\Controllers\Admin\Charts\ProjectByCategoryCostChartController;

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

   

    public function getDashboardDataForClient()
    {

        $fiscal_year_id = Session::get('fiscal_year_id');

        //for all  fiscal_year\
        $new_projects = $this->user->clientEntity->clientProjectsDemand;
        $selected_projects = $this->user->clientEntity->clientProjectsSelected;
        $wip_projects = $this->user->clientEntity->clientProjectsWip;
        $completed_projects = $this->user->clientEntity->clientProjectsComplete;
        $total_projects = $this->user->clientEntity->clientProjects;
        
        if(isset($fiscal_year_id)){
            $new_projects = $new_projects->where('fiscal_year_id',$fiscal_year_id);
            $selected_projects = $selected_projects->where('fiscal_year_id',$fiscal_year_id);
            $wip_projects = $wip_projects->where('fiscal_year_id',$fiscal_year_id);
            $completed_projects = $completed_projects->where('fiscal_year_id',$fiscal_year_id);
            $total_projects = $total_projects->where('fiscal_year_id',$fiscal_year_id);
        }

        $new_projects_cnt =  $new_projects->count();
        $selected_projects_cnt =  $selected_projects->count();
        $wip_projects_cnt =  $wip_projects->count();
        $completed_projects_cnt =  $completed_projects->count();
        $total_projects_cnt =  $total_projects->count();

	    Widget::add()->to('before_content')->type('div')->class('row')->content([

            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-teal',
                'wrapper'=>['class' => 'col-md-3'],
                'value' => $new_projects_cnt,
                'description' => 'नया आयोजना माग',
            ]),
            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-cyan',
                'wrapper'=>['class' => 'col-md-3'],
                'value' => $selected_projects_cnt,
                'description' => 'स्वीकृत आयोजना',
            ]),
            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-green',
                'wrapper'=>['class' => 'col-md-2'],
                'value' => $wip_projects_cnt,
                'description' => 'कार्य सुचारु',
            ]),
            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-warning',
                'wrapper'=>['class' => 'col-md-2'],
                'value' => $completed_projects_cnt,
                'description' => 'कार्य सम्पन्न',
            ]),
            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-dark',
                'wrapper'=>['class' => 'col-md-2'],
                'value' => $total_projects_cnt,
                'description' => 'जम्मा आयोजना',
            ]),
	    ]);
	  

        
        $projectByStatus = [
            'type'       => 'chart',
            'controller' => ProjectByStatusChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid red; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                'header' => 'Project By Status', 
            ],
        ];
         
        $projectByCategory = [
            'type'       => 'chart',
            'controller' => ProjectByCategoryChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid blue; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                'header' => 'Project By Category (स्वीकृत आयोजना)', 
            ],
        ];

        $projectByCategoryCost = [
            'type'       => 'chart',
            'controller' => ProjectByCategoryCostChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid brown; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                 'header' => 'Project By Cost (स्वीकृत आयोजना)', 
            ],
        ];

        $content = [
            $projectByStatus,
            $projectByCategory,
            $projectByCategoryCost,
        ];
       

        $widgets['after_content'][] = [
            'type' => 'div',
            'class' => 'row m-2',
            'content' => $content
        ];

        $this->data['widgets'] = $widgets;
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
            $features_one['properties']['PROVINCE_NAME']=$province->name_lc;

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

        //get tmpp_applicable districts only
        $tmpp_applicable_districts = MstFedLocalLevel::where('is_tmpp_applicable',true)->distinct('district_id')->pluck('district_id')->toArray();
        $features=[];
        foreach($db_data as $key=> $dt){
            $district=DB::table('mst_fed_district')->where('code',$dt->code)->first();

            $features_one['type']="Feature";
            $features_one['geometry']['type']="Polygon";
            $features_one['properties']['District']=$district->id;
            $features_one['properties']['TARGET']=$district->name_en;
            $features_one['properties']['Level']=1;
            $features_one['properties']['DISTRICT_NAME']=$district->name_lc;
            if(in_array($district->id,$tmpp_applicable_districts)){
                $features_one['properties']['TMPP_applicable']=true;
            }else{
                $features_one['properties']['TMPP_applicable']=false;
            }

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

        $tmpp_applicable_locallevel = MstFedLocalLevel::where('is_tmpp_applicable',true)->pluck('id')->toArray();

        $features=[];
        foreach($db_data as $key=> $dt){
            $local_level=DB::table('mst_fed_local_level')->where('code',$dt->code)->first();
            $features_one['type']="Feature";
            $features_one['geometry']['type']="Polygon";
            $features_one['properties']['Locallevel']=$local_level->id;
            $features_one['properties']['TARGET']=$local_level->name_en;
            $features_one['properties']['Level']=2;
            $features_one['properties']['LOCALLEVEL_NAME']=$local_level->name_lc;

            if(in_array($local_level->id,$tmpp_applicable_locallevel)){
                $features_one['properties']['TMPP_applicable']=true;
            }else{
                $features_one['properties']['TMPP_applicable']=false;
            }

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
    public function getLocalLevelProjectsData(Request $request)
    {
        $fiscal_year_clause = "1=1";
        $fiscal_year_id = Session::get('fiscal_year_id');
        $level = $request->level;

        $province_clause = '1=1';
        $district_clause = '1=1';
        $local_level_clause = '1=1';

        if($level == '0'){
            $province_clause = 'mfp.id='.$request->area_id;
        }else if($level == '1'){
            $district_clause = 'mfd.id='.$request->area_id;
        }else if($level == '2'){
            $local_level_clause = 'mfll.id='.$request->area_id;
        }

        if($fiscal_year_id){
            $fiscal_year_clause = "pp.fiscal_year_id=".$fiscal_year_id;
        }
        $projects =  DB::table('pt_selected_project as pp')
                        ->join('app_client as ac','ac.id','pp.client_id')
                        ->join('mst_fed_local_level as mfll', 'ac.fed_local_level_id','mfll.id')                            
                        ->join('mst_fed_district as mfd', 'mfll.district_id','mfd.id')
                        ->join('mst_fed_province as mfp','mfd.province_id','mfp.id')
                        ->join('mst_project_category as mpc','pp.category_id','mpc.id')
                        ->join('mst_project_status as mps','pp.project_status_id','mps.id')
                        ->join('mst_fiscal_year as mfy','pp.fiscal_year_id','mfy.id')
                        ->select('pp.project_id','mfy.code as fiscal_year', 'mfp.name_lc as province','mfd.name_lc as district','mfll.name_lc as locallevel','pp.name_lc as project_name','mpc.code as icon','mpc.name_lc as project_category',                                           
                                'pp.project_cost as central_contribution','pp.source_local_level_amount as local_level_contribution', 'pp.source_donar_amount as other_contribution','mps.name_lc as project_status',
                                DB::raw("COALESCE ( pp.gps_lat, mfll.gps_lat,'27.700769') as lat"),DB::raw("COALESCE ( pp.gps_long, mfll.gps_long, '85.300140' ) as long"))
                        ->whereIn('pp.project_status_id',[2,3,4])
                        ->whereRaw($fiscal_year_clause)
                        ->whereRaw($province_clause)
                        ->whereRaw($district_clause)
                        ->whereRaw($local_level_clause)
                        ->get();
        $local_level_projects = json_encode($projects);

        return response()->json($local_level_projects);
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
        $fiscal_year_id = Session::get('fiscal_year_id');
        $data['level'] = -1;
        $districts =DB::table('mst_fed_local_level')->where('is_tmpp_applicable',true)->distinct('district_id')->pluck('district_id');
        $local_level=DB::table('mst_fed_local_level')->whereIn('district_id',$districts)->where('is_tmpp_applicable',true)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['districts_count']=$districts->count();
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=$count[2];
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

    
            $new_projects = DB::table('pt_project')->where('project_status_id',1)->where('deleted_uq_code',1);
            $selected_projects = DB::table('pt_selected_project')->where('project_status_id',2)->where('deleted_uq_code',1);
            $wip_projects = DB::table('pt_selected_project')->where('project_status_id',3)->where('deleted_uq_code',1);
            $completed_projects = DB::table('pt_selected_project')->where('project_status_id',4)->where('deleted_uq_code',1);
        
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

        $fiscal_year_clause = "1=1";
        if(isset($fiscal_year_id)){
            $fiscal_year_clause = "pp.fiscal_year_id = ".$fiscal_year_id;
        }

        //get province-wise-project-data
        $province_projects = DB::table('pt_selected_project as pp')->select(DB::raw('ROW_NUMBER() OVER(ORDER BY mfp.id ASC) AS sn'),'mfp.id as province_id','mfp.name_lc',DB::raw('count(mfp.id) as total_project'),DB::raw('sum(pp.source_federal_amount) as project_cost'))
                                ->join('app_client as ac','ac.id','pp.client_id')
                                ->join('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                                ->join('mst_fed_district as mfd','mfd.id','mfll.district_id')
                                ->join('mst_fed_province as mfp','mfp.id','mfd.province_id')
                                ->join('mst_fiscal_year as mfy','mfy.id','pp.fiscal_year_id')
                                ->whereIn('pp.project_status_id',[2,3,4])
                                ->whereRaw($fiscal_year_clause)
                                ->groupBy('mfp.id')
                                ->orderBy('mfp.id','ASC')
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

    //get Province Data
    public function getProvinceGeoData($request)
    {
        $fiscal_year_id = Session::get('fiscal_year_id');
        $province_id = $request->id;
        $data['level'] = 0;

        $districts =DB::table('mst_fed_local_level as mfll')
                        ->leftJoin('mst_fed_district as mfd','mfd.id','mfll.district_id')
                        ->leftJoin('mst_fed_province as mfp','mfp.id','mfd.province_id')
                        ->where('mfll.is_tmpp_applicable',true)
                        ->where('mfp.id',$province_id)
                        ->distinct('mfll.district_id')
                        ->pluck('mfll.district_id');
                     

        $local_level=DB::table('mst_fed_local_level')->whereIn('district_id',$districts)->where('is_tmpp_applicable',true)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['districts_count']=$districts->count();
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=$count[2];
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

        //select app client only from respective districts of respective province
        $app_client_ids = DB::table('app_client as ac')
                            ->leftJoin('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                            ->leftJoin('mst_fed_district as mfd','mfd.id','mfll.district_id')
                            ->whereIn('mfll.district_id',$districts)
                            ->pluck('ac.id')
                            ->toArray();

        $new_projects = DB::table('pt_project')->where('project_status_id',1)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $selected_projects = DB::table('pt_selected_project')->where('project_status_id',2)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $wip_projects = DB::table('pt_selected_project')->where('project_status_id',3)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $completed_projects = DB::table('pt_selected_project')->where('project_status_id',4)
                            ->whereIn('client_id',$app_client_ids)
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

        $fiscal_year_clause = "1=1";
        if(isset($fiscal_year_id)){
            $fiscal_year_clause = "pp.fiscal_year_id = ".$fiscal_year_id;
        }

        //get province-wise-project-data
        $province_projects = DB::table('pt_selected_project as pp')->select(DB::raw('ROW_NUMBER() OVER(ORDER BY mfp.id ASC) AS sn'),'mfp.id as province_id','mfp.name_lc',DB::raw('count(mfp.id) as total_project'),DB::raw('sum(pp.source_federal_amount) as project_cost'))
                                ->join('app_client as ac','ac.id','pp.client_id')
                                ->join('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                                ->join('mst_fed_district as mfd','mfd.id','mfll.district_id')
                                ->join('mst_fed_province as mfp','mfp.id','mfd.province_id')
                                ->join('mst_fiscal_year as mfy','mfy.id','pp.fiscal_year_id')
                                ->whereIn('pp.project_status_id',[2,3,4])
                                ->whereIn('pp.client_id',$app_client_ids)
                                ->whereRaw($fiscal_year_clause)
                                ->groupBy('mfp.id')
                                ->orderBy('mfp.id','ASC')
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
                                ->whereIn('pp.client_id',$app_client_ids)
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

    //get District Data
    public function getDistrictGeoData($request)
    {
        $fiscal_year_id = Session::get('fiscal_year_id');
        $district_id = $request->id;
        $data['level'] = 1;


        $local_level=DB::table('mst_fed_local_level')->where('district_id',$district_id)->where('is_tmpp_applicable',true)->pluck('level_type_id')->toArray();
        $count = array_count_values($local_level);

        //count of districts, Metro/Sub-metro/Rural Mun
        $data['count']['districts_count']=0;
        $data['count']['rural_mun_count']=$count[1];
        $data['count']['mun_count']=array_key_exists(2,$count)?$count[2]:0;
        $data['count']['sub_metro_count']=array_key_exists(3,$count)?$count[3]:0;
        $data['count']['metro_count']=array_key_exists(4,$count)?$count[4]:0;
        $data['count']['total_local_level_count']=count($local_level);

        //select app client only from respective districts of respective province
        $app_client_ids = DB::table('app_client as ac')
                            ->leftJoin('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                            ->where('mfll.district_id',$district_id)
                            ->pluck('ac.id')
                            ->toArray();

        $new_projects = DB::table('pt_project')->where('project_status_id',1)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $selected_projects = DB::table('pt_selected_project')->where('project_status_id',2)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $wip_projects = DB::table('pt_selected_project')->where('project_status_id',3)
                            ->whereIn('client_id',$app_client_ids)
                            ->where('deleted_uq_code',1)
                            ->get();

        $completed_projects = DB::table('pt_selected_project')->where('project_status_id',4)
                            ->whereIn('client_id',$app_client_ids)
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
        $province_projects = DB::table('pt_selected_project as pp')->select(DB::raw('ROW_NUMBER() OVER(ORDER BY mfd.id ASC) AS sn'),'mfd.name_lc',DB::raw('count(mfd.id) as total_project'),DB::raw('sum(pp.source_federal_amount) as project_cost'))
                                ->join('app_client as ac','ac.id','pp.client_id')
                                ->join('mst_fed_local_level as mfll','mfll.id','ac.fed_local_level_id')
                                ->join('mst_fed_district as mfd','mfd.id','mfll.district_id')
                                ->join('mst_fiscal_year as mfy','mfy.id','pp.fiscal_year_id')
                                ->whereIn('pp.project_status_id',[2,3,4])
                                ->whereIn('pp.client_id',$app_client_ids)
                                ->whereRaw($fiscal_year_clause)
                                ->groupBy('mfd.id')
                                ->orderBy('mfd.id','ASC')
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
                                ->whereIn('pp.client_id',$app_client_ids)
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