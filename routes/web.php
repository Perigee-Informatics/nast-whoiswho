<?php

// use App\Base\BasePivotController;
use App\Base\BasePivotController;
use Illuminate\Support\Facades\Route;
use App\Notifications\NewProjectCreate;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\CategoryUnitController;
use App\Http\Controllers\Admin\DashboardCrudController;
use Backpack\CRUD\app\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ProvinceDistrictController;
use App\Http\Controllers\Api\DependentDropdownController;
use App\Http\Controllers\Api\DistrictLocalLevelController;
use App\Http\Controllers\Api\ProjectSubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class,'showLoginForm']);
Route::get('/admin', [LoginController::class,'showLoginForm']);
// Route::get('/', function(){
//     return view('errors.503');
// });
// Route::get('/admin', function(){
//     return view('errors.503');
// });
// Route::get('/admin/login', function(){
//     return view('errors.503');
// });
Route::get('/home', [AdminController::class,'redirect']);


Route::get('api/get_district/{add_province_id}', [ProvinceDistrictController::class,'index']);
Route::get('api/get_locallevel/{add_district_id}', [DistrictLocalLevelController::class,'index']);
Route::get('api/get_subcategory/{category_id}', [ProjectSubCategoryController::class,'index']);
Route::get('api/get_project/{client_id}', [ProjectApiController::class,'index']);
Route::get('api/get_units/{category_id}', [CategoryUnitController::class,'index']);

Route::get('/district/{id}', [DependentDropdownController::class, 'getdistrict']);
Route::get('/local_level/{id}', [DependentDropdownController::class,'getlocal_level']);
Route::get('/projectid/{id}', [DependentDropdownController::class,'getproject']);
Route::get('/sub_category/{id}', [DependentDropdownController::class,'getsub_category']);
Route::get('/month_id/{id}', [DependentDropdownController::class,'getTimeofreport']);
Route::get('/unit_type/{id}', [DependentDropdownController::class,'getunittype']);
Route::get('/app_client_filter/{district_id}', [DependentDropdownController::class,'getapp_client']);


Route::get('get-nepal-map-data', [DashboardCrudController::class,'getNepalMapdata']);
Route::get('get-province-data', [DashboardCrudController::class,'getProvinceData']);
Route::get('get-district-data', [DashboardCrudController::class,'getDistrictData']);
Route::get('get-all-projects-on-local-level', [DashboardCrudController::class,'getLocalLevelProjectsData']);
Route::get('get-geodata', [DashboardCrudController::class,'getGeoData']);
Route::get('admin/report/masterdata', [BasePivotController::class,'getMasterData']);





