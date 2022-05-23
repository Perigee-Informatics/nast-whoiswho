<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use CrudTrait;
    // const Project_Create = 1;
    // const Project_Progress_Create = 2;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getContent()
    {
        $data = json_decode($this->data);
        $content = $data->client_name;
        if($this->project_status === 1){
             $href = '<a href="'.backpack_url('newproject/'.$data->project_id .'/edit').'"title="Click to View" style="color:blue">'.$content.'</a>';
        }else{
            $href = '<a href="'.backpack_url('projectprogress/'.$data->project_id .'/edit').'"title="Click to View" style="color:blue">'.$content.'</a>';
        }
        return $href;
    }
    public function getRead()
    {
        $value = 'TRUE';
        if($this->read_at === null){
            $value = 'FALSE';
        }
        return $value;
    }
    
    public function getStatus()
    {
        if($this->project_status === 1){
            $value = 'New Project Demand Added';
        }else{
            $value = 'New Project Progress Added';
        }
        return $value;
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
