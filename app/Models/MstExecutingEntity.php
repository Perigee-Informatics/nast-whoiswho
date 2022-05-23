<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Base\DataAccessPermission;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class MstExecutingEntity extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'mst_executing_entity';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = ['client_id','entity_type_id','code','name_en','name_lc','add_province_id','add_district_id','add_local_level_id','add_ward_no','
    add_tole_name','add_house_number','contact_person','contact_person_designation','contact_person_phone','contact_person_mobile','contact_person_email',
    'uc_registration_number','company_registration_number','remarks'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */


    public function entity_type(){
        return $this->belongsTo(MstExecutingEntityType::class, 'entity_type_id', 'id');
    }
    public function provinceEntity(){
        return $this->belongsTo(MstFedProvince::class, 'add_province_id', 'id');
    }
    public function districtEntity(){
        return $this->belongsTo(MstFedDistrict::class, 'add_district_id', 'id');
    }
    public function localLevelEntity(){
        return $this->belongsTo(MstFedLocalLevel::class, 'add_local_level_id', 'id');
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
