@php
$date = convert_bs_from_ad();

$current_url = url()->current();
@endphp

<header class="{{ config('backpack.base.header_class') }}">
  <!-- Logo -->
@if(Str::contains($current_url,'admin'))
  <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto ml-3" type="button" data-toggle="sidebar-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand text-dark" href="{{ url(config('backpack.base.home_link')) }}" title="{{ config('backpack.base.project_name') }}">
    {!! config('backpack.base.project_logo').' '. config('backpack.base.project_name_small')  !!}
  </a>
  <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>
 @else
 <div class="row" style="display: contents">
    <a class="ml-4 text-dark text-decoration-none font-xl" href="{{ url(config('backpack.base.home_link')) }}" title="{{ config('backpack.base.project_name') }}">
      {!! config('backpack.base.project_logo')  !!}
      <span style="font-weight: 600; color: #dd3c36 !important; position:relative; top: -5px !important">
        {{config('backpack.base.project_name')}}<br/>
      </span>
      <span class="font-sm ml-5 pl-3" style="position:relative; top: -20px !important; color:black;">
        Science & Technology for National Development
      </span>
    </a>
 </div>
 @endif

  <div class="date-time d-lg-block d-sm-none">
    <p style="color: black;" class="mt-2">{{$date}} <br><span id="txt"></span></p>
  </div>
  @include(backpack_view('inc.menu'))
  <img src="{{asset('css/images/nepalflag.gif')}}" border="0" alt=""  style="width:50px;height:50px;" />

</header>

<style>

@font-face {
      font-family: "Kalimati";
      src: url("/fonts/Kalimati.ttf") format("truetype");
  }

      .date-time{
        color: #73818f;
        width: 250px;
        position: sticky;
        top: 0;
        left: 80%;
      }

</style>

<script>
  function startTime() {
    var today = new Date();
    var h = today.getHours();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    h = h ? h : 12;
    var m = today.getMinutes();
    var s = today.getSeconds();
    h=checkTime(h);
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt').innerHTML =
    h + ":" + m + ":" + s+ " " + ampm;
    var t = setTimeout(startTime, 500);
  }
  function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
  }
  
  window.onload=startTime();
  
  </script>