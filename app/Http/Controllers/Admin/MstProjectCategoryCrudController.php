<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstProjectCategory;
use App\Http\Requests\MstProjectCategoryRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstProjectCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstProjectCategoryCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(MstProjectCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstprojectcategory');
        CRUD::setEntityNameStrings(trans('menu.projectcategory'), trans('menu.projectcategory'));
        $this->setUpLinks(['edit']);
        $this->setFilters();
    }

    public function tabLinks()
    {
        return $this->setProjectCategoryTabs();

    }

    protected function setFilters()
    {
        $this->addNameEnFilter();
        $this->addNameLcFilter();

    }

    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            // $this->addCodeColumn(),
            [
                'name' => 'code',
                'type' => 'model_function',
                'function_name' => 'codeLink'
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
        CRUD::setValidation(MstProjectCategoryRequest::class);
        $arr = [
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
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
