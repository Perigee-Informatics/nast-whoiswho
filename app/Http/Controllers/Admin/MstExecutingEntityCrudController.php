<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Base\BaseCrudController;
use App\Models\MstFedLocalLevel;
use App\Models\MstExecutingEntity;
use App\Models\MstExecutingEntityType;
use App\Http\Requests\MstExecutingEntityRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstExecutingEntityCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstExecutingEntityCrudController extends BaseCrudController
{
    protected $action_method;

    public function setup()
    {
        CRUD::setModel(MstExecutingEntity::class);
        CRUD::setRoute('admin/mstexecutingentitytype/'.$this->parent('executing_entity_id').'/mstexecutingentity');
        CRUD::setEntityNameStrings(trans('menu.executingentity'), trans('menu.executingentity'));
        $this->checkPermission();
        

       
        $this->setUpLinks(['index','edit']);

        $this->action_method = $this->crud->getActionMethod();

        if(in_array($this->action_method, ['index','edit'])){
          $name = MstExecutingEntityType::find($this->parent('executing_entity_id'))->name_lc;
          $this->data['custom_title'] = 'सेवा प्रदायक प्रकार- '.$name. ' ('.trans('menu.executingentity').')';
        }
    }

    public function tabLinks()
    {
        return $this->setExecutingEntityTabs();

    }
   
    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            $this->addCodeColumn(),
          

            [
                'name' => 'name_en',
                'label' => trans('ExecutingEntity.name_en'),
            ],

            [
                'name' => 'name_lc',
                'label' => trans('ExecutingEntity.name_lc'),
            ],
            [  // Select
                'label' =>trans('ExecutingEntity.add_province_id'),
                'type' => 'select',
                'name' => 'add_province_id', // the db column for the foreign key
                'entity' => 'provinceEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstFedProvince::class,
            ],
            [  // Select
                'label' => trans('ExecutingEntity.add_district_id'),
                'type' => 'select',
                'name' => 'add_district_id', // the db column for the foreign key
                'entity' => 'districtEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstFedDistrict::class,
            ],
            [  // Select
                'label' => trans('ExecutingEntity.add_local_level_id'),
                'type' => 'select',
                'name' => 'add_local_level_id', // the db column for the foreign key
                'entity' => 'localLevelEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstFedLocalLevel::class,
            ],
            [
                'label' => trans('ExecutingEntity.add_ward_no'),
                'name' => 'add_ward_no',
            ],
            [
                'label' =>trans('ExecutingEntity.add_tole_name'),
                'name' => 'add_tole_name',
            ],
            [
                'label' => trans('ExecutingEntity.add_house_number'),

                'name' => 'add_house_number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.contact_person'),
                'name' => 'contact_person',
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_designation'),
                'name' => 'contact_person_designation',
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_phone'),
                'name' => 'contact_person_phone',
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_mobile'),
                'name' => 'contact_person_mobile',
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_email'),
                'name' => 'contact_person_email',
            ],
            [
                'label' =>trans('ExecutingEntity.uc_registration_number'),
                'name' => 'uc_registration_number',
            ],

            [
                'label' =>trans('ExecutingEntity.company_registration_number'),
                'type' => 'text',
                'name' => 'company_registration_number',
            ],
            $this->addIsActiveColumn(),
        ];
        // dd($cols);
        $cols = array_filter($cols);
        $this->crud->addColumns($cols);

        if ($this->parent('executing_entity_id') === null) {
            abort(404);
        } else {
            $this->crud->addClause('where', 'entity_type_id', $this->parent('executing_entity_id'));
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstExecutingEntityRequest::class);

        $arr = [
            $this->addCodeField(),
            $this->addPlainHtml(),
            [
                'name' => 'entity_type_id',
                'type' => 'hidden',
                'value' => $this->parent('executing_entity_id')
            ],
            
            $this->addNameEnField(),
            $this->addNameLcField(),

            [  // Select
                'label' => trans('ExecutingEntity.add_province_id'),
                'type' => 'select2',
                'name' => 'add_province_id', // the db column for the foreign key
                'entity' => 'provinceEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstFedProvince::class,
                // optional
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
                // optional
                'options'   => (function ($query) {
                    return (new MstFedProvince())->getFieldComboOptions($query);
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ],

            [
                'name'=>'add_district_id',
                'label' => trans('ExecutingEntity.add_district_id'),
                'type'=>'select2_from_ajax',
                'model'=>MstFedDistrict::class,
                'entity'=>'districtEntity',
                'attribute'=>'name_lc',
                'data_source' => url("api/get_district/add_province_id"),
                'placeholder' => "Select a District",
                'minimum_input_length' => 0,
                'include_all_form_fields' => true,
                'dependencies'         => ['add_province_id'],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name'=>'add_local_level_id',
                'label' => trans('ExecutingEntity.add_local_level_id'),
                'type'=>'select2_from_ajax',
                'entity'=>'localLevelEntity',
                'model'=>MstFedLocalLevel::class,
                'attribute'=>'name_en',
                'data_source' => url("api/get_locallevel/add_district_id"),
                'placeholder' => "Select a Local Level",
                'minimum_input_length' => 0,
                'include_all_form_fields' => true,
                'dependencies'         => ['add_district_id'],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id' => 'local_level_id',
                ]
            ],
            [
                'label' => trans('ExecutingEntity.add_ward_no'),
                'type' => 'number',
                'name' => 'add_ward_no',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.add_tole_name'),
                'type' => 'text',
                'name' => 'add_tole_name',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.add_house_number'),
                'type' => 'text',
                'name' => 'add_house_number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.contact_person'),
                'type' => 'text',
                'name' => 'contact_person',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_designation'),
                'type' => 'text',
                'name' => 'contact_person_designation',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_phone'),
                'type' => 'text',
                'name' => 'contact_person_phone',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.contact_person_mobile'),
                'type' => 'text',
                'name' => 'contact_person_mobile',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' =>trans('ExecutingEntity.contact_person_email'),
                'type' => 'text',
                'name' => 'contact_person_email',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            [
                'label' => trans('ExecutingEntity.uc_registration_number'),
                'type' => 'text',
                'name' => 'uc_registration_number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],

            [
                'label' =>trans('ExecutingEntity.company_registration_number'),
                'type' => 'text',
                'name' => 'company_registration_number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ],
            ],
            $this->addIsActiveField(),
            $this->addRemarksField(),
        ];
     

        $arr = array_filter($arr);
        $this->crud->addFields($arr);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
