<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PtProject;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NewProjectRequest;
use App\Http\Controllers\Admin\PtProjectCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class NewProjectCrudController extends PtProjectCrudController
{
    protected $user;
    public function setup()
    {
        $route = 'admin/newproject';
        $this->user = backpack_user();

        CRUD::setModel(PtProject::class);
        CRUD::setEntityNameStrings(trans('project.new_project'), trans('project.new_project'));
        $langfile= 'newproject';
        $this->_setup($route, $langfile);
        $this->checkPermission();

        $this->crud->query->where('project_status_id',1);
    }

  

}
