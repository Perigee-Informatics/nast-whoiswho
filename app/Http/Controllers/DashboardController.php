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

    public function getPageContent(Request $request)
    {   
        $key = $request->key;
        if($key == 'btn-graphical')
        {
            return view('public.partial.graphical');
        }else
        {
            $data['provinces'] = MstFedProvince::all();
            $data['districts'] = MstFedDistrict::all();
            $data['genders'] = MstGender::all();
            return view('public.partial.tabular_index',$data);
        }
    }

    public function getMembersList()
    { 
        $data = [];
        foreach(Member::all() as $member)
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

    public function printProfile($id)
    {
        return (new MemberCrudController())->printProfile($id);
    }
}
