<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppClient;
use App\Models\PtProject;
use App\Models\PtProjectFile;
use App\Base\BaseCrudController;
use App\Http\Requests\PtProjectFileRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PtProjectFileCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PtProjectFileCrudController extends BaseCrudController
{   
    protected $action_method;
    protected $user;
    public function setup()
    {
        CRUD::setModel(PtProjectFile::class);
        CRUD::setRoute('admin/ptproject/'.$this->parent('project_id').'/ptprojectfiles');
        CRUD::setEntityNameStrings(trans('project.projectfiles'), trans('project.projectfile'));
        $this->checkPermission();
        $this->setUpLinks();
        $this->user = backpack_user();
        
        $this->action_method = $this->crud->getActionMethod();

        if(in_array($this->action_method, ['index','edit'])){
          $name = PtProject::find($this->parent('project_id'))->name_lc;
          $this->data['custom_title'] = 'आयोजना- '.$name. ' ('.trans('project.projectfiles').')';
        }
    }

    public function tabLinks()
    {
        return $this->setPtProjectTabs();
    }

    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            $this->addClientColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            [
                'label' => 'Document',
                'name' => 'path',
                'type' => 'upload',
                'prefix' => '/storage/',
            ],
           
        ];
        $cols = array_filter($col);
        $this->crud->addColumns($cols);
        if ($this->parent('project_id') === null) {
            abort(404);
        } else {
            $this->crud->addClause('where', 'project_id', $this->parent('project_id'));
        }

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PtProjectFileRequest::class);
        $client_id = PtProject::findOrFail($this->parent('project_id'))->client_id;
        $arr = [
            [
                'name' => 'project_id',
                'type' => 'hidden',
                'value' => $this->parent('project_id'),
            ],
            [
                'name' => 'client_id',
                'type' => 'hidden',
                'value' => $client_id
            ],
            $this->addNameEnField(),
            $this->addNameLcField(),
            [
             'name' => 'path',
             'label' => 'Upload File',
             'type' => 'upload',
             'upload' => true,
             'disk' => 'uploads', 
             'wrapper' => [
                 'class' => 'form-group col-md-4',
             ],
         ],
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
