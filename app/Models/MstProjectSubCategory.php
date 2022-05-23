<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstProjectCategory;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class MstProjectSubCategory extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'mst_project_sub_category';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = ['code','name_en','name_lc','remarks','is_active','project_category_id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function projectCategoryEntity(){
        return $this->belongsTo(MstProjectCategory::class, 'project_category_id', 'id');
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
