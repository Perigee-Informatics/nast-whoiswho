<?php

namespace App\Http\Controllers\Admin;

use App\Models\PtProject;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Session;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Http\Controllers\Admin\Charts\ProjectByStatusChartController;
use App\Http\Controllers\Admin\Charts\ProjectByCategoryChartController;
use App\Http\Controllers\Admin\Charts\ProjectByProvinceChartController;
use App\Http\Controllers\Admin\Charts\ProjectByCategoryCostChartController;
use App\Http\Controllers\Admin\Charts\ProjectByProvinceCostChartController;

/**
 * Class DashboardCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class Old_DashboardCrudController extends BaseCrudController
{
    protected $user;
    public function index()
    {
        $this->user = backpack_user();
        $fiscal_year = MstFiscalYear::all();
        $fiscal_year_status = true;
        if(isset(request()->fiscal_year_id)){
            $fiscal_year_id = request()->fiscal_year_id;
            if($fiscal_year_id === 'all'){
                $fiscal_year_status = false;
            }
        }else{
            $fiscal_year_id = AppSetting::where('client_id',$this->user->client_id)->pluck('fiscal_year_id')->first();
        }

        if($fiscal_year_status){
            Session::put('fiscal_year_id', $fiscal_year_id);
            $this->data['fiscal_year_id'] = $fiscal_year_id;
        }else{
            Session::put('fiscal_year_id', null);
        }
        $this->data['fiscal_year'] = $fiscal_year;

        //datas for widgets

        $f_y_id = Session::get('fiscal_year_id');

        //for all  fiscal_year\
        if(backpack_user()->isClientUser()){
            $new_projects = $this->user->clientEntity->clientProjectsDemand;
            $selected_projects = $this->user->clientEntity->clientProjectsSelected;
            $wip_projects = $this->user->clientEntity->clientProjectsWip;
            $completed_projects = $this->user->clientEntity->clientProjectsComplete;
            $total_projects = $this->user->clientEntity->clientProjects;
        }
        else{
            $new_projects = PtProject::where('project_status_id',1);
            $selected_projects = PtProject::where('project_status_id',2);
            $wip_projects = PtProject::where('project_status_id',3);
            $completed_projects = PtProject::where('project_status_id',4);
            $total_projects = PtProject::whereIn('project_status_id',[2,3,4]);
        }
        
        if(isset($f_y_id)){
            $new_projects = $new_projects->where('fiscal_year_id',$f_y_id);
            $selected_projects = $selected_projects->where('fiscal_year_id',$f_y_id);
            $wip_projects = $wip_projects->where('fiscal_year_id',$f_y_id);
            $completed_projects = $completed_projects->where('fiscal_year_id',$f_y_id);
            $total_projects = $total_projects->where('fiscal_year_id',$f_y_id);
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
         
        $projectByProvince = [
            'type'       => 'chart',
            'controller' => ProjectByProvinceChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid green; border-bottom:5px solid lightgray; border-radius:20px;',

            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                 'header' => 'Project By Province', 
            ],
        ];

        $projectByCategory = [
            'type'       => 'chart',
            'controller' => ProjectByCategoryChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid blue; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                'header' => 'Project By Category', 
            ],
        ];

        $projectByProvinceCost = [
            'type'       => 'chart',
            'controller' => ProjectByProvinceCostChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid orange; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                 'header' => 'Project By Cost (Province)', 
            ],
        ];
        $projectByCategoryCost = [
            'type'       => 'chart',
            'controller' => ProjectByCategoryCostChartController::class,
            'class'   => 'card mb-2',
            'style' => 'border-top:5px solid brown; border-bottom:5px solid lightgray; border-radius:20px;',
            'wrapper' => ['class'=> 'col-md-6 text-center font-weight-bold mb-3'] ,
            'content' => [
                 'header' => 'Project By Cost (Category)', 
            ],
        ];

        if(backpack_user()->isClientUser()){
            $content = [
                $projectByStatus,
                $projectByCategory,
                $projectByCategoryCost,
            ];
        }else{
            $content = [
                $projectByStatus,
                $projectByProvince,
                $projectByCategory,
                $projectByProvinceCost,
                $projectByCategoryCost,

            ];
        }

        $widgets['after_content'][] = [
            'type' => 'div',
            'class' => 'row m-2',
            'content' => $content
        ];

          $this->data['widgets'] = $widgets;
        return view('admin.old_dashboard',$this->data);
    }

  
}
