
   @php
    $sum = 0;
    $fiscal_year = null;
    $local_level_name = null;
    $incharge_name = null;

    if(($data !== null && (isset($data) && count($data) > 0) )){
        $fiscal_year = $data[0]->fiscal_year_name;
        $local_level_name = $data[0]->local_level;
        $incharge_name = $data[0]->incharge_name;

        foreach($data as $dat)
        {
        $totalprojectcost=$dat->project_cost;
        $sum+=$totalprojectcost;
        }
    }
   @endphp

 <div class="card">
    <div class="heading-report">
     
        <div class="row mt-2">
            <div class="col">
                <center><h5 class="font-weight-bold">अनुसूची - ५</h5>
                <h6 style="text-decoration:underline;color:DodgerBlue;"><b>निर्देशिकाको दफा-१० को खण्ड 'ख' संग सम्बन्धित<br/> <span id="reporting_interval_name"></span> प्रतिवेदन</b></h5></center>
            </div>
        </div>

        <div class="row">
            <div class="col" style="margin-top:-70px;">
                <a href="javascript:;" class="btn btn-sm btn-primary la la-file-excel float-right mr-2" onclick="printReport('EXCEL')"> Export to Excel</a>
                <a href="javascript:;" class="btn btn-sm btn-success la la-file-pdf float-right mr-3" onclick="printReport('PDF')"> Export to PDF</a>
            </div>
        </div>
    </div>

        <div class="row">
            <div class="col-md-12">
                      
                        <table class="table table-bordered table-striped table-sm" style="background-color:lightgrey;">
                         <colgroup>
                        <col style="width: 33%;" />
                        <col style="width: 33%;" />
                        <col style="width: 33%;" />
                        </colgroup>
                            <tr>
                                <td class="report-data">१. आ.ब. :-  @englishToNepaliDigits($fiscal_year)</td>
                                <td class="report-data">२. मन्त्रालय :- सङ्घीय मामिला तथा सामान्य प्रशासन मन्त्रालय</td>
                                <td class="report-data">३. कार्यक्रमको नाम :- तराई मधेश समृद्धि कार्यक्रम</td>
                            </tr>
                            <tr>
                                @if(!backpack_user()->isClientUser())
                                <td class="report-data">४. स्थानीय तह :- <span id="local_level_name"></span></td>
                                @else
                                <td class="report-data">४. स्थानीय तह :- {{$local_level_name}}</td>
                                @endif
                                <td class="report-data">५. आयोजना प्रमुखको नाम :- {{$incharge_name}}</td>
                                <td class="report-data">६. स्वीकृत रकम :- रु. @englishToNepali($sum)</td>
                            </tr>
                       </table>
            </div>
        </div> 
            <div class="row mt-0">
                <div class="col-md-12">
                        <div class="col p-2" style="overflow-x:auto;">
                            <table id="anusuchi_4_data_table" class="table table-bordered table-striped table-sm" style="background-color:lightgrey;">
                                <thead>
                                    <tr>
                                        <th class="report-heading" rowspan="2">क्र.सं.</th>
                                        <th class="report-heading th_large" rowspan="2">संचालित आयोजना/कार्यक्रमको नाम</th>
                                        <th class="report-heading" colspan="3">श्रोत</th>
                                        <th class="report-heading" colspan="3">कार्यक्रम संचालन प्रक्रिया</th>
                                        <th class="report-heading" rowspan="2">इकाई </th>
                                        <th class="report-heading" colspan="3">बार्षिक लक्ष्य </th>
                                        <th class="report-heading" colspan="3">यस अबधिसम्मको प्रगति</th>
                                        <th class="report-heading" colspan="2">यस अबधिसम्मको खर्च</th>
                                        <th class="report-heading" rowspan="2">लाभान्वित समुदाय जनसंख्या </th>
                                        <th class="report-heading th_large" rowspan="2">कैफियत </th>
                                    </tr>
                                    <tr>
                                        <th class="report-heading">केन्द्रीय अनुदान रकम </th>
                                        <th class="report-heading">स्थानीय तह लागत साझेदारी </th>
                                        <th class="report-heading">कुल रकम</th>

                                        <th class="report-heading">उपभोक्ता समिति </th>
                                        <th class="report-heading">ठेक्का </th>
                                        <th class="report-heading">अन्य</th>

                                        <th class="report-heading">परिमाण</th>
                                        <th class="report-heading">भार </th>
                                        <th class="report-heading">बजेट </th>

                                        <th class="report-heading">परिमाण</th>
                                        <th class="report-heading">भार </th>
                                        <th class="report-heading">प्रतिशत (%) </th>
                                        <th class="report-heading">रकम रु. </th>
                                        <th class="report-heading">प्रतिशत (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if($data !== null && (isset($data) && count($data) > 0) )
                                    @foreach($data as $row)
                                    <tr>
                                        <td class="report-data">{{$loop->iteration}}</td>
                                        <td class="report-data">{{$row->name_lc}} ({{$row->description_lc}})</td>
                                        <td class="report-data nepali_amount">@englishToNepali($row->source_federal_amount)</td>
                                        <td class="report-data nepali_amount">@englishToNepali($row->source_local_level_amount)</td>
                                        <td class="report-data nepali_amount">@englishToNepali($row->project_cost)</td>

                                        <td class="report-data">{{$row->executing_type_uc}}</td>
                                        <td class="report-data">{{$row->executing_type_contract}}</td>
                                        <td class="report-data">{{$row->executing_type_other}}</td>
                                        <td class="report-data">{{$row->unit}}</td>
                                        <td class="report-data nepali_amount">@englishToNepaliDigits($row->quantity)</td>
                                        <td class="report-data nepali_amount">@englishToNepaliDigits($row->weightage)</td>
                                        <td class="report-data nepali_amount"></td>
                                        <td class="report-data nepali_amount">@englishToNepaliDigits($row->pragati_quantity)</td>
                                        <td class="report-data nepali_amount">@englishToNepaliDigits($row->pragati_weightage)</td>
                                        <td class="report-data nepali_amount">{{$row->physical_progress_percent}}</td>
                                        <td class="report-data nepali_amount">@englishToNepali($row->financial_progress_amount)</td>
                                        <td class="report-data nepali_amount">{{$row->financial_progress_percent}}</td>
                                        <td class="report-data nepali_amount">@englishToNepali($row->project_affected_population)</td>
                                        <td class="report-data">{{$row->remarks}}</td>
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
        font-weight: 600;
        color:black;
        font-family: 'Kalimati';
    }
    tr>th{
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
        background-color:#3B72A0 !important;
        color:white;
    }

    .th_large{
        min-width:200px !important;
    }
    .nepali_amount{
        font-size:11px;
    }
