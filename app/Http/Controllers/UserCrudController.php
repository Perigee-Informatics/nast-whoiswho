<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppClient;
use Illuminate\Http\Request;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use Spatie\Permission\Models\Permission;

class UserCrudController extends BaseCrudController
{
    
    protected $client_user ;

    public function setup()
    {
        $this->client_user = backpack_user();
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(backpack_url('user'));
        $this->setFilters();

    }

    public function setFilters()
    {
        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => trans('Client'),
                'type' => 'select2',
                'name' => 'client_id', // the db column for the foreign key
            ],
            function () {
                return (new AppClient())->getClientFilterComboOptions();
            },
            function ($value) { 
                $this->crud->addClause('where', 'client_id', $value);
            }
        );
        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => trans('Role'),
                'type' => 'select2',
                'name' => 'roles', // the db column for the foreign key
            ],
            function () {
                return Role::pluck('field_name', 'id')->toArray();
            },
            function ($value) { 
                if($value){
                    $user_ids = DB::table('model_has_roles')->where('role_id',$value)->pluck('model_id');
                }
                $this->crud->addClause('whereIn', 'id',$user_ids);
            }
        );
    }

    public function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            [
                'name'=>'client_name',
                'type'=>'model_function',
                'label' => 'स्थानीय तह',
                'function_name' => 'clientName'
            ],
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'field_name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ],
        ];

        $cols = array_filter($cols);

        $this->crud->addColumns($cols);
    }

    public function addFields()
    {
      $arr = [
          $this->addClientIdField(),
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'type' => 'custom_html',
                'name'=>'custom_html_2',
                'value' => '<br/>',
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'field_name', // foreign key attribute that is shown to user
                        'model'            => Role::class, // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 4, //can be 1,2,3,4,6
                        'option' => $this->getPrivateRoles(),
                    ],
                    'secondary' => [
                        'label'          => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => Permission::class, // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 4, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ];
        $arr = array_filter($arr);
        $this->crud->addFields($arr);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(UserCreateRequest::class);     
        $this->addFields();
    }
    
    public function setupUpdateOperation()
    {
        $this->crud->setValidation(UserUpdateRequest::class);
        $this->addFields();
    }

    public function getPrivateRoles()
    {
       
    return Role::all();
    }


    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        $user = backpack_user();

        $request = $this->crud->validateRequest();
        $request->request->set('created_by', $user->id);
        $request->request->set('updated_by', $user->id);
        // $request->request->set('client_id', $user->client_id);

    
        //save full_name, email and password for sending email
        // $email_details = [
        //     'full_name' => $request->name_en,
        //     'email' => $request->email,
        //     'password' =>$request->password,
        // ];


        //encrypt password
        $request = $this->handlePasswordInput($request);

        DB::beginTransaction();
        try {
                $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'http_referrer']));  
            
                // if($item && env('SEND_MAIL_NOTIFICATION') == TRUE){
                //     $this->send_mail($email_details);
                // }
            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }


    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        $user = backpack_user();

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        $request->request->set('updated_by', $user->id);
        // $request->request->set('client_id', $user->client_id);



        //save full_name, email and password for sending email
        // $email_details = [
        //     'full_name' => $request->name_en,
        //     'email' => $request->email,
        //     'password' =>$request->password,
        // ];
        //encrypt password
        $request = $this->handlePasswordInput($request);

        DB::beginTransaction();
        try {
                $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
                        $request->except(['save_action', '_token', '_method', 'http_referrer']));

            
                // if($item && env('SEND_MAIL_NOTIFICATION') == TRUE){
                //     $this->send_mail($email_details);
                // }
            \Alert::success(trans('backpack::crud.update_success'))->flash();

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }
        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }
}