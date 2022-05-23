

<div class="row mb-2 ml-5">
    <div class="col-md-12">
          <ul class="nav nav-tabs flex-column flex-sm-row mt-2"id="myTab" role="tablist">
              <li role="presentation" class="nav-item border border-white">
                  <a class="nav-link tab-link {{$selected_tab}} p-0 pr-2 pl-2" 
                  href="{{ url($crud->route)}}?status=selected" role="tab">जम्मा स्वीकृत</a>
              </li>
              <li role="presentation" class="nav-item border border-white">
                  <a class="nav-link tab-link {{$work_in_progress_tab}} p-0 pr-2 pl-2" 
                  href="{{ url($crud->route)}}?status=work_in_progress" role="tab">कार्य सुचारु</a>
              </li>
              <li role="presentation" class="nav-item border border-white ">
                  <a class="nav-link tab-link {{$completed_tab}} p-0 pr-2 pl-2" 
                  href="{{ url($crud->route)}}?status=completed" role="tab">कार्य संम्पन्न</a>
              </li>
              <li role="presentation" class="nav-item border border-white ">
                  <a class="nav-link tab-link {{$selected_not_started_tab}} p-0 pr-2 pl-2" 
                  href="{{ url($crud->route)}}?status=selected_not_started" role="tab">स्वीकृत भएको तर कार्य सुचारु नभएको </a>
              </li>
              {{-- <li role="presentation" class="nav-item border border-white ">
                  <a class="nav-link tab-link {{$all_tab}} p-0 pr-2 pl-2" 
                  href="{{ url($crud->route)}}?status=all" role="tab">सबै</a>
              </li> --}}
          </ul>
        </div>
  </div>
 
 