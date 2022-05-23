<html>

<head>
  <meta charset="utf-8">
  <style>
    
    @font-face {
                font-family: 'Kalimati';
                font-style: normal;
                font-weight: normal;
                src: url({#asset /Kalimati.ttf @encoding=dataURI});
                format('truetype');
      }

      @page{
        size: A4 landscape;
        margin-top: 10px;
        margin-left: 50px;
        margin-right: 10px;
      }

      .heading-report{
        margin-top:10px;
      }
      .report-heading {
        text-align: center;
        font-size:13px;
        font-family: 'Kalimati';
        background-color:lightgray !important;

    }
    .report-data {
        font-size:13px;
        font-weight: 500;
        color:black;
        font-family: 'Kalimati';
    }
    #anusuchi_3_data_table{
      margin-top:15px;
    }
    #anusuchi_3_data_table .report-data{
      font-size:12px;
      border-bottom: 1px solid black !important;
      border-right: 1px solid black !important;
    }
    tr>th{
        border-bottom: 1px solid black !important;
        border-right: 1px solid black !important;
        color:black;
    }

    .th_large{
        min-width:200px !important;
    }
    .th-width-100{
      min-width:100px !important;

    }
    .nepali_amount{
      text-align: center;
        font-size:10px;
    }
    .prepared-by{
        text-align:right;
        margin-right: 100px;

      }
      .footer-data{
        text-align:right;
        margin-right: 200px;
        padding: 5px;
      }
  </style>
</head>


<body class="main">
  <div class="box">
    <div class="heading-report">
      <h2 style="text-align:center; margin-bottom:-10px;"><b>अनुसूची - ४</b></h2>
      <h3 style="text-align:center; margin-bottom: -15px;"><b>( निर्देशिकाको दफा ८(१) संग सम्बन्धित )</b></h3>
      <h4 style="text-align:center;"><b>आयोजना कार्यक्रम माग फारम</b> </h4>
    </div>
   @php
    $fiscal_year = null;
    $district = null;
    $province = null;

    if(($data !== null && (isset($data) && count($data) > 0) )){
        $fiscal_year = $data[0]->fiscal_year_name;
        $district = $data[0]->district;
        $province = $data[0]->province;
    }
  @endphp

    <div class="intro" style="margin-left:10px;">
      <table class="table table-bordered table-striped table-sm" width="100%">
        <colgroup>
            <col style="width: 50%;" />
            <col style="width: 50%;" />
        </colgroup>
    <tr>
        <td class="report-data">१. आ.ब. :-  @englishToNepaliDigits($fiscal_year)</td>
        <td class="report-data">२. महानगर/उप-महानगर/नगर/गाउ पालिकाको नाम :-  {{$local_level_name}}</td>
    </tr>
    <tr>
        <td class="report-data">३. जिल्ला :- @if(!backpack_user()->isClientUser()) {{$district_name}} @else {{$district}} @endif </td>
        <td class="report-data">४. प्रदेश :- @if(!backpack_user()->isClientUser()) {{$province_name}} @else {{$province}} @endif </td>
    </tr>
</table>
     
    </div>
    <div class="reports">
      <table id="anusuchi_3_data_table" class="table table-bordered table-striped table-sm">
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
       
    <div class ="row prepared-by">
      <h5 style="text-decoration: underline;">माग एव सिफारिस गर्नेको</h5>
    </div>
      <div class="row footer-data">
        <tr>
          <td>नाम :-  </td> <br />
          <td>दरखास्त :-  </td><br />
          <td>छाप :-  </td><br />
          <td>मिति :-  </td><br />
      </tr>
      </div>
  </div>

</body>

</html>