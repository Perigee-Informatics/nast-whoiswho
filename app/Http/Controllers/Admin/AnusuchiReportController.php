<?php

namespace App\Http\Controllers\Admin;

use App\Models\PtProject;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Base\Helpers\PdfPrint;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Base\BaseCrudController;
use App\Models\MstFedLocalLevel;
use App\Models\MstProjectStatus;
use App\Models\MstProjectCategory;
use Illuminate\Support\Facades\DB;
use App\Models\MstReportingInterval;
use Illuminate\Support\Facades\App; 
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;


class AnusuchiReportController extends BaseCrudController
{
    
    public function anusuchiFourIndex()
    {
        $client_id = backpack_user()->client_id;
        $project_province = DB::select('select id, code,name_en,name_lc from mst_fed_province as p	WHERE id in (SELECT distinct province_id FROM mst_fed_district where id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)) order by code asc');
        $project_interval = MstReportingInterval::all();
        $fiscal_year = MstFiscalYear::all();
        $fiscal_year_id = AppSetting::where('client_id',backpack_user()->client_id)->pluck('fiscal_year_id')->first();
        $project_status = MstProjectStatus::whereIn('id', [2,3,4])->get();
        return view('reports.anusuchi_4.anusuchi_4_filter', compact('client_id', 'fiscal_year', 'project_province', 'project_interval', 'project_status','fiscal_year_id'));

    }

    public function getAnushiFourReportData(Request $request)
    {
        $params = [];
        $wheres = [];

        $sql = "SELECT distinct pp.id, p.name_lc,p.description_lc,
        p.source_local_level_amount,p.project_cost,
        case when pp.executing_entity_type_id = 1 then 'उ.स.'END executing_type_uc,
        case when pp.executing_entity_type_id = 2 then 'ठेक्का'END executing_type_contract,
        case when pp.executing_entity_type_id = 3 then 'अन्य'END executing_type_other,
        mu.name_lc as unit,a.incharge_name,p.quantity,p.weightage,pp.quantity as pragati_quantity,pp.weightage as pragati_weightage,
        null fy_target_physical, null fy_target_financial,null fy_target_budget,
        pp.financial_progress_percent,pp.physical_progress_percent,mfy.code as fiscal_year_name,pp.financial_progress_amount,
        p.source_federal_amount,  p.source_donar_amount, 
        null interval_target_physical, null interval_target_financial,p.project_affected_population, p.remarks,
        p.project_status_id,pr.name_lc province,
        d.name_lc district,ll.name_lc local_level,ac.id client_id,mri.name_lc as reporting_interval
        FROM pt_selected_project p
        INNER JOIN pt_project_progress pp ON p.id = pp.selected_project_id
        LEFT JOIN app_client ac on ac.id = p.client_id
        INNER JOIN app_setting a on a.client_id = ac.id
        LEFT JOIN mst_fed_local_level ll on ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d on ll.district_id = d.id
        LEFT JOIN mst_fed_province pr on d.province_id= pr.id
        LEFT JOIN mst_fiscal_year mfy on p.fiscal_year_id=mfy.id
        LEFT JOIN mst_reporting_interval mri on mri.id = pp.reporting_interval_id
        LEFT JOIN mst_unit mu on mu.id = p.unit_type
        WHERE pp.deleted_uq_code = 1";

        $province = $request->province;
        $district = $request->district;
        $local_level = $request->local_level;
        $reporting_interval = $request->reporting_interval_id;
        $fiscal_year = $request->fiscal_year;
        $data = null;
        $client_id = backpack_user()->client_id;

        if($province != null || $district != null || $local_level != null ||  $reporting_interval != null || $fiscal_year != null || $status != null || $client_id != 1000)
        {
            if (!empty($fiscal_year)) {
                if($fiscal_year !== 'all'){
                    $wheres[] =  'and pp.fiscal_year_id = '. $fiscal_year;
                }
            }
            if (!empty($province)) {
                $wheres[] =  'and d.province_id =' . $province;
            }

            if (!empty($district)) {
                $wheres[] =  'and ll.district_id = '. $district;
            }
            if (!empty($local_level)) {
                $wheres[] =  'and ll.id = ' .$local_level;
            }
            if(backpack_user()->isClientUser()) {
                $wheres[] =  'and p.client_id = ' . $client_id;
            }

            if (!empty($reporting_interval)) {
                $wheres[] =  'and pp.reporting_interval_id = ' .$reporting_interval;
            }
           
                $where_clause = implode(" ", $wheres);
                $sql .= $where_clause;
                $data = DB::select($sql);
        }

        return view('reports.anusuchi_4.anusuchi_4_report_data', compact('data','reporting_interval'));

    }

