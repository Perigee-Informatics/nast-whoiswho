<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('member') }}'><i class='nav-icon la la-users'></i> Members</a></li>

<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gears"></i>{{ trans('menu.primary') }}</a>
	<ul class="nav-dropdown-items" style="overflow-x:hidden">
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedprovince'><i class='nav-icon la la-compass'></i>{{trans('menu.fedprovince')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfeddistrict'><i class='nav-icon la la-compass'></i> {{trans('menu.feddistrict')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedlocallevel'><i class='nav-icon la la-compass'></i> {{trans('menu.fedlocallevel')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfedlocalleveltype'><i class='nav-icon la la-compass'></i> {{trans('menu.fedlocalleveltype')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstfiscalyear'><i class='nav-icon la la-compass'></i> {{trans('menu.fiscalyear')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstnepalimonth'><i class='nav-icon la la-compass'></i> {{trans('menu.nepalimonth')}}</a></li>
		<li class='nav-item'><a class='nav-link' href='/admin/mstgender'><i class='nav-icon la la-compass'></i> {{trans('menu.gender')}}</a></li>
	</ul>
</li>
<li class='nav-item'><a class='nav-link' href='/admin/user'><i class='nav-icon la la-users'></i> Users</a></li>


