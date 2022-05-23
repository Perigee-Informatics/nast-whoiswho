<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Base\BasePivotController;
use Illuminate\Support\Facades\DB;

class ProjectProgrammePivotReport extends BasePivotController
{
    public function index()
    {   
        $this->setUp();
        $this->customScript();
        return $this->fromView('project_program_analysis_pivot');
    }

    public function customScript()
    {
        $this->data['script_js'] = '
        $(function(){
            var dataUrl,masterUrl;
            $.ajax({
              url: "projectprogramanalysis",
              cache: false,
              success: function(data){
                  dataUrl = data;
                        $.ajax({
                          url: "masterdata",
                          cache: false,
                          success: function(data){
                            masterUrl = data;  
                            pivotTableWorking.loadAndRender({
                                  pivotDataUrl : dataUrl, 
                                  masterDataUrl : masterUrl,
                                  rows : ["आयोजना क्षेत्र"],
                                  cols : ["प्रदेश"]
                              });
                              }
                        });                
                  }
            });
          });

        window.pivotTableWorking = (function(params) {
            var getWorkingDerivedMapping = function() {
                var opt = {
                    "आर्थिक वर्ष": { master: "fiscal_year", "data_field": "fiscal_year_id" },
                    "अवस्था": { master: "project_status", "data_field": "project_status_id" },
                    "जिल्ला": { master: "fed_district", "data_field": "district_id" },
                    "स्थानीय तह": { master: "fed_local_level", "data_field": "local_level_id" },
                    "आयोजना क्षेत्र": { master: "project_category", "data_field": "project_category_id" },
                    "कार्यक्रम संचालन प्रकृया": { master: "executing_entity_type", "data_field": "executing_entity_type_id" },
                };
                return opt;
            };
            var getLabels = function() {
                var opt =  {
                    "project_cost":"आयोजना/कार्यक्रम लागत",
                    "source_federal_amount":"केन्द्रीय अनुदान रकम",
                    "source_local_level_amount":"स्थानीय तह लागत साझेदारी",
                    "source_federal_percent":"केन्द्रीय अनुदान प्रतिसत (%)",
                    "source_local_level_percent":"स्थानीय तह साझेदारी प्रतिसत (%)",
                    "project_affected_population":"लाभान्वित जनसंख्या",
                    "province":"प्रदेश"
                };
                return opt;
            };

            var loadAndRender = function(params) {
                params.hiddenAttributes = params.hiddenAttributes || [];
                params.hiddenAttributes.push("id");
                params.derivedAttributes = params.derivedAttributes || {};
                params.derivedMapping = params.derivedMapping || {};

                params.derivedMapping = params.derivedMapping || {};
                $.each(getWorkingDerivedMapping(), function (key,item) {
                    params.derivedMapping[key] = item;
                });

                params.rows = params.rows || [];
                params.cols = params.cols || [];

                
                if (params.rows.length == 0) {
                }
                
                params.labels = getLabels();
                pivotTableHelper.loadAndRender(params);
            };
            return {
                loadAndRender: loadAndRender
            }

        })();';
    }

    public function getPivotData() 
    {
        echo "id,source_federal_amount,source_local_level_amount,project_cost,source_federal_percent,source_local_level_percent,project_affected_population,fiscal_year_id,project_status_id,district_id,local_level_id,project_category_id,executing_entity_type_id,province" . "\r\n";

        $pivotdata = DB::select(DB::raw("SELECT distinct p.id as id,
            p.source_federal_amount, p.source_local_level_amount, p.project_cost,
            p.source_federal_percent, p.source_local_level_percent,
            p.project_affected_population,
            mfy.id as fiscal_year_id,ps.id as project_status_id,
            d.id as district_id,ll.id as local_level_id,
            pc.id as project_category_id,meet.id as executing_entity_type_id,
            concat(pr.code, ' - ', pr.name_lc) as province
            FROM pt_project p
            INNER JOIN pt_selected_project psp on psp.project_id = p.id
            LEFT JOIN pt_project_progress ppp on ppp.selected_project_id = psp.id
            LEFT JOIN mst_fed_local_level ll on p.client_id = ll.id
            LEFT JOIN mst_fed_district d on ll.district_id = d.id
            LEFT JOIN mst_fed_province pr on d.province_id= pr.id
            LEFT JOIN mst_project_category pc on p.category_id = pc.id
            LEFT JOIN mst_project_status ps on p.project_status_id = ps.id
            LEFT JOIN mst_fiscal_year mfy on mfy.id = p.fiscal_year_id 
            LEFT JOIN mst_executing_entity_type meet on meet.id = ppp.executing_entity_type_id
            where p.deleted_uq_code = 1
            "));

        $resp = array();
        foreach ($pivotdata as $entry) {
            $row = array();
            foreach ($entry as $key => $value) {
                array_push($row, $value);
            }
            array_push($resp, implode(',', $row));
        }
        echo implode("\r", $resp);

    }

}
