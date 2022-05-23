<?php

namespace App\Models;

use App\Base\BaseModel;
// use Illuminate\Database\Eloquent\Model;
use App\Models\MstUnit;
use App\Models\AppClient;
use App\Eloquent\SoftDeletes;
use App\Models\MstFiscalYear;
use App\Models\MstProjectStatus;
use App\Base\DataAccessPermission;
use App\Models\MstProjectCategory;
use App\Models\MstExecutingEntityType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PtSelectedProject extends BaseModel
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'pt_selected_project';
    protected $guarded = ['id'];
    protected $fillable = ['client_id','project_id','lmbiscode','fiscal_year_id','name_lc','description_lc','category_id','project_status_id','project_cost',
                            'source_federal_amount','source_local_level_amount','source_donar_amount','executing_entity_type_id','unit_type','quantity','weightage','project_affected_population',
                            'project_affected_ward_count','project_affected_wards','gps_lat','gps_long','selected_date_bs','selected_date_ad','proposed_start_date_bs','proposed_start_date_ad',
                            'proposed_duration_year','proposed_duration_months','proposed_end_date_bs','proposed_end_date_ad','actual_start_date_bs','actual_start_date_ad','actual_end_date_bs',
                            'actual_end_date_ad','actual_duration_year','actual_duration_months','actual_duration_days','incharge_name','incharge_designation','incharge_phone','incharge_mobile',
                            'incharge_email','remarks','has_dpr','file_upload'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function clientEntity(){
        return $this->belongsTo(AppClient::class, 'client_id', 'id');
    }

    public function fiscalYearEntity()
    {
        return $this->belongsTo(MstFiscalYear::class,'fiscal_year_id','id');
    }
    public function categoryEntity()
    {
        return $this->belongsTo(MstProjectCategory::class,'category_id','id');
    }
    public function subCategoryEntity()
    {
        return $this->belongsTo(MstProjectSubCategory::class,'sub_category_id','id');
    }
    public function projectStatusEntity()
    {
        return $this->belongsTo(MstProjectStatus::class,'project_status_id','id');
    }
    public function executingEntityTypeEntity()
    {
        return $this->belongsTo(MstExecutingEntityType::class,'executing_entity_type_id','id');
    }
    public function unitTypeEntity()
    {
        return $this->belongsTo(MstUnit::class,'unit_type','id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function nameLc()
    {
        $name = wordwrap($this->name_lc, 100, "<br/>" ,false);
        $href = '<a href="'.backpack_url('ptselectedproject/'.$this->id.'/edit').'"title="Click to View" style="color:blue;">'.$name.'</a>';
        // $name = '<span style="color:blue;">'.$name.'</span>';
        return $href;
    }

    public function clientName()
    {
        $name = $this->clientEntity->name_lc;
        if($this->clientEntity->lmbiscode){
            $name .= '<br><span style="color:red; font-size:10px;">('.$this->clientEntity->lmbiscode.')</span>';
        }

       return $name;
    }

    public function provinceDistrict()
    {
        $province = $this->clientEntity->fedLocalLevelEntity->districtEntity->provinceEntity->name_lc;
        $district = $this->clientEntity->fedLocalLevelEntity->districtEntity->name_lc;

        return $province."<br>(".$district.")";
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
}
