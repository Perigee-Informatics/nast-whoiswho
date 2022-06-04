<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MemberCrudController;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('public.dashboard');
    }

    public function printProfile($id)
    {
        return (new MemberCrudController())->printProfile($id);
    }
}
