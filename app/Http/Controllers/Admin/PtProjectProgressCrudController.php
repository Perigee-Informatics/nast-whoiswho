<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\MstUnit;
use App\Models\AppClient;
use App\Models\PtProject;
use App\Models\AppSetting;
use App\Models\MstFiscalYear;
use App\Models\MstDesignation;
use App\Models\MstNepaliMonth;
use App\Base\BaseCrudController;
use App\Models\MstProjectStatus;
use App\Models\PtProjectProgress;
use App\Models\PtSelectedProject;
use App\Models\MstTmppRelatedStaff;
use App\Models\MstReportingInterval;
use App\Models\MstExecutingEntityType;
use App\Http\Requests\PtProjectProgressRequest;
use App\Notifications\NewProjectProgressCreate;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PtProjectProgressCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PtProjectProgressCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
     protected $user;
    public function setup()
    {
        CRUD::setModel(PtProjectProgress::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/projectprogress');
        CRUD::setEntityNameStrings(trans('ptProjectProgress.title_text'), trans('ptProjectProgress.title_text'));
        $this->checkPermission([
            'super_admin' => ['list', 'create', 'update', 'delete', 'export', 'print'],
            'central_admin' => ['list', 'create','update','delete','export', 'print'],
            'central_operator' => ['list','create', 'update', 'export', 'print'],
            'central_viewer' => ['list', 'create','update', 'export', 'print'],
            'locallevel_admin' => ['list','create','update','export', 'print'],
            'locallevel_operator' => ['list','create','update'], 
        ]);
        $this->setUpLinks(['edit']);
        $this->user = backpack_user();
        $this->setFilters();

        if(request()->has('fiscal_year_id')){
            $this->crud->query->where('fiscal_year_id',request()->fiscal_year_id);
        }else{
            $fiscal_year_id = AppSetting::where('client_id',$this->user->client_id)->pluck('fiscal_year_id')->first();
            if($fiscal_year_id){
                $this->crud->query->where('fiscal_year_id',$fiscal_year_id);
            }
        }

    }
    
    // public function tabLinks()
    // {
    //     return $this->setProjectProgressTabs();
    // }

    public function setFilters()
    {
        $this->addProvinceIdFilter();
        $this->addDistrictIdFilter();
        $this->addClientIdFilter();
        $this->crud->addFilter(
            [ 
                'label' => trans('ptProjectProgress.reporting_interval'),
                'type' => 'select2',
                'name' => 'reporting_interval_id', // the db column for the foreign key
            ],
            function () {
                return MstReportingInterval::pluck('name_lc', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'reporting_interval_id', $value);
            }
        );
        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => trans('project.executing_entity_type'),
                'type' => 'select2',
                'name' => 'executing_entity_type_id', // the db column for the foreign key
            ],
            function () {
                return (new MstExecutingEntityType())->getFilterComboOptions();
            },
            function ($value) { 
                $this->crud->addClause('where', 'executing_entity_type_id', $value);
            }
        );

        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => 'आर्थिक वर्ष',
                'type' => 'select2',
                'name' => 'fiscal_year_id', // the db column for the foreign key
            ],
            function () {
                return (new MstFiscalYear())->getCodeFilterOptions();
            },
            function ($value) { 
                $this->crud->addClause('where', 'fiscal_year_id', $value);
            }
        );
    }

    protected function setupListOperation()
    {
        $province_district = NULL;
        if(!backpack_user()->isClientUser()){
            $province_district =    [
                'name'=>'province_district',
                'type'=>'model_function',
                'label' => trans('प्रदेश <br> जिल्ला'),
                'function_name' => 'provinceDistrict'
            ];
        }

        $col = [
            $this->addRowNumberColumn(),
            $province_district,
            $this->addClientColumn(),
            [
                'name'=>'project_name',
                'type'=>'model_function',
                'label' =>'कार्यक्रम / आयोजना',
                'function_name' => 'projectName'
            ],
            [
                'name' => 'reporting_interval_id',
                'type' => 'select',
                'entity'=>'reportingIntervalEntity',
                'attribute' => 'name_lc',
                'model'=>MstReportingInterval::class,
                'label' => trans('प्रतिवेदन अन्तराल'),
            ],
            [
                'label' => trans('इकाई'),
                'type' => 'select',
                'name' => 'unit_type', 
                'entity' => 'unitTypeEntity', 
                'attribute' => 'name_lc', 
                'model' => MstUnit::class,
            ],
            [
                'label' => trans('संचालन'.'<br>'.'प्रक्रिया'),
                'type' => 'select',
                'name' => 'executing_entity_type_id', 
                'entity' => 'executingEntityTypeEntity', 
                'attribute' => 'name_lc', 
                'model' => MstExecutingEntityType::class,
            ],
            [
                'name'=>'date_bs',
                'type'=>'model_function',
                'label' => 'मिति',
                'function_name' => 'fullDate'
            ],
            [
                'label' => trans('वित्तीय'.'<br>'.'प्रगति (रु.)'),
                'type' => 'nepali_number_amount',
                'name' => 'financial_progress_amount',
                'prefix' => 'रु. ',
                'style'=>'color:blue'
            ],
            [
                'label' => trans('वित्तीय'.'<br>'.'प्रगति (%)'),
                'type' => 'number',
                'name' => 'financial_progress_percent',
                'suffix'=>' %',
                'style'=>'color:green'
            ],
            [
                'label' => trans('भौगोलिक'.'<br>'.'प्रगति (%)'),
                'type' => 'number',
                'name' => 'physical_progress_percent',
                'suffix'=>' %',
                'style'=>'color:green'
            ],
            [   // Upload
                'name' => 'file_upload',
                'label' => 'कागजात',
                'type' => 'upload_multiple',
                'upload' => true,
                'disk' => 'uploads', 
            ],


        ];
        $col = array_filter($col);
        $this->crud->addColumns($col);
        $this->crud->addClause('where', 'deleted_uq_code',1);

        if ($this->user->isClientUser()) {
            $this->crud->addClause('where', 'client_id', $this->user->client_id);
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PtProjectProgressRequest::class);
        $date_ad = Carbon::now()->todatestring();
        $date_bs = convert_bs_from_ad();
        $arr = [
            [
                'name' => 'fiscal_year_id',
                'type' => 'select2',
                'entity'=>'fiscalyearEntity',
                'attribute' => 'code',
                'model'=>MstFiscalYear::class,
                'label' => trans('common.fiscal_year'),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            $this->addClientIdField(),
            [
                'name'=>'selected_project_id',
                'label' =>'Project',
                'type'=>'select2_from_ajax',
                'model'=>PtSelectedProject::class,
                'entity'=>'projectEntity',
                'attribute'=>'name_lc',
                'data_source' => url("api/get_project/client_id"),
                'placeholder' => "Select Project",
                'minimum_input_length' => 0,
                'include_all_form_fields' => true,
                'dependencies'         => ['client_id'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=> ['id'=>'project_id']
                
            ],
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend1',
                'type' => 'custom_html',
                'value' => '<b><legend></legend></b><hr class="m-0">',
            ],
           [
            'name' => 'reporting_interval_id',
            'type' => 'select2',
            'entity'=>'reportingIntervalEntity',
            'attribute' => 'name_lc',
            'model'=>MstReportingInterval::class,
            'label' => trans('ptProjectProgress.reporting_interval'),
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes'=>[
                'required' => 'Required',
             ],
            ],
           
            [
                'name' => 'date_bs',
                'type' => 'nepali_date',
                'label' => trans('common.date_bs'),
                'value' => $date_bs,
                'attributes'=>[
                    'id'=>'date_bs',
                    // 'relatedId'=>'date_ad',
                    'maxlength' =>'10',
                ],
                 'wrapper' => [
                     'class' => 'form-group col-md-4',
                ],
            ],
            // [
            //     'name' => 'date_ad',
            //     'type' => 'date',
            //     'value' => $date_ad,
            //     'label' => trans('common.date_ad'),
            //     'attributes'=>[
            //         'id'=>'date_ad',
            //     ],
            //     'wrapper' => [
            //         'class' => 'form-group col-md-3',
            //     ],
            // ],
            [
                'label' => trans('project.executing_entity_type'),
                'type' => 'select2',
                'name' => 'executing_entity_type_id', // the db column for the foreign key
                'entity' => 'executingEntityTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstExecutingEntityType::class,
                'placeholder' => 'कार्यक्रम सन्चालन प्रक्रिया छान्नुहोस्',
                'options' => (function ($query) {
                    return (new MstExecutingEntityType())->getFieldComboOptions($query);
                }),
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'label' => trans('project.Project Status'),
                'type' => 'select2',
                'name' => 'project_status_id', // the db column for the foreign key
                'entity' => 'projectStatusEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstProjectStatus::class,
                'placeholder' => 'आयोजना अवस्था छान्नुहोस्',
                'options' => (function ($query) {
                    return MstProjectStatus::selectRaw("code|| ' - ' || name_lc as name_lc, id")->whereIn('id',[2,3,4])->get();
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ]
                ],
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend2',
                'type' => 'custom_html',
                'value' => '<b><legend>भौतिक प्रबिधि खण्ड</legend></b><hr class="m-0">',
            ],
            [
                'label' =>  trans('ptProjectProgress.financial_progress_amount'),
                'type' => 'number',
                'name' => 'financial_progress_amount',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=> [
                    'id'=>'financial_progress_amount',
                    'step' => 'any',
                    'onKeyUp' => 'TMPP.calculateFinancialPercent()',
                ],
                'prefix'=>'Rs.'
            ],
            [
                'label' =>  trans('ptProjectProgress.financial_progress_percent'),
                'type' => 'number',
                'name' => 'financial_progress_percent',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=>[
                    'id' => 'financial_progress_percent',
                    'step' => 'any',
                    'readonly'=>true,
                ],
                'suffix'=>'%'
            ],
            [
                'label' =>  trans('ptProjectProgress.physical_progress_percent'),
                'type' => 'number',
                'name' => 'physical_progress_percent',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                    'step' => 'any',
                ],
                'suffix'=>'%'
            ],
            [
                'label' =>  trans('ptProjectProgress.quantity'),
                'type' => 'text',
                'name' => 'quantity',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'label' =>  trans('ptProjectProgress.weightage'),
                'type' => 'text',   
                'name' => 'weightage',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => 'readonly'
                ]
            ],
            [   // Upload
                'name' => 'file_upload',
                'label' => 'कागजात',
                'type' => 'upload_multiple',
                'upload' => true,
                'disk' => 'uploads', 
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-12',
                ],
            ],
            [
                'name' => 'prepared_by',
                'type' => 'hidden',
                'value' => $this->user->id
            ],
            [
                'name' => 'submitted_by',
                'type' => 'hidden',
                'value' => $this->user->id
            ],
            [
                'name' => 'approved_by',
                'type' => 'hidden',
                'value' => $this->user->id
            ],
            
        ];
        $arr = array_filter($arr);
        $this->crud->addFields($arr);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $unit_type_id = PtSelectedProject::findOrFail($request->selected_project_id)->unit_type;
        // $request->request->set('created_by', $user_id);
        $request->request->set('unit_type', $unit_type_id);

        $update_project_status = PtSelectedProject::where('id',$request->selected_project_id)->update(['project_status_id'=>$request->project_status_id]);

        // insert item in the db
        $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'http_referrer']));
        if(backpack_user()->isClientUser()){
            $this->user->notify(new NewProjectProgressCreate($item));
        }

        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
        
    }
}
