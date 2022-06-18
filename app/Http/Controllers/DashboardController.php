<?php

namespace App\Http\Controllers;

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

    public function printProfile($id)
    {
        return (new MemberCrudController())->printProfile($id);
    }
}
