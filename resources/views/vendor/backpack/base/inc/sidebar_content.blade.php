<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@hasanyrole('super_admin|central_admin|central_operator')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('newproject') }}'><i class='nav-icon la la-home'></i>{{trans('menu.newproject')}}</a></li>
@endhasanyrole

@hasanyrole('locallevel_admin|locallevel_operator')
@php
	$is_allowed = \App\Models\AppSetting::where('client_id',1000)->pluck('allow_new_project_demand')->first();
@endphp

@if($is_allowed)
	<li class='nav-item'><a class='nav-link' href='{{ backpack_url('newproject') }}'><i class='nav-icon la la-home'></i>{{trans('menu.newproject')}}</a></li>
@endif
@endhasanyrole

{{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('ptproject') }}'><i class='nav-icon la la-road'></i>{{trans('menu.ptproject')}}</a></li> --}}
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('ptselectedproject') }}'><i class='nav-icon la la-road'></i> {{trans('menu.ptproject')}}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('projectprogress') }}'><i class='nav-icon la la-bar-chart'></i> {{trans('menu.ptprojectprogress')}}</a></li>

<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>प्रतिवेदन</a>
	<ul class="nav-dropdown-items" style="overflow-x:hidden">
		<li class='nav-item'><a class='nav-link' href='/admin/anusuchi_3'><i class='nav-icon la la-file-text'></i>अनुसुची ४ (माग)</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/anusuchi_4'><i class='nav-icon la la-file-text'></i>अनुसुची ५</a></li>
		
		@hasanyrole('super_admin|central_admin|central_operator')
		<li class='nav-item'><a class='nav-link' href='/admin/project_progress'><i class='nav-icon la la-file-text'></i> प्रगति प्रबिस्ट प्रतिबेदन</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/report/project_pivot'><i class='nav-icon la la-bar-chart'></i>Pivot Report</a></li>
		@endhasanyrole
    </ul>
</li> 

<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gears"></i>{{ trans('menu.master') }}</a>
	<ul class="nav-dropdown-items" style="overflow-x:hidden">
		<li class='nav-item'><a class='nav-link' href='/admin/mstexecutingentitytype'><i class='nav-icon la la-compass'></i> {{trans('menu.executingentitytypes')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/msttmpprelatedstaff'><i class='nav-icon la la-compass'></i> {{trans('menu.employee')}}</a></li>
    </ul>
</li> 

@hasanyrole('super_admin|central_admin|central_operator')
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gears"></i>{{ trans('menu.primary') }}</a>
	<ul class="nav-dropdown-items" style="overflow-x:hidden">
		<li class='nav-item'><a class='nav-link' href='{{ backpack_url('appclient') }}'><i class='nav-icon la la-compass'></i>{{trans('menu.appclient')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstprojectcategory'><i class='nav-icon la la-compass'></i>{{trans('menu.projectcategory')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfundingsource'><i class='nav-icon la la-compass'></i> {{trans('menu.fundingsource')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstreportinginterval'><i class='nav-icon la la-compass'></i> {{trans('menu.reportinginterval')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstprojectstatus'><i class='nav-icon la la-compass'></i> {{trans('menu.projectstatus')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstdesignation'><i class='nav-icon la la-compass'></i> {{trans('menu.designation')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstunit'><i class='nav-icon la la-compass'></i> {{trans('menu.unit')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstroadconnectivitytype'><i class='nav-icon la la-compass'></i> {{trans('menu.roadconnectivitytype')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstnotetype'><i class='nav-icon la la-compass'></i> {{trans('menu.notetype')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedprovince'><i class='nav-icon la la-compass'></i>{{trans('menu.fedprovince')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfeddistrict'><i class='nav-icon la la-compass'></i> {{trans('menu.feddistrict')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedlocallevel'><i class='nav-icon la la-compass'></i> {{trans('menu.fedlocallevel')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedlocalleveltype'><i class='nav-icon la la-compass'></i> {{trans('menu.fedlocalleveltype')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfiscalyear'><i class='nav-icon la la-compass'></i> {{trans('menu.fiscalyear')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstnepalimonth'><i class='nav-icon la la-compass'></i> {{trans('menu.nepalimonth')}}</a></li>
	</ul>
</li>
<li class='nav-item'><a class='nav-link' href='/admin/user'><i class='nav-icon la la-users'></i> Users</a></li>
@php
	$app_setting_id = \App\Models\AppSetting::where('client_id',1000)->pluck('id')->first();
@endphp
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('appsetting/'.$app_setting_id.'/edit') }}'><i class='nav-icon la la-gear'></i> App Setting</a></li>
@endhasanyrole
{{-- <li class='nav-item'><a href="{{ backpack_url('pull-tmpp-data') }}" class="nav-link text-center"><i class='nav-icon la la-gear'></i>Pull Data</a></li> --}}


@hasanyrole('locallevel_admin|locallevel_operator')
@php
	$app_setting_id = \App\Models\AppSetting::where('client_id',backpack_user()->client_id)->pluck('id')->first();
@endphp
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('appsetting/'.$app_setting_id.'/edit') }}'><i class='nav-icon la la-gear'></i> App Setting</a></li>
@endhasanyrole


