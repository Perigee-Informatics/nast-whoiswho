<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstUnit;
// use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Models\AppSetting;
use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use App\Models\MstProjectStatus;
use App\Models\PtSelectedProject;
use App\Models\MstProjectCategory;
use App\Http\Requests\PtSelectedProjectRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PtSelectedProjectCrudController extends BaseCrudController
{
    protected $user;
    protected $mode;

    public function setup()
    {
        $this->user = backpack_user();
        CRUD::setModel(PtSelectedProject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/ptselectedproject');
        CRUD::setEntityNameStrings('आयोजना', 'आयोजना');
        $this->setUpLinks(['edit']);
        $this->setCustomTabLinks();
        $this->mode = $this->crud->getActionMethod();
        $this->checkPermission([
            'super_admin' => ['list', 'create', 'update', 'delete', 'export', 'print'],
            'central_admin' => ['list','update','export', 'print'],
            'central_operator' => ['list','create', 'update', 'export', 'print'],
            'central_viewer' => ['list', 'update', 'export', 'print'],
            'locallevel_admin' => ['list','update','export', 'print'],
            'locallevel_operator' => ['list'], 
        ]);
        $this->setFilters();
        $this->crud->enableExportButtons();
        if(request()->has('fiscal_year_id')){
            $this->crud->query->where('fiscal_year_id',request()->fiscal_year_id);
        }else{
            $fiscal_year_id = AppSetting::where('client_id',$this->user->client_id)->pluck('fiscal_year_id')->first();
            if($fiscal_year_id){
                $this->crud->query->where('fiscal_year_id',$fiscal_year_id);
            }
        }
    }

    protected function setCustomTabLinks()
    {
        $this->data['selected_tab'] = "";
        $this->data['work_in_progress_tab'] = "";
        $this->data['completed_tab'] = "";
        $this->data['selected_not_started_tab'] = "";
        // $this->data['all_tab'] = "";
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
            case 'selected_not_started':
                $this->data['selected_not_started_tab'] = "disabled active";
                $this->crud->query->where('project_status_id',2);
            break;

            // case 'all':
            //     $this->data['all_tab'] = "disabled active";
            //     $this->crud->query->whereIn('project_status_id',[2,3,4]);
            // break;

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
        $province_district = NULL;
        if(!backpack_user()->isClientUser()){
            $province_district =    [
                'name'=>'province_district',
                'type'=>'model_function',
                'label' => trans('प्रदेश <br>(जिल्ला)'),
                'function_name' => 'provinceDistrict'
            ];

        }
        $cols = [
            $this->addRowNumberColumn(),
            $province_district,
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
        CRUD::setValidation(PtSelectedProjectRequest::class);
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

            $project_status = [
                'name' => 'project_status_id',
                'type' => 'hidden',
                'value' =>2
            ];
           
        }

        $arr = [
            $this->addClientIdField(),
            $lmbis_code,
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
                'label' => trans('आयोजना'),
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
            $this->addRemarksField(),
        ];

        $arr = array_filter($arr);

        // if logged in user is client user then, make all fields disabled
        if($this->mode === 'edit' && $this->user->isClientUser()){
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
   
}
