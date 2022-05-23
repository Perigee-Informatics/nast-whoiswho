<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstReportingInterval;
use App\Http\Requests\MstReportingIntervalRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstReportingIntervalCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstReportingIntervalCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(MstReportingInterval::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstreportinginterval');
        CRUD::setEntityNameStrings(trans('menu.reportinginterval'), trans('menu.reportinginterval'));
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
        CRUD::setValidation(MstReportingIntervalRequest::class);
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
