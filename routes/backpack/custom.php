<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => 'public',
    'middleware' => ['XSS'],
    'namespace'  => 'App\Http\Controllers',
], function () { // custom admin routes
    Route::get('member/{member_id}/print-profile', [DashboardController::class,'printProfile']);
    Route::post('get-members-list', [DashboardController::class,'getMembersList']);
    Route::get('member/{member_id}/view-detailed-info', [DashboardController::class,'viewDetailedInfo']);
});


Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web','XSS', config('backpack.base.middleware_key', 'admin')],
    // 'middleware' => array_merge(
    //     (array) config('backpack.base.web_middleware', 'web'),
    //     (array) config('backpack.base.middleware_key', 'admin'),
    // ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    // primary master routes
    Route::crud('mstfedprovince', 'MstFedProvinceCrudController');
    Route::crud('mstfeddistrict', 'MstFedDistrictCrudController');
    Route::crud('mstfedlocallevel', 'MstFedLocalLevelCrudController');
    Route::crud('mstfedlocalleveltype', 'MstFedLocalLevelTypeCrudController');
    Route::crud('mstfiscalyear', 'MstFiscalYearCrudController');
    Route::crud('mstnepalimonth', 'MstNepaliMonthCrudController');
    Route::crud('mstgender', 'MstGenderCrudController');


    Route::get('dashboard', 'DashboardCrudController@index');
    
    
    Route::crud('notification', 'NotificationCrudController');

    


    Route::crud('member', 'MemberCrudController');
    Route::post('import-member', 'MemberCrudController@importMembers')->name('importMemberExcel');
    Route::get('member/{member_id}/print-profile', 'MemberCrudController@printProfile');
    Route::get('member/profiles/print-all', 'MemberCrudController@printAllProfiles');
}); // this should be the absolute last line of this file