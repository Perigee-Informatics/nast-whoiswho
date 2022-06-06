@php
$date = convert_bs_from_ad();
@endphp

<header class="{{ config('backpack.base.header_class') }}">
  <!-- Logo -->
  <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto ml-3" type="button" data-toggle="sidebar-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand text-white" href="{{ url(config('backpack.base.home_link')) }}" title="{{ config('backpack.base.project_name') }}">
    {!! config('backpack.base.project_logo') !!}
  </a>
  <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="date-time">
    <p style="color: white;" class="mt-2">{{$date}} <br><span id="txt"></span></p>
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
      .notification-menu{
        margin-right: -90px !important;
      }
      .la-bell{
        font-size:25px;
        text-shadow: 2px 2px 5px #000000;
      }

      .dropdown a i{
        transition: .5s;
      }
      .dropdown a:hover .bell-icon {
        /* font-weight: bold;
        transform: rotateY(180deg); */
        background-color:#73818f;
        color:yellow !important;
        font-weight:bold;
        padding:5px;
        border-radius:7px;
     } 
    
      .badge {  
      position: relative;  
      top: -15px;
      left:-10px;  
      padding: 4px 6px;  
      border-radius: 50%;  
      color: white;  
    } 
    .dropdown-toggle::after {
          display:none;
    }
    .notification-card{
      margin-left:-50vh;
      width:400px;
      max-height:300px;
      overflow: hidden;
      border-radius: 15px;
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