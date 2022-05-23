<?php

namespace App\Models;

use App\Models\AppClient;
use App\Models\PtProject;
use App\Base\DataAccessPermission;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PtProjectProgressFiles extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'pt_project_progress_files';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['client_id','project_id','project_progress_id','file_name','path','remarks'];
    // protected $fillable = [];
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
    public function setPathAttribute($value)
    {
        $attribute_name = "path";
        $disk = "uploads";

        $client = (isset(request()->client_id) ? AppClient::find(request()->client_id)->name_en : 0);
        $progress_id = request()->project_progress_id;

        $path  = '###CLIENT###/###PROGRESS###/File';
        $destination_path = str_replace("###CLIENT###", $client, $path);
        $destination_path = str_replace("###PROGRESS###", $progress_id, $destination_path);
        $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);  
       
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if (count((array)$obj->files)) {
                foreach ($obj->files as $file_path) {
                    \Storage::disk('uploads')->delete($file_path);
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
