@extends(backpack_view('layouts.plain'))

@section('content')
    <div class="row card" style="width: 100%">
        <div class="card-header">
            <div class="form-row">
                <div class="col p-1 ml-3">
                    <div class="nav nav-pills">
                        <a class="p-1 pl-3 pr-3 tab-btn nav-link bg-primary text-white" 
                        id="btn-graphical" href="javascript:;"><i class="fa fa-sitemap"></i> Graphical</a> 

                        <a class="p-1 pl-3 tab-btn pr-3 nav-link bg-primary text-white" 
                        id="btn-tabular" href="javascript:;"><i class="fa fa-table"></i> Tabular</a> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col p-0 border rounded p-2" id="body-content"></div>
    </div>

@endsection

    @push('after_scripts')
        <script>
            $(document).ready(function(){

                $('a.tab-btn').click(function(event) {
                    if(!$(this).hasClass('bg-primary')) {
                        return false;
                    }
                    $('#body-content').html('<div class="text-center mt-5"><img src="/gif/loading.gif"/></div>');
                    event.preventDefault();
                    let key = $(this).attr('id');
                    loadBodyContent(key);

                    $('a.tab-btn').removeClass('bg-primary bg-success text-white').addClass('bg-primary text-white')
                    $(this).removeClass('bg-primary bg-success text-dark text-white').addClass('bg-success text-white')
                });

                loadBodyContent = (key) => {
                    let url = '/home/get-page-content';
                    $.get(url,{key:key}, function(response) {
                        $('#body-content').html(response);
                    });
                }

                // Load default tab
                $('a.tab-btn:first-child').click();

            }); 
        </script>

    @endpush

   

  

