<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Base\DataAccessPermission;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PtProjectNote extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'pt_project_notes';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['client_id','project_id','note_type_id','date_bs','date_ad','note'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function projectEntity(){
        return $this->belongsTo(PtProject::class, 'project_id', 'id');
    }
    public function clientEntity(){
        return $this->belongsTo(AppClient::class, 'client_id', 'id');
    }
    public function noteTypeEntity(){
        return $this->belongsTo(MstNoteType::class, 'note_type_id', 'id');
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
