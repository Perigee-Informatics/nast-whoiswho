<?php

namespace App\Http\Controllers\Api;

use App\Models\MstUnit;
use App\Models\AppClient;
use App\Models\PtProject;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Models\MstNepaliMonth;
use App\Models\MstFedLocalLevel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MstProjectSubCategory;
use Illuminate\Support\Facades\Validator;


class DependentDropdownController extends Controller
{

    public function getdistrict($id)
    {   
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $district = MstFedDistrict::where('province_id', $id)->whereRaw("id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)")->get();
            return response()->json($district);
        }
    }
    public function getlocal_level($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $local_level = MstFedLocalLevel::where('district_id', $id)->where("is_tmpp_applicable", true)->get();
            return response()->json($local_level);
        }
       
    }
    public function getproject($id)
    {
       
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $project_id = PtProject::where('client_id', $id)->get();
           return response()->json($project_id);
        }
    }
    public function getsub_category($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $sub_category = MstProjectSubCategory::where('project_category_id', $id)->get();
             return response()->json($sub_category);
        }
    }

    public function getunittype($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $unittype = MstUnit::leftjoin('mst_unit','pt_project.unit_type','=','mst_unit.id')->where('pt_project.id',$id)->get();
            return response()->json($unittype);
        }
    }

    public function getTimeofreport($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            
            if ($id == 1) {
                $Time_of_report = MstNepaliMonth::all();
            } elseif ($id == 2) {
                $Time_of_report = MstNepaliMonth::where('is_quarterly', '1')->get();
            } elseif ($id == 3) {
                $Time_of_report = MstNepaliMonth::where('is_yearly', '1')->get();
            } elseif ($id == 4) {
                $Time_of_report = MstNepaliMonth::where('is_halfyearly', '1')->get();
            }
            return response()->json($Time_of_report);
        }
    }

    public function getapp_client($district_id)
    {

        $validator = Validator::make(['district_id' => $district_id], [
            'district_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            abort(404);
        }else {
            $app_client = DB::table('app_client')->select('app_client.*')
            ->join('mst_fed_local_level', 'mst_fed_local_level.id', 'app_client.fed_local_level_id')
            ->whereRaw('mst_fed_local_level.district_id = ?', $district_id)
            ->orderBy('mst_fed_local_level.lmbiscode')
            ->get();
            return response()->json($app_client);
        }
    }
}
