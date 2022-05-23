
   @php
    $sum = 0;
    $fiscal_year = null;
    $local_level_name = null;
    $district = null;
    $province = null;

    if(($data !== null && (isset($data) && count($data) > 0) )){
        $fiscal_year = $data[0]->fiscal_year_name;
        $local_level_name = $data[0]->local_level;
        $district = $data[0]->district;
        $province = $data[0]->province;
    }
   @endphp

 <div class="card">
    <div class="row mt-2">
        <div class="col">
            <center><h5 class="font-weight-bold">अनुसूची - ४</h5>
            <h6 style="text-decoration:underline;color:DodgerBlue;"><b>( निर्देशिकाको दफा ८(१) संग सम्बन्धित )<br/>आयोजना कार्यक्रम माग फारम</b></h5></center>
        </div>
    </div>
        <div class="row">
            <div class="col" style="margin-top:-70px;">
                <a href="javascript:;" class="btn btn-sm btn-primary la la-file-excel float-right mr-2" onclick="printAnusuchiThreeReport('EXCEL')"> Export to Excel</a>
                <a href="javascript:;" class="btn btn-sm btn-success la la-file-pdf float-right mr-3" onclick="printAnusuchiThreeReport('PDF')"> Export to PDF</a>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped table-sm" style="background-color:lightgrey;">
                        <colgroup>
                            <col style="width: 50%;" />
                            <col style="width: 50%;" />
                        </colgroup>
                    <tr>
                        <td class="report-data">१. आ.ब. :-  @englishToNepaliDigits($fiscal_year)</td>
                        @if(!backpack_user()->isClientUser())
                        <td class="report-data">२. महानगर/उप-महानगर/नगर/गाउ पालिकाको नाम :-  <span id="local_level_name"></span></td>
                        @else
                        <td class="report-data">२. महानगर/उप-महानगर/नगर/गाउ पालिकाको नाम :- {{$local_level_name}}</td>
                        @endif
                    </tr>
                    <tr>
                        @if(!backpack_user()->isClientUser())
                        <td class="report-data">३. जिल्ला :- <span id="district_name"></span></td>
                        @else
                        <td class="report-data">३. जिल्ला :- {{$district}}</td>
                        @endif
                        @if(!backpack_user()->isClientUser())
                        <td class="report-data">४. प्रदेश :- <span id="province_name"></span></td>
                        @else
                        <td class="report-data">४. प्रदेश :- {{$province}}</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div> 
            <div class="row mt-0">
                <div class="col-md-12">
                        <div class="col p-2" style="overflow-x:auto;">
                            <table id="anusuchi_3_data_table" class="table table-bordered table-striped table-sm" style="background-color:lightgrey;">
                                <thead>
                                    <tr>
                                        <th class="report-heading" rowspan="2">क्र.सं.</th>
                                        @if(!backpack_user()->isClientUser())
                                        <th class="report-heading" rowspan="2">जिल्ला</th>
                                        <th class="report-heading" rowspan="2">पालिकाको नाम</th>
                                        @endif
                                        <th class="report-heading th_large" rowspan="2">प्रस्तावित आयोजना/कार्यक्रमको नाम </th>
                                        <th class="report-heading th_large1" rowspan="2">कार्यक्रम सन्चालन हुने स्थान (वडा न. समेत) </th>
                                        <th class="report-heading th_large1" rowspan="2">आयोजना क्षेत्र</th>
                                        <th class="report-heading" rowspan="2">भौतिक लक्ष्य परिमाणमा</th>
                                        <th colspan="3" class="report-heading">अनुमानित लागत</th>
                                        <th class="report-heading" rowspan="2">आयोजना सम्पन्न हुने अबधि (महिना)</th>
                                        <th class="report-heading" rowspan="2">आयोजनाबाट लाभान्वित जनसंख्या</th>
                                        <th class="report-heading" rowspan="2">डी.पि. आर/ ल.ई भए/नभएको</th>
                                        <th class="report-heading" rowspan="2">कैफियत</th>
                                    </tr>
                                    <tr>
                                    
                                        <th class="report-heading"><b>केन्द्रीय अनुदान </b></th>
                                        <th class="report-heading"><b>स्थानीय तहबाट बेहोरिने रकम ( न्यूनतम १०% ) </b></th>
                                        <th class="report-heading"><b>कुल लागत</b></th>
                                    </tr>
            
                                </thead>
                                <tbody>
                                    @if($data !== null && (isset($data) && count($data) > 0) )
                                        @foreach($data as $row)
                                        <tr>
                                            <td class="report-data">{{$loop->iteration}}</td>
                                            @if(!backpack_user()->isClientUser())
                                            <td class="report-data">{{$row->district}}</td>
                                            <td class="report-data">{{$row->local_level}}</td>
                                            @endif
                                            <td class="report-data">{{$row->name_lc}}</td>
                                            <td class="report-data">{{$row->description_lc}}</td>
                                            <td class="report-data">{{$row->category_name}}</td>
                                            <td class="report-data nepali_amount">{{$row->quantity}}</td>

                                            <td class="report-data nepali_amount">@englishToNepali($row->source_federal_amount)</td>
                                            <td class="report-data nepali_amount">@englishToNepali($row->source_local_level_amount)</td>
                                            <td class="report-data nepali_amount">@englishToNepali($row->project_cost)</td>
                                        
                                            <td class="report-data nepali_amount">{{$row->proposed_duration_months !== null ? $row->proposed_duration_months : '-'}}</td>
                
                                            <td class="report-data nepali_amount">{{$row->project_affected_population}}</td>
                                            <td class="report-data">{{$row->dpr_details}}</td>
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
        min-width:170px !important;
    }
    .th_large1{
        min-width:130px !important;
    }
    .nepali_amount{
        font-size:11px;
    }
</style>

<script>
    $(document).ready(function () {
        $('#anusuchi_3_data_table').DataTable({
            searching: false,
            paging: true,
            ordering:false,
            select: false,
            bInfo : true,
            lengthChange: false
        });

        let province_id = $("#province").val();
        if(province_id != null){
            let province_name = $("#province option:selected").text();
           $('#province_name').html(province_name);
        }

        let district_id = $("#district").val();
        if(district_id != ""){
            let district_name = $("#district option:selected").text();
           $('#district_name').html(district_name);
        }

        let local_level_id = $("#local_level").val();
        if(local_level_id != ""){
            let local_level_name = $("#local_level option:selected").text();
           $('#local_level_name').html(local_level_name);
        }

    });

    function printAnusuchiThreeReport(type)
    {
      let data = '';
      if($('#fiscal_year_id').val() !== '') {
        data += 'fiscal_year=' + $('#fiscal_year_id').val();
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
      data += (data !== '' ? '&' : '') + 'type=' + type;
     
      if(data !== '') {
        window.open('/admin/anusuchi_three_report_data?' + data);
      }

    }
    
    </script>
