<?php

namespace App\Http\Controllers\Admin;

use App\Models\PtProject;
use App\Models\MstNoteType;
use App\Models\PtProjectNote;
use App\Base\BaseCrudController;
use App\Http\Requests\PtProjectNoteRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PtProjectNoteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PtProjectNoteCrudController extends BaseCrudController
{
    protected $action_method;
    protected $user;
    public function setup()
    {
        CRUD::setModel(PtProjectNote::class);
        CRUD::setRoute('admin/ptproject/'.$this->parent('project_id').'/ptprojectnotes');
        CRUD::setEntityNameStrings(trans('project.projectnote'), trans('project.projectnote'));
        $this->checkPermission();

        $this->setUpLinks();
        $this->user = backpack_user();
        
        $this->action_method = $this->crud->getActionMethod();

        if(in_array($this->action_method, ['index','edit'])){
          $name = PtProject::find($this->parent('project_id'))->name_lc;
          $this->data['custom_title'] = 'आयोजना- '.$name. ' ('.trans('project.projectnote').')';
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
            [
                'label' => trans('project.projectnotetype'),
                'type' => 'select',
                'name' => 'note_type_id', // the db column for the foreign key
                'entity' => 'noteTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstNoteType::class,
            ],
            $this->addDateBsColumn(),
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
        CRUD::setValidation(PtProjectNoteRequest::class);
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
            [
                'label' => trans('project.projectnotetype'),
                'type' => 'select2',
                'name' => 'note_type_id', // the db column for the foreign key
                'entity' => 'noteTypeEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_lc', // foreign key attribute that is shown to user
                'model' => MstNoteType::class,
                'placeholder' => 'नोट प्रकार छान्नुहोस्',
                'options' => (function ($query) {
                    return (new MstNoteType())->getFieldComboOptions($query);
                }),
                'wrapper' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            $this->addDateBsField(),
            $this->addDateAdField(),
            [
                'name' => 'note',
                'label' => trans('common.note'),
                'type' => 'textarea',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ]
        ];
        $arr = array_filter($arr);
        $this->crud->addFields($arr);
     
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