    public function getReportQuery($request)
    {
        $province = $request->input('province');
        $district = $request->input('district');
        $local_level = $request->input('local_level');
        $reporting_interval = $request->input('reporting_interval_id');
        $fiscal_year = $request->input('fiscal_year_id');
        $status = $request->input('project_status_id');
        $client_id = backpack_user()->client_id;


        $params = [];
        $wheres = [];

        $sql = "SELECT distinct p.id, p.name_lc,p.description_lc,
        p.source_local_level_amount,p.project_cost,
        case when pp.executing_entity_type_id = 1 then 'उ.स.'END executing_type_uc,
        case when pp.executing_entity_type_id = 2 then 'ठेक्का'END executing_type_contract,
        case when pp.executing_entity_type_id = 3 then 'अन्य'END executing_type_other,
        mu.name_lc as unit,a.incharge_name,p.quantity,p.weightage,pp.quantity as pragati_quantity,pp.weightage as pragati_weightage,
        null fy_target_physical, null fy_target_financial,null fy_target_budget,
        pp.financial_progress_percent,pp.physical_progress_percent,mfy.code as fiscal_year_name,pp.financial_progress_amount,
        p.source_federal_amount,  p.source_donar_amount, 
        null interval_target_physical, null interval_target_financial,p.project_affected_population, p.remarks,
        p.project_status_id,pr.name_lc province,
        d.name_lc district,ll.name_lc local_level,ac.id client_id,mri.name_lc as reporting_interval
        FROM pt_selected_project p
        INNER JOIN pt_project_progress pp ON p.id = pp.selected_project_id
        LEFT JOIN app_client ac on ac.id = p.client_id
        INNER JOIN app_setting a on a.client_id = ac.id
        LEFT JOIN mst_fed_local_level ll on ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d on ll.district_id = d.id
        LEFT JOIN mst_fed_province pr on d.province_id= pr.id
        LEFT JOIN mst_fiscal_year mfy on p.fiscal_year_id=mfy.id
        LEFT JOIN mst_reporting_interval mri on mri.id = pp.reporting_interval_id
        LEFT JOIN mst_unit mu on mu.id = p.unit_type
        WHERE p.project_status_id != 1 and p.deleted_uq_code = 1 
        and pp.created_at = (SELECT max(created_at) from pt_project_progress WHERE selected_project_id = p.id)";

            if ($fiscal_year != 'null') {
                if($fiscal_year !== 'all'){
                    $wheres[] =  ' and p.fiscal_year_id = '. $fiscal_year;
                }
            }
            if ($province != 'null' && $province != 'undefined') {
                $wheres[] =  ' and d.province_id =' . $province;
            }

            if ($district != null && $district != 'undefined') {
                $wheres[] =  ' and ll.district_id = '. $district;
            }
            if ($local_level != null && $district != 'undefined') {
                $wheres[] =  ' and ll.id = ' .$local_level;
            }
            if(backpack_user()->isClientUser()) {
                $wheres[] =  ' and p.client_id = ' . $client_id;
            }
            if ($reporting_interval != 'null') {
                $wheres[] =  ' and pp.reporting_interval_id = ' .$reporting_interval;
            }
        
                $where_clause = implode(" ", $wheres);
                $sql .= $where_clause;
                $data = DB::select($sql);
                return $data;
    }

    public function generateAnusuchiFourReport(Request $request)
    {
        $reporting_interval_name = null;
        $local_level_name = null;
        $reporting_interval_id = $request->input('reporting_interval_id');
        $local_level = $request->input('local_level');
        $data = $this->getReportQuery($request);
        if($reporting_interval_id != 'null'){
            $reporting_interval_name = MstReportingInterval::findOrFail($reporting_interval_id)->name_lc;
        }
        if($local_level != null ){
            if(backpack_user()->isClientUser()) {
              $local_level_name = MstFedLocalLevel::findOrFail(backpack_user()->client_id)->name_lc;
            }else{
              $local_level_name = MstFedLocalLevel::findOrFail($local_level)->name_lc;
            }
        }
        if($request->type === 'PDF'){
            $html = view('reports.anusuchi_4.pdf_anusuchi_four', compact('data','reporting_interval_name','local_level_name'))->render();
            \App\Base\Helpers\PdfPrint::printLandscape($html, "Anusuchi_4.pdf"); 
        }elseif($request->type === 'EXCEL')
        {
            $sheet =  new \App\Exports\ReportExport('reports.anusuchi_4.excel_anusuchi_four', compact('data','reporting_interval_name','local_level_name'));
            ob_end_clean();
            ob_start();
            return Excel::download($sheet, 'anusuchi_4.xlsx');
        }
    }

