@php
$date = convert_bs_from_ad();
$count = NULL;
$all_notifications = [];
$route = isset($crud) ? $crud->getRoute() : NULL;
  if(!backpack_user()->isClientUser()){
    if($route === 'admin/newproject'){
      $all_notifications = new_project_added();
    }elseif ($route === 'admin/projectprogress') {
      $all_notifications = project_progress_added();
    }else{
      $all_notifications = project_added_and_progress();
    }

    if(count($all_notifications) > 0) {
    $count = count($all_notifications);
  }
}
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
  <div class="col">
    <a class="text-white text-decoration-none">
        <b><h5>नेपाल सरकार संघीय मामिला तथा सामान्य प्रशासन मन्त्रालय</h5>
        <h4>तराई-मधेश समृद्धि कार्यक्रम</h4></b>
    </a>
  </div>
@if(!backpack_user()->isClientUser())
    <div class="dropdown notification-menu">
     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="la la-bell bell-icon text-white"></i><span class="badge badge-danger">{{$count}}</span></a>
      <div class="dropdown-menu card notification-card">
        <div class="card-header bg-secondary text-center p-2" style="margin-top:-10px;"><b> {{ (count($all_notifications) > 0) ? 'You have '.$count.' new '.Str::plural('notification',$count) : 'No new notification !!'}}</b></div>

        <div class="card-body p-0">
          @if(count($all_notifications) > 0)
            <table class="table table-striped notification-table table-responsive table-hover table-sm" width="100%" style="height:200px;">
              <tbody style="width:100%; display:table">
                  @foreach ($all_notifications as $notification)
                      @php
                      $data = json_decode($notification->data);
                      if($notification->type === 'App\Notifications\NewProjectProgressCreate'){
                        $a_class = 'text-primary ml-5';
                        $img_url = "homepage/img/work_in_progress.png";
                      }else{
                        $a_class = 'text-success ml-5';
                        $img_url = "homepage/img/plus.png";
                      }
                      @endphp
                    <tr>
                      <td><div class="font-weight-bold" style="font-size: 15px;"><img class="rounded-circle z-depth-2 mr-2" src={{asset($img_url)}} height="20" width="20"> {{$data->client_name}}<br/>
                        <a class="{{ $a_class }}" href="{{'project/'.$notification->id. '/' .$data->project_id }}">{{ $data->project_detail }}</a></div>
                      </td>
                    </tr>
                    <tr><td><hr style="height:2px; margin:0px;"></td></tr>
                  @endforeach
                </tbody>
            </table>
            @endif
        </div>
        <div class="card-footer bg-secondary p-1 text-center font-weight-bold">
          <a class="text-center color:blue" href="{{ backpack_url('notification')}}"><i class="la la-eye mr-1"></i>View all notifications</a>
        </div>
      </div>
  </div>
@endif



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
        font: small-caps bold 14px kalimati, serif;
        width: 250px;
        position: sticky;
        top: 0;
        left: 82%;
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