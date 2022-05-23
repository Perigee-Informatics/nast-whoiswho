<?php
namespace App\Http\Controllers\Api;
use App\Models\PtProject;
use Illuminate\Http\Request;
use App\Models\PtSelectedProject;
use App\Http\Controllers\Controller;

class ProjectApiController extends Controller{

    public function index(Request $request,$value)
   {
       
    $search_term = $request->input('q');
    $form = collect($request->input('form'))->pluck('value', 'name');
    // dd($search_term,$form);


    $options = PtSelectedProject::query();

    if (!data_get($form, $value)) {
        return [];
    }

    if (data_get($form, $value)) {
        $options = $options->where('client_id', $form[$value]);
    }

    if ($search_term) {
        $results = $options->where('name_lc', 'LIKE', '%' . $search_term . '%')->paginate(10);
    } else {
        $results = $options->paginate(10);
    }

    return $options->paginate(10);
   }
}
