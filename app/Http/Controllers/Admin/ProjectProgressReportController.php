<?php
namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use App\Models\MstReportingInterval;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class ProjectProgressReportController extends BaseCrudController
{
    public function index()
    {
        $project_province = DB::select('select id, code,name_en,name_lc from mst_fed_province as p	WHERE id in (SELECT distinct province_id FROM mst_fed_district where id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)) order by code asc');
        $project_interval = MstReportingInterval::all();
        $fiscal_year = MstFiscalYear::all();
        $fiscal_year_id = AppSetting::where('client_id',backpack_user()->client_id)->pluck('fiscal_year_id')->first();

        return view('reports.project_progress.project_progress_filter', compact('project_province', 'project_interval','fiscal_year','fiscal_year_id'));
    }

    public function getReportData(Request $request)
    {
        $params = [];
        $wheres = [];

        $sql = "SELECT p.name_lc,pp.financial_progress_amount,
        pp.financial_progress_percent,pp.physical_progress_percent,p.fiscal_year_id,mfy.code as fiscal_year_name,
        pr.name_lc province,d.name_lc district,ll.name_lc local_level,ac.id client_id,
        mri.name_lc as reporting_interval
        FROM pt_selected_project p
        LEFT JOIN pt_project_progress pp ON p.id = pp.selected_project_id
        LEFT JOIN app_client ac on ac.id = p.client_id
        LEFT JOIN mst_fed_local_level ll on ac.fed_local_level_id = ll.id
        LEFT JOIN mst_fed_district d on ll.district_id = d.id
        LEFT JOIN mst_fed_province pr on d.province_id= pr.id
        LEFT JOIN mst_fiscal_year mfy on p.fiscal_year_id=mfy.id
        LEFT JOIN mst_reporting_interval mri on mri.id = pp.reporting_interval_id
        where p.deleted_uq_code = 1 and pp.deleted_uq_code =1";
        

        $province = $request->province;
        $district = $request->district;
        $local_level = $request->local_level;
        $reporting_interval_id = $request->reporting_interval_id;
        $fiscal_year = $request->fiscal_year;
        $progress_report_summary = $request->progress_report_summary;
        $client_id = backpack_user()->client_id;

        if (!empty($fiscal_year)) {
            if($fiscal_year !== 'all'){
             $wheres[] =  'and p.fiscal_year_id = '. $fiscal_year;
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

        if (!empty($progress_report_summary)) {
            if($progress_report_summary === '2'){
                $wheres[] =  'and p.id not in (select selected_project_id from pt_project_progress)';
            }else{
                $wheres[] =  'and p.id in (select selected_project_id from pt_project_progress)';
            }   
        }

        if (!empty($reporting_interval_id)) {
                $wheres[] =  'and pp.reporting_interval_id = ' .$reporting_interval_id;
        }
           
                $where_clause = implode(" ", $wheres);
                $sql .= $where_clause;
                $data = DB::select($sql);

        Session::put('data', $data);
        return view('reports.project_progress.project_progress_report_data', compact('data'));

    }   

    public function generatePdf(Request $request)
    {
        $data = Session::get('data');
        if ($request->has('download_pdf')) {
            $html = view('reports.project_progress.pdf_progress_report', compact('data'))->render();
            \App\Base\Helpers\PdfPrint::printPortrait($html, "Progress Report.pdf");  
        }
    }

    public function generateExcel(Request $request)
    {
        $data = Session::get('data');
        if ($request->has('download_xls')) {
            $sheet =  new \App\Exports\ReportExport('reports.project_progress.excel_progress_report', compact('data'));
            ob_end_clean();
            ob_start();
            return Excel::download($sheet, 'progress_report.xlsx');
        }
    }
}
