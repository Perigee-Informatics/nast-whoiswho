<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\MstUnit;
use App\Models\PtProject;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\MstFiscalYear;
use App\Models\MstFedProvince;
use App\Base\BaseCrudController;
use App\Models\MstProjectStatus;
use App\Models\PtSelectedProject;
use App\Models\MstProjectCategory;
use Illuminate\Support\Facades\DB;
use App\Models\MstProjectSubCategory;
use App\Models\MstExecutingEntityType;
use App\Http\Requests\PtProjectRequest;
use App\Notifications\NewProjectCreate;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PtProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PtProjectCrudController extends BaseCrudController
{
    protected $user;
    protected $mode;
    protected $route;
    protected $langfile;
    protected $is_allowed;


    public function setup()
    {
        $route = 'admin/ptproject';
        $this->user = backpack_user();

        CRUD::setModel(PtProject::class);
        CRUD::setEntityNameStrings(trans('project.title_text'), trans('project.add_text'));
        $this->setUpLinks(['edit']);
        $this->setCustomTabLinks();

        $langfile = 'project';

        $this->_setup($route,$langfile);
        $this->checkPermission([
            'super_admin' => ['list', 'create', 'update', 'delete', 'export', 'print'],
            'central_admin' => ['list','create','update','export', 'print'],
            'central_operator' => ['list','create', 'update', 'export', 'print'],
            'central_viewer' => ['list', 'update', 'export', 'print'],
            'locallevel_admin' => ['list','update','export', 'print'],
            'locallevel_operator' => ['list'], 
        ]);
        $this->crud->query->whereIn('project_status_id',[2,3,4]);


    }


    public function _setup($route, $langfile)
    {
        $this->route = $route;
        $this->langfile = $langfile;

        
        CRUD::setRoute($this->route);
        $this->is_allowed = AppSetting::where('client_id',1000)->pluck('allow_new_project_demand')->first();
     

        $this->setFilters();
        $this->mode = $this->crud->getActionMethod();

        if(request()->has('fiscal_year_id')){
            $this->crud->query->where('fiscal_year_id',request()->fiscal_year_id);
        }else{

            $fiscal_year_id = AppSetting::where('client_id',$this->user->client_id)->pluck('fiscal_year_id')->first();
            if($fiscal_year_id){
                $this->crud->query->where('fiscal_year_id',$fiscal_year_id);
            }
        }

        // remove save buttons from form
        if($this->user->hasRole('locallevel_admin')){
            if(!$this->is_allowed){
                $this->crud->denySave = true;
            }
        }
    }

    public function tabLinks()
    {
        return $this->setPtProjectTabs();
    }

    protected function setCustomTabLinks()
    {
            
        $this->data['selected_tab'] = "";
        $this->data['work_in_progress_tab'] = "";
        $this->data['completed_tab'] = "";
        $this->data['all_tab'] = "";
        $this->data['list_tab_header_view'] = 'admin.tab.project_status_tab';
    
        $tab = $this->request->status;
        switch ($tab) {
            case 'selected':
                $this->data['selected_tab'] = "disabled active";
                $this->crud->query->whereIn('project_status_id',[2,3,4]);
            break;

            case 'work_in_progress':
                $this->data['work_in_progress_tab'] = "disabled active";
                $this->crud->query->where('project_status_id',3);
            break;

            case 'completed':
                $this->data['completed_tab'] = "disabled active";
                $this->crud->query->where('project_status_id',4);
            break;

            case 'all':
                $this->data['all_tab'] = "disabled active";
                $this->crud->query->whereIn('project_status_id',[2,3,4]);
            break;

            default:
                $this->data['selected_tab'] = "disabled active";
                $this->crud->query->whereIn('project_status_id',[2,3,4]);
            break;
        }


    }

    public function setFilters()
    {
        $this->addProvinceIdFilter();
        $this->addDistrictIdFilter();
        $this->addClientIdFilter();
        $this->crud->addFilter(
            [ 
                'label' => trans('आयोजना / कार्यक्रमको नाम'),
                'type' => 'text',
                'name' => 'name_lc', 
            ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'name_lc', 'iLIKE', '%'.$value.'%');
            }
        );
        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => trans('project.category_id'),
                'type' => 'select2',
                'name' => 'category_id', // the db column for the foreign key
            ],
            function () {
                return (new MstProjectCategory())->getFilterComboOptions();
            },
            function ($value) { 
                $this->crud->addClause('where', 'category_id', $value);
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
        if($this->user->hasAnyRole(['locallevel_admin'])){
            if(!$this->is_allowed){
                $this->crud->removeButton('update');
                $this->crud->removeButton('delete');
                $this->crud->removeButton('create');
            }
        }
        
        $province_district = NULL;
        if(!backpack_user()->isClientUser()){
            $province_district =    [
                'name'=>'province_district',
                'type'=>'model_function',
                'label' => trans('प्रदेश <br> जिल्ला'),
                'function_name' => 'provinceDistrict'
            ];

        }
        $cols = [
            $this->addRowNumberColumn(),
            // $this->addCodeColumn(),
            $province_district,
            // $this->addClientColumn(),
          
            [
                'name'=>'client_name',
                'type'=>'model_function',
                'label' => 'स्थानीय तह<br> ( Code )',
                'function_name' => 'clientName'
            ],
            [
                'name'=>'name_lc',
                'type'=>'model_function',
                'label' => 'आयोजना / कार्यक्रमको नाम',
                'function_name' => 'nameLc'
            ],
            [
                // Select
                'label' => trans('project.category_id'),
                'type' => 'select',
                'name' => 'category_id', // the db column for the foreign key
                'entity' => 'categoryEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstProjectCategory::class,
            ],

            [
                'name' => 'source_federal_amount',
                'label' => trans('project.source_federal_amount'),
                'type' => 'nepali_number_amount',
                'prefix' => 'रु. ',
                'style'=>'color:blue; font-size:11px;'
            ],
            [
                'name' => 'source_local_level_amount',
                'label' => trans('स्थानीय तह <br> साझेदारी'),
                'type' => 'nepali_number_amount',
                'prefix' => 'रु. ',
                'style'=>'color:blue; font-size:11px;'
            ],
            [
                'name' => 'source_donar_amount',
                'label' => trans('project.source_donar_amount'),
                'type' => 'nepali_number_amount',
                'prefix' => 'रु. ',
                'style'=>'color:blue; font-size:11px;'
            ],
            [
                'name' => 'project_cost',
                'label' => trans('project.project_cost'),
                'type' => 'nepali_number_amount',
                'prefix' => 'रु. ',
                'style'=>'color:blue; font-size:11px;'
            ],
            [
                'label' => trans('इकाई'),
                'type' => 'select',
                'name' => 'unit_type', // the db column for the foreign key
                'entity' => 'unitTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstUnit::class,
            ],
            [
                'name' => 'project_affected_population',
                'label' => trans('project.Project Affect Population'),
                'type' => 'number',
                'style' => 'font-size:11px;'
            ],
            $this->addFiscalYearColumn(),
           
        ];
        $cols = array_filter($cols);
        $this->crud->addColumns($cols);
        $this->crud->addClause('where', 'deleted_uq_code',1);

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PtProjectRequest::class);
        $fiscal_year = [
                'name' => 'fiscal_year',
                'type' => 'text',
                'fake'=>true,
                'label' => trans('common.fiscal_year'),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=>[
                    'readonly' => 'readonly',
                ],
                'default' => get_next_fiscal_year(),
            ];
        if($this->mode === 'edit'){
            $lmbis_code = [
                'name' => 'lmbiscode',
                'label' => trans('स्थानीय तह कोड'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id'=>'lmbis-code',
                    'readonly' => true,
                ],
            ];

            $description_lc = [
                'name' => 'description_lc',
                'label' => trans('project.description_lc'),
                'type' => 'text',
                'attributes' => [
                    'id'=>'description-lc',
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ];

            $fiscal_year = [
                'label' => trans('आर्थिक वर्ष'),
                'type' => 'select2',
                'name' => 'fiscal_year_id', // the db column for the foreign key
                'entity' => 'fiscalYearEntity', // the method that defines the relationship in your Model
                'attribute' => 'code', // foreign key attribute that is shown to user
                'model' => MstFiscalYear::class,
                'options' => (function ($query) {
                    return MstFiscalYear::selectRaw("code, id")->orderBy('id','desc')->get();
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ]
            ];

            /**
             * check route !
             * if route is new_project, and logged in user is client user then hide project status field else shoe field with option
             * if route is prproject, and logged in user is client_user show (wip and complete option) else, show all options
             */
            if($this->route === 'admin/newproject'){
                $project_status=NULL;
               

                if(!$this->user->isClientUser()){
                    $project_status = [
                        'label' => trans('project.Project Status'),
                        'type' => 'select2',
                        'name' => 'project_status_id', // the db column for the foreign key
                        'entity' => 'projectStatusEntity', // the method that defines the relationship in your Model
                        'attribute' => 'name_lc', // foreign key attribute that is shown to user
                        'model' => MstProjectStatus::class,
                        'placeholder' => 'आयोजना अवस्था छान्नुहोस्',
                        'options' => (function ($query) {
                            return MstProjectStatus::selectRaw("code|| ' - ' || name_lc as name_lc, id")->whereIn('id',[1,2])->get();
                        }),
                        'wrapper' => [
                            'class' => 'form-group col-md-4'
                        ]
                    ];
                }
            }else{
                if($this->user->isClientUser()){
                    $project_status = [
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
                    ];
                }else{
                    $project_status = [
                        'label' => trans('project.Project Status'),
                        'type' => 'select2',
                        'name' => 'project_status_id', // the db column for the foreign key
                        'entity' => 'projectStatusEntity', // the method that defines the relationship in your Model
                        'attribute' => 'name_lc', // foreign key attribute that is shown to user
                        'model' => MstProjectStatus::class,
                        'placeholder' => 'आयोजना अवस्था छान्नुहोस्',
                        'options' => (function ($query) {
                            return MstProjectStatus::selectRaw("code|| ' - ' || name_lc as name_lc, id")->get();
                        }),
                        'wrapper' => [
                            'class' => 'form-group col-md-4'
                        ]
                    ];
                }
            }

        }else{
            $lmbis_code = [
                'name' => 'lmbiscode',
                'label' => trans('स्थानीय तह कोड'),
                'type' => 'number',
                'value' => ($this->user->isClientUser() === true) ? ($this->user->clientEntity->fedLocalLevelEntity->lmbiscode) : '',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id'=>'lmbis-code',
                    'readonly' => true,
                ],
            ];
            $description_lc = [
                'name' => 'description_lc',
                'label' => trans('project.description_lc'),
                'type' => 'text',
                'value' => ($this->user->isClientUser() === true) ? ($this->user->clientEntity->fedLocalLevelEntity->name_lc) : '',
                'attributes' => [
                    'id'=>'description-lc',
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ];

            if(!$this->user->isClientUser()){
                $fiscal_year = [
                    'label' => trans('आर्थिक वर्ष'),
                    'type' => 'select2',
                    'name' => 'fiscal_year_id', // the db column for the foreign key
                    'entity' => 'fiscalYearEntity', // the method that defines the relationship in your Model
                    'attribute' => 'code', // foreign key attribute that is shown to user
                    'model' => MstFiscalYear::class,
                    'options' => (function ($query) {
                        return MstFiscalYear::selectRaw("code, id")->orderBy('id','desc')->get();
                    }),
                    'wrapper' => [
                        'class' => 'form-group col-md-4'
                    ]
                ];
            }

            /**
             * check route !
             * if route is new_project, set project status_id = 1
             * if route is ptproject, set project_status_id =2
             */
            if($this->route === 'admin/newproject'){
                $project_status = [
                    'name' => 'project_status_id',
                    'type' => 'hidden',
                    'value' =>1
                ];
            }else{
                $project_status = [
                    'name' => 'project_status_id',
                    'type' => 'hidden',
                    'value' =>2
                ];
            }
           
        }

        $arr = [
            $this->addClientIdField(),
            $lmbis_code,
            $fiscal_year,
            // $this->addFiscalYearField(),
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend1',
                'type' => 'custom_html',
                'value' => '<legend>आयोजना विवरण</legend><hr class="m-0">',
            ],
            [
                'name' => 'name_lc',
                'label' => trans($this->langfile.'.name_lc'),
                'type' => 'text',
                'attributes'=>[
                    'id' => 'name-lc',
                    'required' => 'required',
                    'max-length'=>200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
           $description_lc,
            [
                // Select
                'label' => trans('project.category_id'),
                'type' => 'select2',
                'name' => 'category_id', // the db column for the foreign key
                'entity' => 'categoryEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstProjectCategory::class,
                'placeholder' => 'आयोजना क्षेत्र छान्नुहोस्',
                'options' => (function ($query) {
                    return (new MstProjectCategory())->getFieldComboOptions($query);
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ]
            ],
            $project_status,
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend3',
                'type' => 'custom_html',
                'value' => '<legend>भैतिक लक्ष्य</legend><hr class="m-0">',
            ],

            [
                'name' => 'quantity',
                'label' => trans('project.quantity'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                // Select2_from_ajax
                'label' => trans('project.unit_type'),
                'type' => 'select2_from_ajax',
                'name' => 'unit_type', // the db column for the foreign key
                'entity' => 'unitTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstUnit::class,
                'data_source' => url("api/get_units/category_id"),
                'placeholder' => "-- इकाई छान्नुहोस् --",
                'minimum_input_length' => 0,
                'dependencies' => ["category_id"],
                'include_all_form_fields' => true,
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
                'value' => '<legend>लागत विवरण</legend><hr class="m-0">',
            ],
            [
                'name' => 'source_federal_amount',
                'label' => trans('project.source_federal_amount'),
                'type' => 'number',
                'prefix' => 'रु.', 
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ],
                'attributes'=> [
                    'id' => 'source_federal_amount',
                    'onBlur'=>'TMPP.calculateProjectCost()',
                ],
                'default' => 0,
            ],
            [
                'name' => 'source_local_level_amount',
                'label' => trans('project.source_local_level_amount'),
                'type' => 'number',
                'prefix' => 'रु.', 
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ],
                'attributes'=> [
                    'id' => 'source_local_level_amount',
                    'onBlur'=>'TMPP.calculateProjectCost()',
                ],
                'default' => 0,
            ],
            [
                'name' => 'source_donar_amount',
                'label' => trans('project.source_donar_amount'),
                'type' => 'number',
                'prefix' => 'रु.', 
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ],
                'attributes'=> [
                    'id' => 'source_donar_amount',
                    'onBlur'=>'TMPP.calculateProjectCost()',
                ],
                'default' => 0,
            ],
            [
                'name' => 'project_cost',
                'label' => trans('project.project_cost'),
                'type' => 'number',
                'prefix' => 'रु.', 
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ],
                'attributes'=> [
                    'id' => 'project_cost',
                    'readonly'=>true
                ],
                'default' => 0,
            ],
      
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend4',
                'type' => 'custom_html',
                'value' => '<legend>आयोजना लाभ</legend><hr class="m-0">',
            ],
            [
                'name' => 'project_affected_population',
                'label' => trans('project.Project Affect Population'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'project_affected_ward_count',
                'label' => trans('project.Project Affect Ward Count'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'project_affected_wards',
                'label' => trans('project.Project Affect Wards'),
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend10',
                'type' => 'custom_html',
                'value' => '<legend>थप विवरण </legend><hr class="m-0">',
            ],
            [
                'name'=>'has_dpr',
                'label'=>trans('project.has_dpr'),
                'type'=>'radio',
                'default'=>false,
                'inline' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options'=>
                [
                    true=>'भएको',
                    false=>'नभएको',
                ],
            ],

            [
                'name' => 'proposed_duration_months',
                'label' => trans('project.proposed_duration_months'),
                'type' => 'number',
                'suffix'=>'महिना',
                'attributes' => [
                    // 'readonly' => true,
                    // 'id'=>'estimated_duration_months',
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'proposed_end_date_bs',
                'label' => trans('project.estimated_end_date_bs'),
                'type' => 'nepali_date',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id' => 'estimated-end-date-bs',
                ]
            ],

            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend5',
                'type' => 'custom_html',
                'value' => '<legend>GPS Co-ordinates</legend><hr class="m-0">',
            ],
            [
                'name' => 'gps_lat',
                'label' => trans('Latitude'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id' => 'gps_lat',
                    'step' => 'any',
                ],
                'default' => 0,
            ],
            [
                'name' => 'gps_long',
                'label' => trans('Longitude'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id' => 'gps_long',
                    'step' => 'any',
                ],
                'default' => 0,
            ],
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend11',
                'type' => 'custom_html',
                'value' => '<legend>सम्बन्धित कागजात</legend><hr class="m-0">',
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
           



       
            // [
            //     'name' => 'legend6',
            //     'type' => 'custom_html',
            //     'value' => '<legend></legend><hr class="m-2">',
            // ],
            // [ // fake attribute
            //     'name' => '',
            //     'type' => 'toggle_button',
            //     'fake' => 'true',
            //     'value' => 'आयोजना सम्बन्धि अन्य विवरण',
            //     'wrapper' => [
            //         'id' => 'to-hide',
            //     ],
            // ],
            // [
            //     'name' => 'selected_date_bs',
            //     'label' => trans('project.selected_date_bs'),
            //     'type' => 'nepali_date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'selected-date-bs',
            //         'relatedId' => 'selected-date-ad',
            //     ]
            // ],
            // [
            //     'name' => 'selected_date_ad',
            //     'label' => trans('project.selected_date_ad'),
            //     'type' => 'date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'selected-date-ad'
            //     ]
            // ],

            // [
            //     'name' => 'proposed_start_date_bs',
            //     'label' => trans('project.estimated_start_date_bs'),
            //     'type' => 'nepali_date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'estimated-start-date-bs',
            //         'relatedId' => 'estimated-start-date-ad',
            //     ]
            // ],
            // [
            //     'name' => 'proposed_start_date_ad',
            //     'label' => trans('project.estimated_start_date_ad'),
            //     'type' => 'date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'estimated-start-date-ad',
            //         'onChange'=>'TMPP.calculateDuration()',
            //     ]
            // ],
            

            // [
            //     'name' => 'proposed_duration_year',
            //     'label' => trans('project.estimated_duration_year'),
            //     'type' => 'number',
            //     'suffix'=>'वर्ष',
            //     'attributes' => [
            //         // 'readonly' => true,
            //         'id'=> 'estimated_duration_year',
            //     ],
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            // ],
  
            // [
            //     'name' => 'proposed_end_date_bs',
            //     'label' => trans('project.estimated_end_date_bs'),
            //     'type' => 'nepali_date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'estimated-end-date-bs',
            //         'relatedId' => 'estimated-end-date-ad',
            //     ]
            // ],
            // [
            //     'name' => 'proposed_end_date_ad',
            //     'label' => trans('project.estimated_end_date_ad'),
            //     'type' => 'date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'estimated-end-date-ad',
            //         'onChange'=>'TMPP.calculateDuration()',

            //     ]
            // ],
            // [
            //     'name' => 'actual_start_date_bs',
            //     'label' => trans('project.actual_start_date_bs'),
            //     'type' => 'nepali_date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'actual-start-date-bs',
            //         'relatedId' => 'actual-start-date-ad',
            //     ]
            // ],
            // [
            //     'name' => 'actual_start_date_ad',
            //     'label' => trans('project.actual_start_date_ad'),
            //     'type' => 'date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'actual-start-date-ad'
            //     ]
            // ],
            // [
            //     'name' => 'actual_end_date_bs',
            //     'label' => trans('project.actual_end_date_bs'),
            //     'type' => 'nepali_date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'actual-end-date-bs',
            //         'relatedid' => 'actual-end-date-ad',
            //     ]
            // ],
            // [
            //     'name' => 'actual_end_date_ad',
            //     'label' => trans('project.actual_end_date_ad'),
            //     'type' => 'date',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            //     'attributes' => [
            //         'id' => 'actual-end-date-ad'
            //     ]
            // ],
            // [
            //     'name' => 'actual_duration_year',
            //     'label' => trans('project.actual_duration_year'),
            //     'type' => 'number',
            //     'suffix'=>'वर्ष',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            // ],
            // [
            //     'name' => 'actual_duration_months',
            //     'label' => trans('project.actual_duration_months'),
            //     'type' => 'number',
            //     'suffix'=>'महिना',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            // ],
            // [
            //     'name' => 'actual_duration_days',
            //     'label' => trans('project.actual_duration_days'),
            //     'type' => 'number',
            //     'suffix'=>'दिन',
            //     'wrapper' => [
            //         'class' => 'form-group1 col-md-3',
            //     ],
            // ],

            // [
            //     'name' => 'legend7',
            //     'type' => 'custom_html',
            //     'value' => '<legend></legend><hr class="m-2">',
            // ],
           
            $this->addRemarksField(),

        ];

        $arr = array_filter($arr);

        // if logged in user is client user then, make all fields disabled
        if($this->mode === 'edit' && $this->route === 'admin/ptproject' && $this->user->isClientUser()){
            foreach($arr as $field){
                if(array_key_exists('attributes',$field)){
                    $field['attributes']['disabled'] = "disabled";
                    $field['attributes']['style'] = "pointer-events:none";
                    $field['wrapper']['style']= "pointer-events:none";
                    
                    $fields[] = $field;
                }else{
                    $field['wrapper']['style']= "pointer-events:none";
                    $fields[] = $field;
                }
            }
            $arr = $fields;
            $this->crud->denySave = true;

        }
        $this->crud->addFields($arr);
    }

   
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        //// For get the upcoming fiscalyear
        if($request->fiscal_year_id == null){
            $current_fiscal_year = get_current_fiscal_year();
            $fiscal_year_id = MstFiscalYear::where('code',$current_fiscal_year)->pluck('id')->first();
            $project_create_fiscal_year_id = MstFiscalYear::where('id','>', $fiscal_year_id)->limit(1)->pluck('id')->first();
            $request->request->set('fiscal_year_id', $project_create_fiscal_year_id);
        };
       
        // insert item in the db
        $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'http_referrer']));
        if(backpack_user()->isClientUser()){
            $this->user->notify(new NewProjectCreate($item));
        }

        $this->data['entry'] = $this->crud->entry = $item;


        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
        
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        ///For create and update into new table after the project is selected
        $project_status = $request->project_status_id;
        if($project_status === '2'){
            $project_id_exist = PtSelectedProject::where('project_id',$request->id)->first();
            $fiscal_year_id = PtProject::findorFail($request->id)->fiscal_year_id;

            $data = [
                'project_id' => $request->id,
                'client_id' => $request->client_id,
                'fiscal_year_id' => $fiscal_year_id,
                'lmbiscode' => $request->lmbiscode,
                'name_lc' => $request->name_lc,
                'description_lc' => $request->description_lc,
                'category_id' => $request->category_id,
                'project_status_id' => $request->project_status_id,
                'quantity' => $request->quantity,
                'unit_type' => $request->unit_type,
                'source_federal_amount' => $request->source_federal_amount,
                'source_local_level_amount' => $request->source_local_level_amount,
                'source_donar_amount' => $request->source_donar_amount,
                'project_cost' => $request->project_cost,
                'project_affected_population' => $request->project_affected_population,
                'project_affected_ward_count' => $request->project_affected_ward_count,
                'project_affected_wards' => $request->project_affected_wards,
                'has_dpr' => $request->has_dpr,
                'proposed_duration_months' => $request->proposed_duration_months,
                'proposed_end_date_bs' => $request->proposed_end_date_bs,
                'gps_lat' => $request->gps_lat,
                'gps_long' => $request->gps_long,
                'remarks' => $request->remarks,
                'created_by' => backpack_user()->id,
                'created_at' => Carbon::now()->todatetimestring(),
                'updated_at' => Carbon::now()->todatetimestring(),
            ];
            if(!is_null($project_id_exist)){
                $item = PtSelectedProject::where('project_id',$request->id)->update($data);
                \Alert::success(trans('backpack::crud.update_success'))->flash();
                return redirect(backpack_url('/newproject'));
            }else{
                $item = PtSelectedProject::create($data);
            }
        }else{
            $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest());
            $this->data['entry'] = $this->crud->entry = $item;
        }
        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    public function getDetailsById(Request $request){
        $project_id = $request->project_id;
        $project_source_federal_amount = PtProject::findOrFail($project_id)->source_federal_amount;

        return response()->json($project_source_federal_amount);
    }


}
