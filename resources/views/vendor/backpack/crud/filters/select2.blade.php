{{-- Select2 Backpack CRUD filter --}}
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
    <div class="dropdown-menu-1 p-0 mb-2">
      <div class="form-group backpack-filter mb-0">
{{-- {{dd($filter->options['attributes']['onChange'])}} --}}
			<select id="filter_{{ $filter->key }}" name="filter_{{ $filter->key }}" {{ isset($filter->options['attributes']['onChange']) ? 'onChange='.$filter->options['attributes']['onChange'] : '' }} data-filter-key="{{ $filter->key }}" class="form-control input-sm select2" data-filter-type="select2" data-filter-name="{{ $filter->name }}" placeholder="{{ $filter->placeholder }}">
				<option value="">-</option>
				@if (is_array($filter->values) && count($filter->values))
					@foreach($filter->values as $key => $value)
						<option value="{{ $key }}"
							@if($filter->isActive() && $filter->currentValue == $key)
								selected
							@endif
							>
							{{ $value }}
						</option>
					@endforeach
				@endif
			</select>
		</div>
    </div>
  </li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

@push('crud_list_styles')
    <!-- include select2 css-->
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
	  .form-inline .select2-container {
	    display: inline-block;
	  }
	  .select2-drop-active {
	  	border:none;
	  }
	  .select2-container .select2-choices .select2-search-field input, .select2-container .select2-choice, .select2-container .select2-choices {
	  	border: none;
	  }
	  .select2-container-active .select2-choice {
	  	border: none;
	  	box-shadow: none;

	  }
	  .select2-container--bootstrap .select2-dropdown {
	  	margin-top: -2px;
	  	margin-left: -1px;
		width:270px !important;

	  }
	  .select2-container--bootstrap {
	  	position: relative!important;
	  	top: 0px!important;
	  }
    </style>
@endpush


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
	<!-- include select2 js-->
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ asset('packages/select2/dist/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    @endif

    <script>
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2 box
            $('select[data-filter-type=select2]').not('[data-filter-enabled]').each(function () {
            	var filterName = $(this).attr('data-filter-name');
                var filter_key = $(this).attr('data-filter-key');
            	var element = $(this);

            	$(this).attr('data-filter-enabled', 'true');

            	var obj = $(this).select2({
	            	allowClear: true,
		            closeOnSelect: true,
					theme: "bootstrap",
					dropdownParent: $(this).parent('.form-group'),
	        	    placeholder: $(this).attr('placeholder'),
					// dropdownAutoWidth : true,
    				width: 'auto',
	            });

				$(this).change(function() {
					var value = $(this).val();
					var parameter = $(this).attr('data-filter-name');

			    	// behaviour for ajax table
					var ajax_table = $("#crudTable").DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// replace the datatables ajax url with new_url and reload it
					new_url = normalizeAmpersand(new_url.toString());
					ajax_table.ajax.url(new_url).load();

					// add filter to URL
					crud.updateUrl(new_url);

					// mark this filter as active in the navbar-filters
					if (URI(new_url).hasQuery(parameter, true)) {
						$("li[filter-key="+filter_key+"]").removeClass('active').addClass('active');
					}
					else
					{
						$("li[filter-key="+filter_key+"]").removeClass("active");
						$("li[filter-key="+filter_key+"]").find('.dropdown-menu').removeClass("show");
					}
				});

				// when the dropdown is opened, autofocus on the select2
				$("li[filter-key="+filter_key+"]").on('shown.bs.dropdown', function () {
					$('select[data-filter-key='+filter_key+']').select2('open');
				});

				// clear filter event (used here and by the Remove all filters button)
				$("li[filter-key="+filter_key+"]").on('filter:clear', function(e) {
					// console.log('select2 filter cleared');
					$("li[filter-key="+filter_key+"]").removeClass('active');
	                $('#filter_'+filter_key).val(null).trigger('change');
				});
            });
		});
	</script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
