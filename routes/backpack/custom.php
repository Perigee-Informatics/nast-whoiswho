<?php

use App\Http\Controllers\Admin\DashboardCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.



Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web','XSS', config('backpack.base.middleware_key', 'admin')],
    // 'middleware' => array_merge(
    //     (array) config('backpack.base.web_middleware', 'web'),
    //     (array) config('backpack.base.middleware_key', 'admin'),
    // ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    // primary master routes
    Route::crud('mstfedprovince', 'MstFedProvinceCrudController');
    Route::crud('mstfeddistrict', 'MstFedDistrictCrudController');
    Route::crud('mstfedlocallevel', 'MstFedLocalLevelCrudController');
    Route::crud('mstfedlocalleveltype', 'MstFedLocalLevelTypeCrudController');
    Route::crud('mstfiscalyear', 'MstFiscalYearCrudController');
    Route::crud('mstnepalimonth', 'MstNepaliMonthCrudController');

    //master routes
    Route::crud('mstprojectcategory', 'MstProjectCategoryCrudController');
    Route::crud('mstprojectcategory/{project_category_id}/mstprojectsubcategory', 'MstProjectSubCategoryCrudController');
    Route::crud('mstfundingsource', 'MstFundingSourceCrudController');
    Route::crud('mstexecutingentitytype', 'MstExecutingEntityTypeCrudController');
    Route::crud('mstexecutingentitytype/{executing_entity_id}/mstexecutingentity', 'MstExecutingEntityCrudController');



    //secondary master
    Route::crud('mstreportinginterval', 'MstReportingIntervalCrudController');
    Route::crud('mstprojectstatus', 'MstProjectStatusCrudController');
    Route::crud('mstdesignation', 'MstDesignationCrudController');
    Route::crud('mstroadconnectivitytype', 'MstRoadConnectivityTypeCrudController');
    Route::crud('mstnotetype', 'MstNoteTypeCrudController');

    Route::crud('msttmpprelatedstaff', 'MstTmppRelatedStaffCrudController');
    Route::crud('mstunit', 'MstUnitCrudController');

    // Route::crud('ptproject', 'PtProjectCrudController');
    Route::crud('newproject', 'NewProjectCrudController');
    // Route::crud('ptproject/{project_id}/ptprojectfiles', 'PtProjectFileCrudController');
    // Route::crud('ptproject/{project_id}/ptprojectnotes', 'PtProjectNoteCrudController');

    Route::crud('ptselectedproject', 'PtSelectedProjectCrudController');

    Route::crud('projectprogress', 'PtProjectProgressCrudController');
    Route::crud('projectprogress/{project_progress_id}/ptprojectprogressfiles', 'PtProjectProgressFilesCrudController');
    Route::get('pull-tmpp-data', 'MstFedProvinceCrudController@pullData');
    Route::crud('appclient', 'AppClientCrudController');

    Route::get('appclient/getDetailsById', 'AppClientCrudController@getDetailsById');


    Route::get('ptproject/getDetailsById', 'PtProjectCrudController@getDetailsById');

    
    Route::crud('dashboard', 'DashboardCrudController');
    
    
    Route::get('charts/project-by-status', 'Charts\ProjectByStatusChartController@response');
    Route::get('charts/project-by-province', 'Charts\ProjectByProvinceChartController@response')->name('charts.project-by-province.index');
    Route::get('charts/project-by-category', 'Charts\ProjectByCategoryChartController@response')->name('charts.project-by-category.index');
    Route::get('charts/project-by-province-cost', 'Charts\ProjectByProvinceCostChartController@response')->name('charts.project-by-province-cost.index');
    Route::get('/gisdata', 'GisMapController@gisMapData')->name('gisdata');
    Route::get('/gisfilterdata', 'GisMapController@getGisFilterData');

    Route::get('report/project_pivot', 'ProjectProgrammePivotReport@index');
    Route::get('report/projectprogramanalysis', 'ProjectProgrammePivotReport@getPivotData');
    Route::get('/anusuchi_3', 'AnusuchiReportController@anusuchiThreeIndex')->name('anusuchi_3');
    Route::post('/anushithreereportdata', 'AnusuchiReportController@getAnushiThreeReportData');

    Route::get('/anusuchi_4', 'AnusuchiReportController@anusuchiFourIndex')->name('anusuchi_4');
    Route::post('/anushifourreportdata', 'AnusuchiReportController@getAnushiFourReportData');
    Route::get('/project_progress', 'ProjectProgressReportController@index')->name('projectprogress');
    Route::post('/projectprogressreportdata', 'ProjectProgressReportController@getReportData');
    Route::get('/anusuchi_four_report_data', 'AnusuchiReportController@generateAnusuchiFourReport');
    Route::get('/anusuchi_three_report_data', 'AnusuchiReportController@generateAnusuchiThreeReport');

    Route::get('/projectprogressreportdata/generateprogresspdf', 'ProjectProgressReportController@generatePdf')->name('generateprogresspdf');
    Route::get('/projectprogressreportdata/generateprogressexcel', 'ProjectProgressReportController@generateExcel')->name('generateprogressexcel');

    Route::get('charts/project-by-category-cost', 'Charts\ProjectByCategoryCostChartController@response')->name('charts.project-by-category-cost.index');
    
    Route::crud('appsetting', 'AppSettingCrudController');
    Route::get('/project/{notification_id}/{project_id}', 'NotificationCrudController@getNewAddedData');
    Route::crud('notification', 'NotificationCrudController');


}); // this should be the absolute last line of this file