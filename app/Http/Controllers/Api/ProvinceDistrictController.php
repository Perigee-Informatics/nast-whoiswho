<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MstFedDistrict;
use App\Http\Controllers\Controller;

class ProvinceDistrictController extends Controller
{
    public function index(Request $request, $value)
    {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');

        $options = MstFedDistrict::query();
        // if no district has been selected, show no options in localevel
        if (!data_get($form, $value)) {
            return [];
        }

        // if a district has been selected, only show localevel from that district
        if (data_get($form, $value)) {
            $options = $options->where('province_id', $form[$value])->whereRaw("id in (SELECT distinct district_id from mst_fed_local_level where is_tmpp_applicable = true)");
        }

        if ($search_term) {
            $results = $options->where('name_lc', 'LIKE', '%' . $search_term . '%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }

        return $options->paginate(10);
    }
}
