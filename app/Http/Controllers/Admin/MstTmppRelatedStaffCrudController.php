<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstDesignation;
use App\Base\BaseCrudController;
use App\Models\MstTmppRelatedStaff;
use App\Models\MstExecutingEntityType;
use App\Http\Requests\MstTmppRelatedStaffRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstTmppRelatedStaffCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstTmppRelatedStaffCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MstTmppRelatedStaff::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/msttmpprelatedstaff');
        CRUD::setEntityNameStrings(trans('menu.employee'), trans('menu.employee'));
        $this->checkPermission();
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $cols=[
            $this->addRowNumberColumn(),
            // [  // Select
            //     'label' => trans('Entity'),
            //     'type' => 'select',
            //     'name' => 'entity_type_id', // the db column for the foreign key
            //     'entity' => 'entityTypeEntity', // the method that defines the relationship in your Model
            //     'attribute' => 'name_lc', // foreign key attribute that is shown to user
            //     'model' => MstExecutingEntityType::class,
            // ],
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            [  // Select
                'label' => trans('Designation'),
                'type' => 'select',
                'name' => 'designation_id', // the db column for the foreign key
                'entity' => 'designationEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstDesignation::class,
            ],
            $this->addIsActiveColumn(),
        ];
        $cols = array_filter($cols);
        $this->crud->addColumns($cols);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstTmppRelatedStaffRequest::class);

      
        $arr = [
            [  // Select
                'label' => trans('Entity'),
                'type' => 'select2',
                'name' => 'entity_type_id', // the db column for the foreign key
                'entity' => 'entityTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstExecutingEntityType::class,
                // optional
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ],
                // optional
                'options'   => (function ($query) {
                    return (new MstExecutingEntityType())->getFieldComboOptions($query);
                }), 
            ],
            $this->addNameEnField(),
            $this->addNameLcField(),
            [  // Select
                'label' => trans('Designation'),
                'type' => 'select2',
                'name' => 'designation_id', // the db column for the foreign key
                'entity' => 'designationEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstDesignation::class,
                // optional
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ],
                // optional
                'options'   => (function ($query) {
                    return (new MstDesignation())->getFieldComboOptions($query);
                }), 
            ],
            $this->addIsActiveField(),
            $this->addDisplayOrderField(),
            $this->addRemarksField(),
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
}
