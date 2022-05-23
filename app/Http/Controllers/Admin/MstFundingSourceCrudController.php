<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstFundingSource;
use App\Http\Requests\MstFundingSourceRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstFundingSourceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstFundingSourceCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MstFundingSource::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mstfundingsource');
        CRUD::setEntityNameStrings(trans('menu.fundingsource'), trans('menu.fundingsource'));
        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->addNameEnFilter();
        $this->addNameLcFilter();

    }

    protected function setupListOperation()
    {
        $cols = [
            $this->addCodeColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            [
                'name' => 'min',
                'label' => trans('FundingSource.min'),
            ],
            [
                'name' => 'max',
                'label' =>  trans('FundingSource.max'),
            ],
            $this->addDisplayOrderColumn(),
        ];
        $cols = array_filter($cols);
        $this->crud->addColumns($cols);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstFundingSourceRequest::class);
        $arr = [
            $this->addCodeField(),
            $this->addPlainHtml(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            [
                'name' => 'min',
                'label' => trans('FundingSource.min'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'max',
                'label' => trans('FundingSource.max'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],  
            $this->addDisplayOrderField(),
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
