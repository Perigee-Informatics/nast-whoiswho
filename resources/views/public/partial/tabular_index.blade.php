<style>
    .times-hidden{
        visibility: hidden !important;
    }
    .times-show{
        cursor: pointer;
        font-weight: bold !important;
        color:#cd201f!important;
        margin-left:0.25rem !important;
        font-size:larger !important;
    }
    .filter-active{
        background-color: rgb(140, 245, 195) !important;
        color: black !important;
    }
</style>

<div class="card">
    <div class="card">
        <div class="card-header bg-primary p-1 d-inline-block">
            <span><i class="fa fa-search" aria-hidden="true"></i>Members List</span>
            <span><a class="btn btn-warning btn-sm float-right text-dark font-weight-bold mr-4" href="javascript:;" onclick="getMembersData()"><i class="fa fa-refresh"></i>Refresh</a></span>
        </div>
        <div class="card-body p-0">
            <div class="form-row p-2">
               
                <div class="col d-inline-flex">
                    <select class="form-control searchselect" name="province_id" id="province_id" style="width: 100%;" onchange="getMembersData()">
                        <option class="text-mute" selected disabled value=""> -- Province --</option>
                        @foreach($provinces as $p)
                        <option class="form-control" value="{{ $p->id }}">{{ $p->name_en }}</option>
                        @endforeach
                    </select>
                    <button class="btn bg-light la la-times province_filter times-hidden font-weight-bold" onclick="filterClear(this)"></button>
                </div>
        
                <div class="col d-inline-flex">
                    <select class="form-control searchselect" name="district_id" id="district_id" style="width: 100%;" onchange="getMembersData()">
                        <option class="text-mute" selected disabled value=""> -- District --</option>
                        @foreach($districts as $d)
                        <option class="form-control" value="{{ $d->id }}">{{ $d->name_en }}</option>
                        @endforeach
                    </select>
                    <button class="btn bg-light la la-times district_filter times-hidden font-weight-bold" onclick="filterClear(this)"></button>
                </div>
                <div class="col d-inline-flex">
                    <select class="form-control searchselect" name="gender_id" id="gender_id" style="width: 100%;" onchange="getMembersData()">
                        <option class="text-mute" selected disabled value=""> -- Gender --</option>
                        @foreach($genders as $g)
                            <option class="form-control" value="{{ $g->id }}">{{ $g->name_en }}</option>
                        @endforeach
                    </select>
                    <button class="btn bg-light la la-times gender_filter times-hidden font-weight-bold" onclick="filterClear(this)"></button>
                </div>
                <div class="col d-inline-flex">
                    <select class="form-control searchselect" name="country_status" id="country_status" style="width: 100%;" onchange="getMembersData()">
                        <option class="text-mute" selected disabled value=""> -- Country --</option>
                        <option class="form-control" value="nepal">Nepal</option>
                        <option class="form-control" value="other">Other</option>
                    </select>
                    <button class="btn bg-light la la-times country_status_filter times-hidden font-weight-bold" onclick="filterClear(this)"></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card h-100">
                <div id="members_data"></div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        getMembersData();
    });

    function getMembersData() {
        let data = {
            province_id: $('#province_id').val(),
            district_id: $('#district_id').val(),
            gender_id: $('#gender_id').val(),
            country_status: $('#country_status').val(),
        }
        if($('#province_id').val()){
            $('.province_filter').removeClass('times-hidden').addClass('times-show');
        }
      
        if($('#district_id').val()){
            $('.district_filter').removeClass('times-hidden').addClass('times-show');
        }
       
        if($('#gender_id').val()){
            $('.gender_filter').removeClass('times-hidden').addClass('times-show');
        }
        if($('#country_status').val()){
            $('.country_status_filter').removeClass('times-hidden').addClass('times-show');
        }

        let active_ele = document.getElementsByClassName('times-show').forEach(function(ele){
            let itm = ele.previousElementSibling;
            $(itm).addClass('filter-active');
        });

        $('#members_data').html('<div class="text-center"><img src="/css/images/loading.gif"/></div>');
        // $.ajax({
        //     type: "POST",
        //     url: "/admin/report/payrollreportdata",
        //     data: data,
        //     success: function(response) {
        //         $('#members_data').html(response);
        //         $('#members_data_table').DataTable({
        //             searching: true,
        //             paging: true,
        //             ordering: true,
        //             select: false,
        //             bInfo: true,
        //             lengthChange: false
        //         });
        //     }
        // });
    }

    function filterClear(item){
        let element_name =item.parentElement.firstElementChild.getAttribute('name');
        if(element_name){
            $('select[name='+element_name+']').val('').trigger('change');

            $(item).removeClass('times-show').addClass('times-hidden');
            $('#'+element_name).removeClass('filter-active');
        }
    }
</script>