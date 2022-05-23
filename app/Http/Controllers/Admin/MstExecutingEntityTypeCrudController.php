<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstExecutingEntityType;
use App\Http\Requests\MstExecutingEntityTypeRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstExecutingEntityTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstExecutingEntityTypeCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(MstExecutingEntityType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstexecutingentitytype');
        CRUD::setEntityNameStrings(trans('menu.executingentitytypes'), trans('menu.executingentitytypes'));
        $this->checkPermission();
        
      

        $this->setUpLinks(['edit']);
        $this->setFilters();
    }

    public function tabLinks()
    {
        return $this->setExecutingEntityTabs();

    }
    
    protected function setFilters()
    {
        $this->addNameEnFilter();
        $this->addNameLcFilter();

    }

    protected function setupListOperation()
    {
        $cols = [
                $this->addRowNumberColumn(),
                [
                    'name' => 'code',
                    'type' => 'model_function',
                    'function_name' => 'codeLink'
                ],
                $this->addNameEnColumn(),
                $this->addNameLcColumn(),
                $this->addDisplayOrderColumn(),
        ];
        $this->crud->addColumns($cols);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstExecutingEntityTypeRequest::class);
     
        $arr = [
            $this->addCodeField(),
            $this->addPlainHtml(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addRemarksField(),
            $this->addDisplayOrderField(),
        ];
            
        $this->crud->addFields($arr);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        if(backpack_user()->isClientUser()){
            $this->crud->disableSaveAction = true;
        };
    }
}
