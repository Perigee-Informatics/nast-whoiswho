<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Country;
use App\Models\MstGender;
use Illuminate\Http\Request;
use App\Base\Helpers\PdfPrint;
use App\Imports\MembersImport;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Base\BaseCrudController;
use App\Http\Requests\MemberRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MemberCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MemberCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Member::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/member');
        CRUD::setEntityNameStrings('member', 'members');
        $this->setFilters();
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */

    protected function setFilters()
    {
        $this->addProvinceIdFilter();
        $this->addDistrictIdFilter();
        $this->crud->addFilter(
            [ // simple filter
                'type' => 'select2',
                'name' => 'is_other_country',
                'label' => trans('Country'),
                'placeholder'=>'--choose--'
            ],
            [
                0 => 'Nepal',
                1 => 'Other',
            ],
            function ($value) { // if the filter is active
                if($value<2){
                    $this->crud->addClause('where', 'is_other_country', "$value");
                }
            }
        );

        $this->crud->addFilter(
            [ // simple filter
                'type' => 'select2',
                'name' => 'gender_id',
                'label' => trans('Gender'),
                'placeholder'=>'--choose--'

            ], function () {
                return MstGender::all()->pluck('name_en', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                $this->crud->query->whereGenderId($value);
            });

    }


    protected function setupListOperation()
    {
        $this->crud->addButtonFromView('top', 'excelImport', 'excelImport', 'end');
        
        // CRUD::setFromDb(); // columns
        $this->crud->addButtonFromView('line', 'print_profile', 'print_profile', 'beginning');
        $cols=[
            $this->addRowNumberColumn(),
            [   // Upload
                'name' => 'photo_path',
                'label' => trans('Photo'),
                'type' => 'image',
                'upload' => true,
                'disk' => 'uploads',
            ],
            [
                'name'=>'first_name',
                'type'=>'model_function',
                'function_name'=>'fullName',
                'label'=>'Full Name'
            ],
            // [
            //     'name' => 'first_name',
            //     'label' => trans('First Name'),
            //     'type' => 'text',
            // ],

            // [
            //     'name' => 'middle_name',
            //     'label' => trans('Middle Name'),
            //     'type' => 'text',
            // ],

            // [
            //     'name' => 'last_name',
            //     'label' => trans('Last Name'),
            //     'type' => 'text',
            // ],

            [
                'name' => 'gender_id',
                'type' => 'select',
                'entity'=>'genderEntity',
                'attribute' => 'name_en',
                'model'=>MstGender::class,
                'label' => trans('Gender'),
            ],
            [
                'name'=>'dob_ad',
                'type'=>'model_function',
                'function_name'=>'dob',
                'label'=>'D.O.B (B.S/A.D)'
            ],

            [
                'name'=>'nrn_number',
                'type'=>'text',
                'label'=>trans('NRN Number'),
            ],

            [ //Toggle
                'name' => 'is_other_country',
                'label' => "Other".'<br>'."Country ?",
                'type' => 'radio',
                'options'     => [ 
                    0 => trans('No'),
                    1 => trans('Other'),
                ],
            ],
            [
                'name'=>'country_id',
                'type'=>'select',
                'label'=>trans("Country"),
                'entity'=>'countryEntity',
                'model'=>Country::class,
                'attribute'=>'name_en',
            ],
            [
                'name'=>'province_id',
                'type'=>'select',
                'label'=>trans('Province'),
                'entity'=>'provinceEntity',
                'model'=>MstFedProvince::class,
                'attribute'=>'name_en',
            ],
            [
                'name'=>'district_id',
                'label'=>trans('District'),
                'type'=>'select',
                'model'=>MstFedDistrict::class,
                'entity'=>'districtEntity',
                'attribute'=>'name_en',
            ],
            [
                'name'  => 'current_organization',
                'label'   => '<center>Current Organization</center>',
                'type'  => 'custom_table',
                'columns' => [
                    'position'=> 'Position',
                    'organization' => 'Organization',
                    'address' => 'Address',
                ]
            ],
            [
                'name'  => 'past_organization',
                'label'   => '<center>Past Organization</center>',
                'type'  => 'custom_table',
                'columns' => [
                    'position'=> 'Position',
                    'organization' => 'Organization',
                    'address' => 'Address',
                ]
            ],
            [
                'name'  => 'doctorate_degree',
                'label'   => '<center>Educational Qualifications</center>',
                'type'  => 'education_custom_table',
                'columns' => [
                    'degree_name'=> 'Degree Name',
                    'others_degree' => 'Others (If any)',
                    'subject_or_research_title' => 'Subject/Research Title',
                    'university_or_institution' => 'Name of University/Institution',
                    'country' => 'Address',
                    'year' => 'Year',
                ]
            ],
            [
                'name'  => 'awards',
                'label'   => '<center> Awards</center>',
                'type'  => 'awards_custom_table',
                'columns' => [
                    'award_name'=> 'Award Name',
                    'awarded_by' => 'Awarded By',
                    'awarded_year' => 'Year',
                ]
            ],
            [
                'name'  => 'expertise',
                'label'   => '<center> Expertise</center>',
                'type'  => 'awards_custom_table',
                'columns' => [
                    'name'=> 'Name',
                ]
            ],
            [
                'name'  => 'affiliation',
                'label'   => '<center> Affiliation</center>',
                'type'  => 'awards_custom_table',
                'columns' => [
                    'name'=> 'Name',
                ]
            ],
           
            [
                'name' => 'mailing_address',
                'label' => trans('Mailing Address'),
                'type' => 'model_function',
                'function_name'=>'mailingAddress'
            ],
            [
                'name' => 'phone',
                'label' => trans('Phone/Cell'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => trans('E-mail'),
                'type' => 'text',
            ],
            [
                'name' => 'link_to_google_scholar',
                'label' => trans('Link to Google Scholar'),
                'type' => 'url',
            ],

        ];

        $this->crud->addColumns(array_filter($cols));
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MemberRequest::class);

        // CRUD::setFromDb(); // fields
        $arr=[
            [
                'name' => 'gender_id',
                'type' => 'select2',
                'entity'=>'genderEntity',
                'attribute' => 'name_en',
                'model'=>MstGender::class,
                'label' => trans('Gender'),
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
                'attributes'=>[
                    'required' => 'required',
                ],
            ],

            [
                'name' => 'dob_bs',
                'type' => 'nepali_date',
                'label' => trans('common.date_bs'),
                'attributes'=>[
                    'id'=>'date_bs',
                    'relatedId'=>'dob_ad',
                    'maxlength' =>10,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'dob_ad',
                'type' => 'date',
                'label' => trans('common.date_ad'),
                'attributes'=>[
                    'id'=>'dob_ad',
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],

            [
                'name'=>'nrn_number',
                'type'=>'text',
                'label'=>trans('NRN Number'),
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],

            [
                'name' => 'first_name',
                'label' => trans('First Name'),
                'type' => 'text',
                'attributes'=>[
                    'id' => 'name-en',
                    'required' => 'required',
                    'max-length'=>200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],

            [
                'name' => 'middle_name',
                'label' => trans('Middle Name'),
                'type' => 'text',
                'attributes'=>[
                    'max-length'=>200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],

            [
                'name' => 'last_name',
                'label' => trans('Last Name'),
                'type' => 'text',
                'attributes'=>[
                    'required' => 'required',
                    'max-length'=>200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],
            [   // Upload
                'name' => 'photo_path',
                'label' => trans('Photo'),
                'type' => 'image',
                'upload' => true,
                'disk' => 'uploads',
                'crop'=>true, 
                'aspect_ratio'=>1,
                'wrapper' => [
                    'class' => 'form-group col-md-3',
                ],
            ],

            [ //Toggle
                'name' => 'is_other_country',
                'label' => trans('Is Other Country ?'),
                'type' => 'toggle',
                'options'     => [ 
                    0 => trans('Nepal'),
                    1 => trans('Other'),
                ],
                'inline' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' =>[
                    'id' => 'is_other_country',
                ],
                'hide_when' => [
                    0 => ['country_id'],
                    1 => ['province_id','district_id'],
                ],
                'default' => 0,
            ],
            [
                'name'=>'province_id',
                'type'=>'select2',
                'label'=>trans('Province'),
                'entity'=>'provinceEntity',
                'model'=>MstFedProvince::class,
                'attribute'=>'name_en',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
            [
                'name'=>'district_id',
                'label'=>trans('District'),
                'type'=>'select2_from_ajax',
                'model'=>MstFedDistrict::class,
                'entity'=>'districtEntity',
                'attribute'=>'name_en',
                'data_source' => url("api/district/province_id"),
                'placeholder' => "Select a District",
                'minimum_input_length' => 0,
                'dependencies'         => ['province_id'],
                'include_all_form_fields'=>true,
                'method'=>'POST',
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
        
            [
                'name'=>'country_id',
                'type'=>'select2',
                'label'=>trans("Country"),
                'entity'=>'countryEntity',
                'model'=>Country::class,
                'attribute'=>'name_en',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes'=> [
                    'id'=> 'country-id',
                ]
            ],

            [
                'name'  => 'current_organization',
                'label'   => trans('Current Organization'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'position',
                        'type'    => 'text',
                        'label'   => trans('Position'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'organization',
                        'type'    => 'text',
                        'label'   => trans('Organization'),
                        'wrapper' => ['class' => 'form-group col-md-5'],
                        'required' => true
                    ],
                    [
                        'name'    => 'address',
                        'type'    => 'text',
                        'label'   => trans('Address'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],


            [
                'name'  => 'past_organization',
                'label'   => trans('Past Organization'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'position',
                        'type'    => 'text',
                        'label'   => trans('Position'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'organization',
                        'type'    => 'text',
                        'label'   => trans('Organization'),
                        'wrapper' => ['class' => 'form-group col-md-5'],
                        'required' => true
                    ],
                    [
                        'name'    => 'address',
                        'type'    => 'text',
                        'label'   => trans('Address'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],

            [
                'name'  => 'doctorate_degree',
                'label'   => trans('Doctorate Degree'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'degree_name',
                        'type'    => 'text',
                        'label'   => trans('Degree Name'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'others_degree',
                        'type'    => 'text',
                        'label'   => trans('Other degree(If any)'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'subject_or_research_title',
                        'type'    => 'text',
                        'label'   => trans('Subject/Research Title'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'university_or_institution',
                        'type'    => 'text',
                        'label'   => trans('Name of University/Institution'),
                        'wrapper' => ['class' => 'form-group col-md-6'],
                        'required' => true
                    ],
                    [
                        'name'    => 'country',
                        'type'    => 'text',
                        'label'   => trans('Country'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                    [
                        'name'    => 'year',
                        'type'    => 'text',
                        'label'   => trans('Year'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name'  => 'masters_degree',
                'label'   => trans('Masters Degree'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'degree_name',
                        'type'    => 'text',
                        'label'   => trans('Degree Name'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'others_degree',
                        'type'    => 'text',
                        'label'   => trans('Other degree(If any)'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'subject_or_research_title',
                        'type'    => 'text',
                        'label'   => trans('Subject/Research Title'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'university_or_institution',
                        'type'    => 'text',
                        'label'   => trans('Name of University/Institution'),
                        'wrapper' => ['class' => 'form-group col-md-6'],
                        'required' => true
                    ],
                    [
                        'name'    => 'country',
                        'type'    => 'text',
                        'label'   => trans('Country'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                    [
                        'name'    => 'year',
                        'type'    => 'text',
                        'label'   => trans('Year'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name'  => 'bachelors_degree',
                'label'   => trans('Bachelors Degree'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'degree_name',
                        'type'    => 'text',
                        'label'   => trans('Degree Name'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'others_degree',
                        'type'    => 'text',
                        'label'   => trans('Other degree(If any)'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'subject_or_research_title',
                        'type'    => 'text',
                        'label'   => trans('Subject/Research Title'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'university_or_institution',
                        'type'    => 'text',
                        'label'   => trans('Name of University/Institution'),
                        'wrapper' => ['class' => 'form-group col-md-6'],
                        'required' => true
                    ],
                    [
                        'name'    => 'country',
                        'type'    => 'text',
                        'label'   => trans('Country'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                    [
                        'name'    => 'year',
                        'type'    => 'text',
                        'label'   => trans('Year'),
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name'  => 'awards',
                'label'   => trans('Awards'),
                'type'  => 'repeatable_with_action',
                'fields' => [
                    [
                        'name'    => 'award_name',
                        'type'    => 'text', 
                        'label'   => trans('Award Name'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'awarded_year',
                        'type'    => 'text',
                        'label'   => trans('Awarded year'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                    [
                        'name'    => 'awarded_by',
                        'type'    => 'text',
                        'label'   => trans('Awarded By'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name'  => 'expertise',
                'label'   => trans('Expertise'),
                'type'  => 'repeatable_with_action',
                'wrapper'=>[
                    'class'=>'col-md-6'
                ],
                'fields' => [
                    [
                        'name'    => 'name',
                        'type'    => 'text',
                        'label'   => trans('Expertise Title'),
                        'wrapper' => ['class' => 'form-group col-md-12'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name'  => 'affiliation',
                'label'   => trans('Affiliation'),
                'type'  => 'repeatable_with_action',
                'wrapper'=>[
                    'class'=>'col-md-6'
                ],
                'fields' => [
                    [
                        'name'    => 'name',
                        'type'    => 'text',
                        'label'   => trans('Affiliation Title'),
                        'wrapper' => ['class' => 'form-group col-md-12'],
                        'required' => true
                    ],
                ],
                'min_rows' => 1,
            ],
            [
                'name' => 'mailing_address',
                'label' => trans('Mailing Address'),
                'type' => 'text',
                'attributes'=>[
                    'max-lenght'=>500,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ],
            [
                'name' => 'phone',
                'label' => trans('Phone/Cell'),
                'type' => 'text',
                'attributes'=>[
                    'max-lenght'=>200,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ],
            [
                'name' => 'email',
                'label' => trans('E-mail'),
                'type' => 'text',
                'attributes'=>[
                    'max-lenght'=>500,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ],
            [
                'name' => 'link_to_google_scholar',
                'label' => trans('Link to Google Scholar'),
                'type' => 'url',
                'attributes'=>[
                    'max-lenght'=>100,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
            ],

        ];

        $this->crud->addFields($arr);

    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }


    public function importMembers(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'excelMemberFile' => 'required',
        ]);

        try {
            $itemImport = new MembersImport;
            Excel::import($itemImport, request()->file('excelMemberFile'));
            return 1;
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $excel_errors = $e->failures();
            return view('partial_excel_barcode_errors', compact('excel_errors'));
        }
	}


    public function printProfile($id){
        $member = Member::find($id);

        $json_data = [
            'current_organization' => json_decode($member->current_organization),
            'past_organization' => json_decode($member->past_organization),
            'doctorate_degree' => json_decode($member->doctorate_degree),
            'masters_degree' => json_decode($member->masters_degree),
            'bachelors_degree' => json_decode($member->bachelors_degree),
            'awards' => json_decode($member->awards),
            'expertise' => json_decode($member->expertise),
            'affiliation' => json_decode($member->affiliation),
            'awards' => json_decode($member->awards),
        ];

        $photo_encoded = "";
        $photo_path = public_path('storage/uploads/'.$member->photo_path);
        // Read image path, convert to base64 encoding
        if($member->photo_path){
            $imageData = base64_encode(file_get_contents($photo_path));
            $photo_encoded = 'data: '.mime_content_type($photo_path).';base64,'.$imageData;
        }
            // Format the image SRC:  data:{mime};base64,{data};

        $html = view('profile.individual_profile', compact('member','json_data','photo_encoded'))->render();
        PdfPrint::printPortrait($html, $member->first_name.' '.$member->middle_name.' '.$member->last_name."_Profile.pdf"); 
    }
}
