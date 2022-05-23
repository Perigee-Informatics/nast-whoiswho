<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppClient;
use Illuminate\Http\Request;
use App\Base\BaseCrudController;
use App\Models\MstFedLocalLevel;
use App\Http\Requests\AppClientRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AppClientCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppClientCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(AppClient::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appclient');
        CRUD::setEntityNameStrings('Client', 'Client');
    }

    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            [
                'name' => 'lmbiscode',
                'label' => trans('स्थानीय तह Code'),
                'type' => 'text',
            ],
            [
                'label'=>trans('appClient.fed_local_level'),
                'type' => 'select',
                'name' => 'fed_local_level_id', 
                'entity' => 'fedLocalLevelEntity', 
                'attribute' => 'name_lc', 
                'model' => MstFedLocalLevel::class,
            ],
        
            $this->addNameEnField(),
            $this->addNameLcField(),
          
            [
                'name'=>'admin_email',
                'type'=>'text',
                'label'=>trans('appClient.admin_email'),
            ],
            [
                'name'=>'is_tmpp_applicable',
                'label'=>trans('Is TMPP applicable ?'),
                'type'=>'radio',
                'options'=>
                [
                    1=>'Yes',
                    0=>'No',
                ],
            ],
        ];
        $this->crud->addColumns($cols);
        $this->crud->addClause('where', 'id','!=',1000);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AppClientRequest::class);

        $arr=[
            [
                'label'=>trans('appClient.fed_local_level'),
                'type' => 'select2',
                'name' => 'fed_local_level_id', 
                'entity' => 'fedLocalLevelEntity', 
                'attribute' => 'name_lc', 
                'model' => MstFedLocalLevel::class,
                'options'   => (function ($query) {
                    return (new MstFedLocalLevel())->getClientFieldComboOptions($query);
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=> [
                    'onChange'=>'TMPP.fetchDetailsById(this)'
                ]
            ],
            $this->addPlainHtml(),

            $this->addReadOnlyCodeField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            [
                'name' => 'lmbiscode',
                'label' => trans('स्थानीय तह Code'),
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id'=>'lmbis-code',
                ],
            ],
            [
                'name'=>'admin_email',
                'type'=>'text',
                'label'=>trans('appClient.admin_email'),
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-8',
                ],
            ],
            [
                'name'=>'is_tmpp_applicable',
                'label'=>trans('Is TMPP applicable ?'),
                'type'=>'radio',
                'default'=>1,
                'inline' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options'=>
                [
                    1=>'Yes',
                    0=>'No',
                ],
            ],
            $this->addRemarksField(),
            $this->addIsActiveField(),
        ];
        $this->crud->addFields($arr);         
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function getDetailsById(Request $request){

        $fed_local_level_id = $request->fed_local_level_id;

        $fed_local_level = MstFedLocalLevel::findOrFail($fed_local_level_id);

        return response()->json([
            'message'=>'success',
            'details' => $fed_local_level,
        ]);
    }
}