    public function generateAnusuchiThreeReport(Request $request)
    {
        $data = $this->getAnusuchiThreeReportQuery($request);
        $province_name = null;
        $district_name = null;
        $local_level_name = null;
        $province = $request->input('province');
        $district = $request->input('district');
        $local_level = $request->input('local_level');

        if(!backpack_user()->isClientUser()) {
        if($province != 'null'){
            $province_name = MstFedProvince::findOrFail($province)->name_lc;
        }
        if($district != null){
            $district_name = MstFedDistrict::findOrFail($district)->name_lc;
        }
        }
        if($local_level != null ){
            if(backpack_user()->isClientUser()) {
              $local_level_name = MstFedLocalLevel::findOrFail(backpack_user()->client_id)->name_lc;
            }else{
              $local_level_name = MstFedLocalLevel::findOrFail($local_level)->name_lc;
            }
        }

        if($request->type === 'PDF'){
            $html = view('reports.anusuchi_3.pdf_anusuchi_three', compact('data','province_name','district_name','local_level_name'))->render();
            \App\Base\Helpers\PdfPrint::printLandscape($html, "Anusuchi_3.pdf");
        }elseif($request->type === 'EXCEL')
        {
            $sheet =  new \App\Exports\ReportExport('reports.anusuchi_3.excel_anusuchi_three', compact('data','province_name','district_name','local_level_name'));
            ob_end_clean();
            ob_start();
            return Excel::download($sheet, 'anusuchi_3.xlsx'); 
        }

    }

    public function getAnusuchiThreeReportQuery($request)
    {
        $params = [];
        $wheres = [];
        $data = null;
        $sql = "SELECT  p.name_lc,p.description_lc,p.created_at,ll.district_id,ac.fed_local_level_id,
        p.source_local_level_amount,p.quantity,p.project_cost,p.source_local_level_amount,
        mfy.code as fiscal_year_name,p.proposed_duration_months,
        case when p.has_dpr = false then 'नभएको' else 'भएको' END dpr_details,
        p.source_federal_amount,p.source_donar_amount, 
        null interval_target_physical, null interval_target_financial,p.project_affected_population, p.remarks,
        p.project_status_id,pr.name_lc province,
        d.name_lc district,ll.name_lc local_level,ac.id client_id,mpc.name_lc as category_name
        FROM pt_project p
        LEFT JOIN app_client ac on ac.id = p.client_id
        LEFT JOIN mst_fed_local_level ll on ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d on ll.district_id = d.id
        LEFT JOIN mst_fed_province pr on d.province_id= pr.id
        LEFT JOIN mst_fiscal_year mfy on p.fiscal_year_id = mfy.id
        LEFT JOIN mst_project_category mpc on mpc.id = p.category_id 
        WHERE p.project_status_id = 1 and p.deleted_uq_code = 1";

        $province = $request->province;
        $district = $request->district;
        $local_level = $request->local_level;
        $fiscal_year = $request->fiscal_year;
        $project_category_id = $request->project_category_id;
        $client_id = backpack_user()->client_id;

        // dd(is_int($district));
        if($province != null || $district != null || $local_level != null  ||  $fiscal_year != null || $project_category_id != null ||  $client_id != 1000)
        {
            if (!empty($fiscal_year)) {
                if($fiscal_year !== 'all'){
                    $wheres[] =  ' and p.fiscal_year_id = '. $fiscal_year;
                }
            }
            if (!empty($province) && $province != 'null' && $province != 'undefined') {
                $wheres[] =  ' and d.province_id =' . $province;
            }
            if (!empty($district) &&  $district != null  && $district != 'undefined') {
                $wheres[] =  '  and ll.district_id = '. $district;
            }
            if (!empty($local_level) &&  $local_level != null  && $local_level != 'undefined') {
                $wheres[] =  'and ll.id = ' .$local_level;
            }
            if (!empty($project_category_id) &&  $project_category_id != null  && $project_category_id != 'undefined') {
                $wheres[] =  'and p.category_id = ' .$project_category_id;
            }
            if(backpack_user()->isClientUser()) {
                $wheres[] =  ' and p.client_id = ' . $client_id;
            }


            $where_clause = implode(" ", $wheres);
            $sql .= $where_clause;
            $filter_latest_data = "select * from ($sql) as latest_project order by latest_project.district_id,latest_project.fed_local_level_id,latest_project.created_at desc";
            $data = DB::select($filter_latest_data);

            // $sql .= $where_clause;
            // $data = DB::select($sql);
            return $data;
        }

    }

    public function anusuchiThreeIndex()
    {
        $project_province = DB::select('select id, code,name_en,name_lc from mst_fed_province as p	WHERE id in (SELECT distinct province_id FROM mst_fed_district where id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)) order by code asc');
        $fiscal_year = MstFiscalYear::all();
        $fiscal_year_id = AppSetting::where('client_id',backpack_user()->client_id)->pluck('fiscal_year_id')->first();
        $project_category = MstProjectCategory::all();
        return view('reports.anusuchi_3.anusuchi_3_filter', compact('fiscal_year', 'project_province','fiscal_year_id','project_category'));
    }

    public function getAnushiThreeReportData(Request $request)
    {
        $data = $this->getAnusuchiThreeReportQuery($request);
        return view('reports.anusuchi_3.anusuchi_3_report_data', compact('data'));
    }
}
