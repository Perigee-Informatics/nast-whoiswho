<div class="card">
    <div class="form-row heading-report">
        <div class="col-md-7 mt-2">
            <h5 style="color:DodgerBlue; text-align:right; text-decoration:underline"><b> प्रगति प्रबिस्ट प्रतिबेदन</b></h5>
        </div>
        <div class="col mt-2">
            <a class="la la-file-excel btn btn-success float-right mr-2" href="{{route('generateprogressexcel',['download_xls'=>'xls'])}}" target="_blank">Export to Excel</a>
            <a class="la la-file-pdf btn btn-primary float-right mr-3" href="{{route('generateprogresspdf',['download_pdf'=>'pdf'])}}" target="_blank">Export to PDF</a>
        </div>
    </div>
        <div class="row mt-0">
            <div class="col-md-12">
                    <div class="col p-2" style="overflow-x:auto;">
                        <table id="project_progress_data_table" class="table table-bordered table-striped table-sm" style="background-color: lightgrey;">
                            <thead>
                                <tr>
                                    <th class="report-heading">क्र.सं.</th>
                                    @if(!backpack_user()->isClientUser())
                                    <th class="report-heading">प्रदेश </th>
                                    <th class="report-heading">जिल्ला </th>
                                    <th class="report-heading">स्थानीय तह </th>
                                    @endif
                                    <th class="report-heading">योजना/कार्यक्रमको नाम </th>
                                    <th class="report-heading">प्रतिबेदन अबधि </th>
                                    <th class="report-heading">आर्थिक वर्ष</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data !== null )
                                @foreach($data as $row)
                                <tr>
                                    <td class="report-data">{{$loop->iteration}}</td>
                                    @if(!backpack_user()->isClientUser())
                                    <td class="report-data">{{$row->province}}</td>
                                    <td class="report-data">{{$row->district}}</td>
                                    <td class="report-data">{{$row->local_level}}</td>
                                    @endif
                                    <td class="report-data">{{$row->name_lc}}</td>
                                    <td class="report-data">{{$row->reporting_interval}}</td>
                                    <td class="report-data">@englishToNepaliDigits($row->fiscal_year_name)</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                </div>
            </div>
        </div>  
    </div>   
</div> 
<style>
    .report-heading {
        text-align: center;
        font-size:13px;
        font-family: 'Kalimati';

    }
    .report-data {
        font-size:13px;
        color:black;
        font-family: 'Kalimati';

    }
      tr>th{
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
        background-color:#3B72A0 !important;
        color:white;
    }
</style>

<script>
$(document).ready(function () {
	$('#project_progress_data_table').DataTable({
        searching: false,
        paging: true,
        ordering:false,
        select: false,
        bInfo : true,
        lengthChange: false
    });
});

</script>

