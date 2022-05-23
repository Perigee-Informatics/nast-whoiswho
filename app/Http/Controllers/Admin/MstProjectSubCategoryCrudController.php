<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\MstProjectCategory;
use App\Models\MstProjectSubCategory;
use App\Http\Requests\MstProjectSubCategoryRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstProjectSubCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstProjectSubCategoryCrudController extends BaseCrudController
{
    protected $action_method;

    public function setup()
    {
        CRUD::setModel(MstProjectSubCategory::class);
        CRUD::setRoute('admin/mstprojectcategory/'.$this->parent('project_category_id').'/mstprojectsubcategory');
        CRUD::setEntityNameStrings(trans('menu.projectsubcategory'), trans('menu.projectsubcategory'));
        $this->setUpLinks(['index']);
        $this->setFilters();

        $this->action_method = $this->crud->getActionMethod();

        if(in_array($this->action_method, ['index','edit'])){
          $name = MstProjectCategory::find($this->parent('project_category_id'))->name_lc;
          $this->data['custom_title'] = 'आयोजना- '.$name. ' ('.trans('menu.projectsubcategory').')';
        }

    }

    public function tabLinks()
    {
        return $this->setProjectCategoryTabs();

    }

    protected function setFilters()
    {
        $this->addNameEnFilter();
        $this->addNameLcFilter();

    }

    protected function setupListOperation()
    {
        $cols=[
            $this->addRowNumberColumn(),
            $this->addCodeColumn(),
        //   [  // Select
    
        //     'label' => trans('ProjectSubCategory.project_category_id'),
        //     'type' => 'select',
        //     'name' => 'project_category_id', 
        //     'entity' => 'projectCategoryEntity', 
        //     'attribute' => 'name_lc',
        //     'model' => MstProjectCategory::class,
        // ],
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
         ];
         $cols = array_filter($cols);
         $this->crud->addColumns($cols);

        if ($this->parent('project_category_id') === null) {
            abort(404);
        } else {
            $this->crud->addClause('where', 'project_category_id', $this->parent('project_category_id'));
        }
    }

   
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstProjectSubCategoryRequest::class);

        $arr = [
            $this->addCodeField(),
            $this->addPlainHtml(),
            [
                'name' => 'project_category_id',
                'type' => 'hidden',
                'value' => $this->parent('project_category_id')
            ],

        //    [  // Select
        //     'label' => trans('ProjectSubCategory.project_category_id'),
        //     'type' => 'select2',
        //     'name' => 'project_category_id', 
        //     'entity' => 'projectCategoryEntity', 
        //     'attribute' => 'name_lc',
        //     'model' => MstProjectCategory::class,
        //     // optional
        //     'wrapper' => [
        //         'class' => 'form-group col-md-4'
        //     ],
        //     // optional
        //     'options'   => (function ($query) {
        //         return (new MstProjectCategory())->getFieldComboOptions($query);
        //     }), 
        // ],
          
        $this->addNameEnField(),
        $this->addNameLcField(),
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
