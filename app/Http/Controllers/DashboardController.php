<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\MstGender;
use Illuminate\Http\Request;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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



            $sql ="with expertise_data as (
                select expertise->>'name' as expertise_name,channel_wiw
                from(
                        select channel_wiw,json_array_elements(expertise)::json as expertise from members)a
                )
                select ed.* from expertise_data ed where expertise_name is not null and channel_wiw = true";

            $results = DB::select($sql);
            // $results = array_slice($results,0,100);

            $expertise_string= '';

            foreach($results as $r)
            {
                if($expertise_string != ''){

                    $pattern = strtolower($r->expertise_name);

                    if(strpos($pattern,$expertise_string) !== false){
                        break;
                    }else{
                        $expertise_string .= '; '. $pattern;
                    }


                    // $pattern_exploded = explode(' ',$pattern);

                    //loop through exploded patterns
                    // foreach($pattern_exploded as $segment)
                    // {
                    //     if(strpos($segment,$expertise_string) !== false){
                    //         break;
                    //     }else{
                    //         $expertise_string .= '; '. $segment;
                    //     }
                    // }
             
           
                }else{
                    $expertise_string .=strtolower($r->expertise_name);
                }
            }

            $expertise_string = \str_replace([',','.','(',')','‘','“'],'',$expertise_string);
            $expertise_string = \str_replace(['/'],' ',$expertise_string);
            $expertise_string = preg_replace("/\r|\n/", "", $expertise_string);
            $explode_result = \explode('; ',$expertise_string);

            $explode_result = array_values(array_unique(array_values(array_filter($explode_result))));
            $final_result = [];

            foreach($explode_result as $er)
            {
                $str_status = false;

                if(strlen($er)>2 && strlen($er) < 70)
                {
                    $str_status= true;
                }

                // for ($i = 0; $i < strlen($er); $i++) {
                //     if (ctype_digit($er[$i]) ) {
                //         $str_status = false;
                //         break;
                //     }
                // }

                if($str_status == true)
                {
                    $final_result[] = $er;
                }
            }

            $final_result = collect($final_result);

            // dd($final_result);

            //array to exclude items;

            // $exclude = ['based','water','']

           $data['expertise_result']= $final_result;

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

        $member_ids = [];

        if($request->age_group != '')
        {
            $_members = Member::all();

            foreach($_members as $member)
            {
                $member_age = Carbon::now()->diffInYears(Carbon::parse($member->dob_ad));

                switch($request->age_group){
                    case "Below-30":
                        if($member_age <= 30){
                            $member_ids[] = $member->id; 
                        }
                    break;
                    case "31-40":
                        if($member_age > 30 && $member_age <= 40){
                            $member_ids[] = $member->id; 
                        }
                    break;
                    case "41-50":
                        if($member_age > 40 && $member_age <= 50){
                            $member_ids[] = $member->id; 
                        }
                    break;
                    case "51-60":
                        if($member_age > 50 && $member_age <= 60){
                            $member_ids[] = $member->id; 
                        }
                    break;
                    case "60-Above":
                        if($member_age > 60){
                            $member_ids[] = $member->id; 
                        }
                    break;

                }
              
            }
            $members = $members->whereIn('id',$member_ids);
        }

        
        if($request->channel != '')
        {
            if($request->channel == 'wiw'){
                $members = $members->where('channel_wiw',true);
            }else if($request->channel == 'wsfn'){
                $members =$members->where('channel_wsfn',true);
            }else if($request->channel == 'foreign'){
                $members =$members->where('channel_foreign',true);
            }else{
                $members = $members;
            }
        }
        if($request->membership_type != '')
        {
            $members = $members->where('membership_type',$request->membership_type);
        }
        if($request->expertise != '')
        {
            $expertise = implode("','%",$request->expertise);

            // dd($expertise);
            $sql ="with expertise_data as (
                select id,expertise->>'name' as expertise_name,channel_wiw
                from( select id,channel_wiw,json_array_elements(expertise)::json as expertise from members)a)
                select ed.* from expertise_data ed where expertise_name is not null and channel_wiw = true
                and expertise_name iLike  ANY(ARRAY['%$expertise'])";

            $results = collect(DB::select($sql));
            $mem_ids = $results->pluck('id')->toArray();

            $mem_ids_uq = array_unique($mem_ids);

            $members = $members->whereIn('id',$mem_ids_uq);

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

        $data = $this->paginate(collect($data), 10, null, url('/') . \Request::getRequestUri());

        return view('public.partial.tabular_member_data',compact('data'));
    }

    public function paginate($items, $perPage = 20, $page = null, $baseUrl = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ?$items : Collection::make($items);
        $lap = new LengthAwarePaginator(
                $items->forPage($page, $perPage),
                $items->count(),
                $perPage,
                $page,
                $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }
        return $lap;
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
