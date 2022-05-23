<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use App\Http\Requests\AppSettingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AppSettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppSettingCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(AppSetting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appsetting');
        CRUD::setEntityNameStrings('App Setting', 'App Setting');
        $this->checkPermission([
            'super_admin' => ['list', 'create', 'update', 'delete', 'export', 'print'],
            'central_admin' => ['list', 'create','update','delete','export', 'print'],
            'central_operator' => ['list', 'update', 'export', 'print'],
            'central_viewer' => ['list', 'update', 'export', 'print'],
            'locallevel_admin' => ['list','update','delete','export', 'print'],
            'locallevel_operator' => ['list'], 
        ]);

    }

  
    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            $this->addClientColumn(),
            [
                'name' => 'fiscal_year_id',
                'type' => 'select',
                'entity'=>'fiscalyearEntity',
                'attribute' => 'code',
                'model'=>MstFiscalYear::class,
                'label' => trans('common.fiscal_year'),
            ],
            [
                'name' => 'incharge_name',
                'label' => trans('project.incharge_id'),
                'type' => 'text',
            ],
            [
                'name' => 'incharge_designation',
                'label' => trans('project.incharge_designation_id'),
                'type' => 'text',
            ],
            [
                'name' => 'incharge_phone',
                'label' => trans('project.incharge_phone'),
                'type' => 'text',
                'prefix'=>'<i class="la la-phone"></i>',
            ],
            [
                'name' => 'incharge_mobile',
                'label' => trans('project.incharge_mobile'),
                'type' => 'text',
                'prefix'=>'<i class="la la-mobile"></i>',
            ],
            [
                'name' => 'incharge_email',
                'label' => trans('project.incharge_email'),
                'type' => 'text',
                'prefix'=>'<i class="la la-envelope-square"></i>',
            ],
        ];

        $this->crud->addColumns(array_filter($cols));
    }


    protected function setupCreateOperation()
    {
        CRUD::setValidation(AppSettingRequest::class);
        $allow_project_demand = NULL;

        $mode = $this->crud->getActionMethod();
        $client = $this->addClientIdField();

        if(in_array($mode,['edit','update'])){
            $entry_client_id = AppSetting::find($this->crud->getCurrentEntryId())->client_id;
            if($entry_client_id === 1000){
                $client = NULL;

                $allow_project_demand = [
                    'name'=>'allow_new_project_demand',
                    'label'=>trans('Allow New Project Demand for Clients?'),
                    'type'=>'radio',
                    'inline' => true,
                    'wrapper' => [
                        'class' => 'form-group col-md-4',
                    ],
                    'options'=>
                    [
                        1=>'Yes',
                        0=>'No',
                    ],
                    'default'=>0,
                ];
            }
        }
    

        $arr = [
            $client,
            [
                'name' => 'fiscal_year_id',
                'type' => 'select2',
                'entity'=>'fiscalyearEntity',
                'attribute' => 'code',
                'model'=>MstFiscalYear::class,
                'label' => trans('common.current_fiscal_year'),
                'placeholder'=>'सबै',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            $allow_project_demand,
        ];
        if(backpack_user()->isClientUser()){
            $arr = array_merge($arr, $this->additionalFields());
        }

        if((!backpack_user()->isClientUser()) && (in_array($mode,['edit','update'])) && $entry_client_id !== 1000){
            $arr = array_merge($arr, $this->additionalFields());
        }

        $this->crud->addFields(array_filter($arr));
    }

    public function additionalFields(){
        $additional_arr = [
            [
                'name' => 'fieldset_open',
                'type' => 'custom_html',
                'value' => '<fieldset>',
            ],
            [
                'name' => 'legend1',
                'type' => 'custom_html',
                'value' => '<legend>आयोजना प्रमुखको विवरण</legend><hr class="m-0">',
            ],

            [
                'name' => 'incharge_name',
                'label' => trans('project.incharge_id'),
                'type' => 'text',
                'attributes' => [
                    'max-length' => 200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'incharge_designation',
                'label' => trans('project.incharge_designation_id'),
                'type' => 'text',
                'attributes' => [
                    'max-length' => 200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'incharge_phone',
                'label' => trans('project.incharge_phone'),
                'type' => 'text',
                'prefix'=>'<i class="la la-phone"></i>',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'incharge_mobile',
                'label' => trans('project.incharge_mobile'),
                'type' => 'number',
                'prefix'=>'<i class="la la-mobile"></i>',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name' => 'incharge_email',
                'label' => trans('project.incharge_email'),
                'type' => 'email',
                'prefix'=>'<i class="la la-envelope-square"></i>',
                'wrapper' => [
                    'class' => 'form-group col-md-8',
                ],
            ],
        ];
        return $additional_arr;
    }


    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),$this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time

        return redirect(backpack_url('appsetting/'.$item->getKey().'/edit'));
    }
}
