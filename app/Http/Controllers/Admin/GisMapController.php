<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppClient;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class GisMapController extends Controller
{
    public function gisMapData(){

        if(backpack_user()->isClientUser()){
            $client_lat = AppClient::findOrFail(backpack_user()->client_id)->fedLocalLevelEntity->gps_lat;
            $client_long = AppClient::findOrFail(backpack_user()->client_id)->fedLocalLevelEntity->gps_long;

            Session::put('client_lat',$client_lat);
            Session::put('client_long',$client_long);
        }

        $project_province = DB::select('select id, code,name_en,name_lc from mst_fed_province as p	WHERE id in (SELECT distinct province_id FROM mst_fed_district where id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)) order by code asc');
        $project_category = DB::select('select id,code,name_en,name_lc from mst_project_category');
        $project_status = DB::select('select id,code,name_en,name_lc from mst_project_status where is_active=true');
        $project_fiscal_year = DB::select('select id,code from mst_fiscal_year');
        $client_id = backpack_user()->client_id;

        return view('map_detail', compact('project_province','project_category', 'project_status', 'project_fiscal_year','client_id'));
    }
    public function getGisFilterData(Request $request)
    {
        // Total Projects Details
        $sql = "SELECT 
        p.id,p.code,d.name_lc as district_name_np,d.name_en as district_name_en,p.fiscal_year_id,p.name_en,p.name_lc,pc.code category_code,
        pc.name_lc category,pc.name_en as encategory,psc.name_lc sub_category,ps.code status_code,ps.name_lc status,ac.name_lc locallevel,
        ac.name_en enlocallevel,COALESCE ( p.gps_lat, ll.gps_lat, '27.700769' ) lat,COALESCE ( p.gps_long, ll.gps_long, '85.300140' ) lon,
        p.is_multi_fiscalyear_project,p.project_cost,p.source_federal_amount,p.source_local_level_amount,pc.code icon 
        FROM pt_project p
        LEFT JOIN app_client ac on ac.id = p.client_id
        LEFT JOIN mst_fed_local_level ll ON ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d ON ll.district_id = d.id
        LEFT JOIN mst_fed_province pr ON d.province_id = pr.id
        LEFT JOIN mst_project_category pc ON p.category_id = pc.id
        LEFT JOIN mst_project_sub_category psc ON p.sub_category_id = psc.id
        LEFT JOIN mst_project_status ps ON p.project_status_id = ps.id ";

        // Category Count
        $cat_count = "SELECT 
        pc.name_lc,COUNT(pc.id),pc.code,sum(project_cost) as totalcost
        FROM pt_project p
        LEFT JOIN app_client ac on ac.id = p.client_id
        LEFT JOIN mst_fed_local_level ll ON ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d ON ll.district_id = d.id
        LEFT JOIN mst_fed_province pr ON d.province_id = pr.id
        LEFT JOIN mst_project_category pc ON p.category_id = pc.id
        LEFT JOIN mst_project_sub_category psc ON p.sub_category_id = psc.id
        LEFT JOIN mst_project_status ps ON P.project_status_id = ps.id";

        $grouping = "GROUP BY pc.id,p.category_id,pc.code ORDER BY category_id";

        ///Get Status of Project    
        $projectstatus = "SELECT ps.name_lc as status,COUNT(ps.id),ps.id
        FROM pt_project p
        LEFT JOIN app_client ac on ac.id = p.client_id
        LEFT JOIN mst_fed_local_level ll ON ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d ON ll.district_id = d.id
        LEFT JOIN mst_fed_province pr ON d.province_id = pr.id
        LEFT JOIN mst_project_category pc ON p.category_id = pc.id
        LEFT JOIN mst_project_sub_category psc ON p.sub_category_id = psc.id
        LEFT JOIN mst_project_status ps ON p.project_status_id = ps.id  and is_active = true";

        $status_grouping=" GROUP BY ps.id,p.project_status_id";

        /// Search Criteria
        $params = [];
        $wheres = [];
        $data = null;
        $client_id = backpack_user()->client_id;
        $province = $request->province;
        $district = $request->district;
        $local_level = $request->local_level;
        $category = $request->category_id;
        $sub_category = $request->sub_category_id;
        $status = $request->status;
        $fiscal_year = $request->fiscal_year;

        
        if($fiscal_year){
          $fiscal_year = $fiscal_year;
        }else{
            $fiscal_year = AppSetting::where('client_id',backpack_user()->client_id)->pluck('fiscal_year_id')->first();
        }

        if($status){
            $status = $status;
        }else{
            $status = '2,3';
        }
        if($province != null || $district != null || $local_level != null || $category != null || $sub_category != null || $status != null || $fiscal_year != null || $client_id != 1000)
        {
            if (!empty($province)) {
                $wheres[] =  'and d.province_id =' . $province;
            }

            if (!empty($district)) {
                $wheres[] =  'and ll.district_id = '. $district;
            }
            if (!empty($local_level)) {
                $wheres[] =  'and ll.id = ' .$local_level;
            }
            if (!empty($category)) {
                $wheres[] =  'and p.category_id = '. $category;
            }
            if (!empty($sub_category)) {
                $wheres[] =  'and p.sub_category_id = ' . $sub_category;
            }
            if (!empty($status)) {
                $wheres[] =  'and p.project_status_id IN ('.$status.')';
            }
            if (!empty($fiscal_year)) {
                $wheres[] =  'and p.fiscal_year_id = '. $fiscal_year;
            }
            if ($client_id != 1000) {
                $wheres[] =  ' and p.client_id = ' . $client_id;
            }

            if (count($wheres) > 0) {
                $where_clause = " WHERE 1=1 " . implode(" ", $wheres);
                $sql .= $where_clause;
            }

            $gps = DB::select($sql);

            $category_count = $cat_count .= $where_clause;
            $category_count .= $grouping;
            $category_count = DB::select($category_count);


            // $Chartstatus = $projectstatus .= $where_clause;
            // $Chartstatus .= $status_grouping;
            // $donutchart = DB::select($Chartstatus);

        }
        else
        {
            $gps = DB::select($sql);

            $category_count = DB::select("SELECT
                            pc.name_lc,COUNT(pc.id),pc.code,sum(source_federal_amount)as totalcost 
                            FROM pt_project p
                            LEFT JOIN mst_project_category pc ON P.category_id = pc.id 
                            GROUP BY pc.ID,p.category_id,pc.code
                            ORDER BY category_id");

        //    $donutchart = DB::select("SELECT
        //                 ps.name_lc as status,COUNT(ps.id)
        //                 FROM pt_project P
        //                 LEFT JOIN mst_project_status ps ON P.project_status_id = ps.id
        //                 where ps.is_active=true
        //                 GROUP BY ps.id,p.project_status_id");
        }

        // foreach ($donutchart as $chart) {
        //     $status = $chart->status;
        //     $projectsnumber = $chart->count;
        //     $ProjectsStatus[] = $status;
        //     $Projects_status_Number[] = $projectsnumber;
        // }

        // $donut = Charts::create('donut', 'highcharts')
        // ->title(' ')
        // ->responsive(false)
        // ->elementLabel('Projects')
        // ->labels($ProjectsStatus)
        // ->values($Projects_status_Number);

        $markers = json_encode($gps);

        // $selected_params = array();
        // foreach ($params as $key => $val) {
        //     $selected_params[str_replace(":", "", $key)] = $val;
        // }

        return view('gis_map_detail', compact('markers','client_id', 'category_count', 'gps','category_count'));

    }
}
