<?php

namespace App\Models;

use Carbon\Carbon;
use App\Base\BaseModel;
use App\Models\AppClient;
use App\Models\PtProject;
use App\Base\DataAccessPermission;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PtProjectFile extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'pt_project_files';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['client_id','project_id','name_en','name_lc','path','remarks'];
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setPathAttribute($value)
    {
        $attribute_name = "path";
        $disk = "uploads";
        // $client = (isset(request()->client_id) ? AppClient::find(request()->client_id)->name_en : 0);
        // $project_id = request()->project_id;
        $user_id = backpack_user()->id;
        $date = Carbon::now()->format('Y-m');

        $path  = '###USERID###/###DATE###';
        $destination_path = str_replace("###USERID###", $user_id, $path);
        $destination_path = str_replace("###DATE###", $date, $destination_path);
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);  
       
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
}
