<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstUnit;
use App\Base\BaseCrudController;
use App\Models\MstProjectCategory;
use App\Http\Requests\MstUnitRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstUnitCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstUnitCrudController extends BaseCrudController
{

    public function setup()
    {
        CRUD::setModel(MstUnit::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstunit');
        CRUD::setEntityNameStrings(trans('menu.unit'), trans('menu.unit'));
    }

    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            $this->addCodeColumn(),
            [
                // Select
                'label' => trans('project.category_id'),
                'type' => 'select',
                'name' => 'category_id', // the db column for the foreign key
                'entity' => 'categoryEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstProjectCategory::class,
            ],
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            $this->addDisplayOrderColumn(),
         
        ];
            $this->crud->addColumns($col);
            $this->crud->orderBy('display_order'); 
    }
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstUnitRequest::class);

        $arr = [
            $this->addCodeField(),
            $this->addPlainHtml(),
            [
                // Select
                'label' => trans('project.category_id'),
                'type' => 'select2',
                'name' => 'category_id', // the db column for the foreign key
                'entity' => 'categoryEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstProjectCategory::class,
                'placeholder' => 'आयोजना प्रकार छान्नुहोस्',
                'options' => (function ($query) {
                    return (new MstProjectCategory())->getFieldComboOptions($query);
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ]
            ],
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addDisplayOrderField(),
        ];
        $arr = array_filter($arr);
        $this->crud->addFields($arr);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
