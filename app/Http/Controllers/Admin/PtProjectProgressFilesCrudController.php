<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppClient;
use App\Models\PtProject;
use App\Base\BaseCrudController;
use App\Models\PtProjectProgress;
use App\Models\PtProjectProgressFiles;
use App\Http\Requests\PtProjectProgressFilesRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PtProjectProgressFilesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PtProjectProgressFilesCrudController extends BaseCrudController
{
   
    protected $user;
    public function setup()
    {
        CRUD::setModel(PtProjectProgressFiles::class);
        CRUD::setRoute('admin/projectprogress/'.$this->parent('project_progress_id').'/ptprojectprogressfiles');
        CRUD::setEntityNameStrings(trans('menu.ptprojectprogressfiles'), trans('menu.ptprojectprogressfiles'));
        $this->checkPermission();
        $this->setUpLinks(['index']);
        $this->user = backpack_user();
    }

    public function tabLinks()
    {
        return $this->setProjectProgressTabs();
    }

    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            $this->addClientColumn(),
            [
                'label' =>'Project',
                'type' => 'select',
                'name' => 'project_id', // the db column for the foreign key
                'entity' => 'projectEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => PtProject::class,
            ],
            [
                'label' => 'Document',
                'name' => 'path',
                'type' => 'upload',
                'prefix' => '/storage/',
            ],
           
        ];
            $col = array_filter($col);
            $this->crud->addColumns($col);
            $this->crud->addClause('where', 'project_progress_id', $this->parent('project_progress_id'));
    }

   
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PtProjectProgressFilesRequest::class);
        $project_id = PtProjectProgress::findOrFail($this->parent('project_progress_id'))->project_id;
        $client_id = PtProjectProgress::findOrFail($this->parent('project_progress_id'))->client_id;
        $arr = [
            [
                'name' => 'project_progress_id',
                'type' => 'hidden',
                'value' => $this->parent('project_progress_id')
            ],
            [
                'name' => 'project_id',
                'type' => 'hidden',
                'value' => $project_id
            ],
            [
                'name' => 'client_id',
                'type' => 'hidden',
                'value' => $client_id
            ],
            [
                'label' =>  'File Name',
                'type' => 'text',
                'name' => 'file_name',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
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

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
