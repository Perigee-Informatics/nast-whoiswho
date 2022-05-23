<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\PtProject;
use App\Models\PtSelectedProject;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class AppClient extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'app_client';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id','created_by','updated_by'];
    protected $fillable = ['code','name_en','name_lc','fed_local_level_id','admin_email','remarks','is_tmpp_applicable','lmbiscode','is_active'];

    public function fedLocalLevelEntity() {
        return $this->belongsTo(MstFedLocalLevel::class,'fed_local_level_id','id');
    }
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    public function clientProjects(){
        return $this->hasMany(PtProject::class,'client_id','id')->whereIn('project_status_id',[1,2,3,4]);
    }

    public function clientProjectsDemand(){
        return $this->hasMany(PtProject::class,'client_id','id')->where('project_status_id',1)->where('deleted_uq_code',1);
    }
    public function clientProjectsSelected(){
        return $this->hasMany(PtSelectedProject::class,'client_id','id')->where('project_status_id',2)->where('deleted_uq_code',1);
    }
    public function clientProjectsWip(){
        return $this->hasMany(PtSelectedProject::class,'client_id','id')->where('project_status_id',3)->where('deleted_uq_code',1);
    }
    public function clientProjectsComplete(){
        return $this->hasMany(PtSelectedProject::class,'client_id','id')->where('project_status_id',4)->where('deleted_uq_code',1);
    }

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

    public function getFilterComboOptions(){
        $a = self::selectRaw("code|| ' - ' || name_lc as name_lc , id");

        return $a->orderBy('id', 'ASC')
            ->get()
            ->keyBy('id')
            ->pluck('name_lc', 'id')
            ->toArray();
    }
}
