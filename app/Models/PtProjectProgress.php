<?php

namespace App\Models;

use App\Models\User;
use App\Base\BaseModel;
use App\Models\MstUnit;
use App\Models\AppClient;
use App\Models\PtProject;
use App\Eloquent\SoftDeletes;
use App\Models\MstFiscalYear;
use App\Models\MstDesignation;
use App\Models\MstNepaliMonth;
use App\Models\MstProjectStatus;
use App\Models\PtSelectedProject;
use App\Base\DataAccessPermission;
use App\Models\MstTmppRelatedStaff;
use App\Models\MstReportingInterval;
use App\Models\MstExecutingEntityType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PtProjectProgress extends BaseModel
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    public $dataAccessPermission = DataAccessPermission::ShowClientWiseDataOnly;

    protected $table = 'pt_project_progress';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['client_id','selected_project_id','reporting_interval_id','date_bs','date_ad','financial_progress_percent',
                            'financial_progress_amount','physical_progress_percent','prepared_by','prepared_by_designation_id',
                            'submitted_by','submitted_by_designation_id','approved_by','approved_by_designation_id','fiscal_year_id',
                            'quantity','weightage','unit_type','project_status_id','executing_entity_type_id','file_upload'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function reportingIntervalEntity(){
        return $this->belongsTo(MstReportingInterval::class, 'reporting_interval_id', 'id');
    }
    public function fiscalyearEntity(){
        return $this->belongsTo(MstFiscalYear::class, 'fiscal_year_id', 'id');
    }
    public function unitTypeEntity(){
        return $this->belongsTo(MstUnit::class, 'unit_type', 'id');
    }
    public function projectEntity(){
        return $this->belongsTo(PtSelectedProject::class, 'selected_project_id', 'id');
    }
    public function clientEntity(){
        return $this->belongsTo(AppClient::class, 'client_id', 'id');
    }
    public function projectStatusEntity(){
        return $this->belongsTo(MstProjectStatus::class, 'project_status_id', 'id');
    }
    public function executingEntityTypeEntity(){
        return $this->belongsTo(MstExecutingEntityType::class, 'executing_entity_type_id', 'id');
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
    public function projectName()
    {
        $project = isset($this->selected_project_id) ? $this->projectEntity->name_lc : '-';
        $project_name = wordwrap($project, 110, "<br/>" ,false);
        $href = '<a href="'.backpack_url('projectprogress/'.$this->id.'/edit').'"title="Click to View" style="color:blue">'.$project_name.'</a>';

        return $href;
    }


    public function fullDate()
    {
        $date_bs = isset($this->date_bs) ? $this->date_bs : '-';
        $date_ad = isset($this->date_ad)? $this->date_ad : '-';
            
        return $date_bs."<br/>".$date_ad;
    }

    public function provinceDistrict()
    {
        $province = $this->clientEntity->fedLocalLevelEntity->districtEntity->provinceEntity->name_lc;
        $district = $this->clientEntity->fedLocalLevelEntity->districtEntity->name_lc;

        return $province."<br>".$district;
    }

    public function setFileUploadAttribute($value)
    {
        $attribute_name = "file_upload";
        $disk = "uploads";
        $client =  AppClient::findOrFail(backpack_user()->client_id)->name_en;
        $project_id = PtSelectedProject::findOrFail(request()->selected_project_id)->project_id;

        $path  = '###CLIENT###/###PROJECT###';
        $destination_path = str_replace("###CLIENT###", $client, $path);
        $destination_path = str_replace("###PROJECT###", $project_id, $destination_path);
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
