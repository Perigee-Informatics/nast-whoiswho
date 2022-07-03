<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MstGender;
use Illuminate\Http\Request;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use App\Http\Controllers\Admin\MemberCrudController;

class DashboardController extends Controller
{
    public function index()
    {
        return view('public.index');
    }

    public function updateProvinceId()
    {
        $members  = Member::all();
      
        foreach($members as $m)
        {
           $p_id = MstFedDistrict::find($m->district_id)->province_id;

           Member::whereId($m->id)->update(['province_id'=>$p_id]);
        
        }
        return back();
    }

    public function getPageContent(Request $request)
    {   
        $key = $request->key;
        if($key == 'btn-graphical')
        {
            return view('public.partial.graphical');
        }else
        {
            $data['provinces'] = MstFedProvince::orderBy('id')->get();
            $data['districts'] = MstFedDistrict::orderBy('id')->get();
            $data['genders'] = MstGender::orderBy('id')->get();
            return view('public.partial.tabular_index',$data);
        }
    }

    public function getMembersList(Request $request)
    { 

        $data = [];
   
        $members = Member::all();

        if($request->province_id != '')
        {
           $members = $members->where('province_id',$request->province_id);
        }
        if($request->district_id != '')
        {
            $members =$members->where('district_id',$request->district_id);
        }

        if($request->gender_id != '')
        {
            $members =$members->where('gender_id',$request->gender_id);
        }

        if($request->country_status != '')
        {
            if($request->country_status == 'other'){
                $members = $members->where('is_other_country',true);
            }else{
                $members =$members->where('is_other_country',false);
            }
        }

        foreach($members as $member)
        {
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
    
            // $photo_encoded = "";
            // $photo_path = public_path('storage/uploads/'.$member->photo_path);
            // // Read image path, convert to base64 encoding
            // if($member->photo_path){
            //     $imageData = base64_encode(file_get_contents($photo_path));
            //     $photo_encoded = 'data: '.mime_content_type($photo_path).';base64,'.$imageData;
            // }

            $data[$member->id]['basic'] = $member;
            $data[$member->id]['json_data'] = $json_data;
            // $data[$member->id]['photo_encoded'] = $photo_encoded;
        }
        return view('public.partial.tabular_member_data',compact('data'));
    }

    public function viewDetailedInfo($id)
    {
        $data=[];
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
        $data['basic'] = $member;
        $data['json_data'] = $json_data;
        return view('public.partial.member-detail-info',compact('data'));

    }

    public function printProfile($id)
    {
        return (new MemberCrudController())->printProfile($id,true);
    }
}
