<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstRoadConnectivityType;
use App\Http\Requests\MstRoadConnectivityTypeRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstRoadConnectivityTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstRoadConnectivityTypeCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MstRoadConnectivityType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstroadconnectivitytype');
        CRUD::setEntityNameStrings(trans('menu.roadconnectivitytype'), trans('menu.roadconnectivitytype'));
    }
    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            $this->addCodeColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            $this->addDisplayOrderColumn(),
         
        ];
            $this->crud->addColumns($col);
            $this->crud->orderBy('display_order'); 
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstRoadConnectivityTypeRequest::class);

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