</style>

<script>
    $(document).ready(function () {
        $('#anusuchi_4_data_table').DataTable({
            searching: false,
            paging: true,
            ordering:false,
            select: false,
            bInfo : true,
            lengthChange: false
        });
        let local_level_id = $("#local_level").val();
        if(local_level_id != ""){
            let local_level_name = $("#local_level option:selected").text();
           $('#local_level_name').html(local_level_name);
        }

        let reporting_interval_id = $('#reporting_interval_id').val();
        if(reporting_interval_id != "" && reporting_interval_id != null){
            let reporting_interval_name = $("#reporting_interval_id option:selected").text();
           $('#reporting_interval_name').html(reporting_interval_name);
        }
       
    });

    function printReport(type)
    {
      let data = '';
      if($('#fiscal_year_id').val() !== '') {
        data += 'fiscal_year_id=' + $('#fiscal_year_id').val();
      }
      if($('#province').val() !== '') {
        data += (data !== '' ? '&' : '') + 'province=' + $('#province').val();
      }
      if($('#district').val() !== '') {
        data += (data !== '' ? '&' : '') + 'district=' + $('#district').val();
      }
      if($('#local_level').val() !== '') {
        data += (data !== '' ? '&' : '') + 'local_level=' + $('#local_level').val();
      }
      if($('#reporting_interval_id').val() !== '') {
        data += (data !== '' ? '&' : '') + 'reporting_interval_id=' + $('#reporting_interval_id').val();
      }
      data += (data !== '' ? '&' : '') + 'type=' + type;
     
      if(data !== '') {
        window.open('/admin/anusuchi_four_report_data?' + data);
      }

    }
    
    </script>
