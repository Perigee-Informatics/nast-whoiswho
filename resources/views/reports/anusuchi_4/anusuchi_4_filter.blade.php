@extends(backpack_view('blank'))
<script src="{{asset('js/jquery-1.9.1.min.js') }}"></script>
<script src="{{asset('js/dependentdropdown.js') }}"></script>

@section('content') 
<div class="card">
		<div class="card">
			<div class="card-header bg-primary p-1"><i class="la la-search"aria-hidden="true"></i>Search</div>
		
			<div class="card-body p-0">
				@if(!backpack_user()->isClientUser())
					<div class="form-row p-2">
						<div class="col-md-4">
							<select class="form-control select2" name="province" id="province" style="width: 100%;" onchange="getAnusuchiData()">
								<option selected disabled style="font-weight:bold;">--प्रदेश छान्नुहोस्--</option>
								@foreach($project_province as $y)
								<option class="form-control" value="{{ $y->id }}">{{ $y->code }}-{{ $y->name_lc }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-4">
							<select class="form-control select2" style="width: 100%;" name="district" id="district" onchange="getAnusuchiData()">
								<option disabled style="font-weight:bold;">--पहिला प्रदेश छान्नुहोस्--</option>
							</select>
						</div>
						<div class="col-md-4">
							<select class="form-control select2" style="width: 100%;" name="local_level" id="local_level" onchange="getAnusuchiData()">
								<option disabled style="font-weight:bold;">--पहिला जिल्ला छान्नुहोस्--</option>
							</select>
						</div>
					</div>
				@endif
					<div class="form-row p-2">
						<div class="col-md-4">
							<select class="form-control select2" style="width: 100%;" name="fiscal_year_id" id="fiscal_year_id" onchange="getAnusuchiData()">
								@if(isset($fiscal_year_id))
									<option value="all" selected>सबै</option>
									@foreach ($fiscal_year as $option)
										@if(intval($fiscal_year_id) === $option->getKey())
											<option value="{{ $fiscal_year_id }}" selected }}>{{ $option->code }}</option>
										@else
											<option value="{{ $option->getKey() }}" >{{ $option->code }}</option>
										@endif    
									@endforeach
									@else
									<option value="" selected>सबै</option>
									@foreach ($fiscal_year as $option)
											<option value="{{ $option->getKey() }}" >{{ $option->code }}</option>
									@endforeach
								@endif 
							</select>
						</div>
						<div class="col-md-4">
							<select class="form-control select2" style="width: 100%;" name="reporting_interval_id" id="reporting_interval_id" onchange="getAnusuchiData()">
								<option selected disabled>-- अवधि  छान्नुहोस्--</option>
								@foreach($project_interval as $p_interval)
								<option class="form-control" value="{{ $p_interval->id }}">{{ $p_interval->name_lc }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<a class="btn btn-warning btn-sm float-right" href="{{route('anusuchi_4')}}" type="reset"><i class="la la-refresh"></i>Reset</a>
						</div>
					</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<div class="card h-100">
					<div id="anusuchi_4_report_data"></div>
				</div>
			</div>
		</div>
	</div>
@endsection


@push('after_scripts')
<script>
$(document).ready(function () {
    getAnusuchiData();
});

function getAnusuchiData(){
		let data = {
			 fiscal_year : $('#fiscal_year_id').val(),
			 province : $('#province').val(),
			 district : $('#district').val(),
			 local_level : $('#local_level').val(),
			 reporting_interval_id : $('#reporting_interval_id').val(),
		}
		$('#anusuchi_4_report_data').html('<div class="text-center"><img src="/css/images/loading.gif"/></div>');
        $.ajax({
            type: "POST",
            url: "/admin/anushifourreportdata",
            data: data,
            success: function(response){
                $('#anusuchi_4_report_data').html(response);
            }
        });
 }

</script>
@endpush

