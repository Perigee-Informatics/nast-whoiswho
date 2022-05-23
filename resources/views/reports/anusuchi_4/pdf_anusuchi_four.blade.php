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
        margin-top: 0px;
        margin-left: 40px;
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
    #anusuchi_4_data_table{
      margin-top:15px;
    }
    #anusuchi_4_data_table .report-data{
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
    .sign-line {
			border-top: 1px solid #000;
			padding: 5px;
      margin: 50px;
		}
  </style>
</head>
  @php
  $sum = 0;
  $fiscal_year = null;
  $incharge_name = null;

  if(($data !== null && (isset($data) && count($data) > 0) )){
      $fiscal_year = $data[0]->fiscal_year_name;
      $incharge_name = $data[0]->incharge_name;

      foreach($data as $dat)
      {
      $totalprojectcost=$dat->project_cost;
      $sum+=$totalprojectcost;
      }
  }
  @endphp
<body class="main">
  <div class="box">
    <div class="heading-report">
      <h2 style="text-align:center; margin-bottom:-10px;"><b>अनुसूची - ५</b></h2>
      <h3 style="text-align:center; margin-bottom:-15px;"><b>निर्देशिकाको दफा-१० को खण्ड 'ख' संग सम्बन्धित</b></h3>
      <h4 style="text-align:center;"><b >{{$reporting_interval_name}} प्रतिवेदन </b> </h4>
    </div>
   
    <div class="header-info">
       <table class="table table-bordered table-striped table-sm" width="100%">
          <colgroup>
            <col style="width: 30%;" />
            <col style="width: 40%;" />
            <col style="width: 30%;" />
          </colgroup>
           <tr>
               <td class="report-data">१. आ.ब. :-  @englishToNepaliDigits($fiscal_year)</td>
               <td class="report-data">२. मन्त्रालय :- सङ्घीय मामिला तथा सामान्य प्रशासन मन्त्रालय</td>
               <td class="report-data">३. कार्यक्रमको नाम :- तराई मधेश समृद्धि कार्यक्रम</td>
           </tr>
           <tr>
               <td class="report-data">४. स्थानीय तह :- {{$local_level_name}}</td>
               <td class="report-data">५. आयोजना प्रमुखको नाम :- {{$incharge_name}}</td>
               <td class="report-data">६. स्वीकृत रकम :- रु. @englishToNepali($sum)</td>
           </tr>
    </div>

      <table id="anusuchi_4_data_table" class="table table-bordered table-striped table-sm">
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
                <th class="report-heading th-width-100" rowspan="2">कैफियत </th>
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
                <td class="report-data nepali_amount">{{$loop->iteration}}</td>
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
                <td class="report-data nepali_amount">@englishToNepali($row->physical_progress_percent)</td>
                <td class="report-data nepali_amount">@englishToNepali($row->financial_progress_amount)</td>
                <td class="report-data nepali_amount">@englishToNepali($row->financial_progress_percent)</td>
                <td class="report-data nepali_amount">@englishToNepali($row->project_affected_population)</td>
                <td class="report-data">{{$row->remarks}}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    <footer>
			<p class="sign-line" style="float: left;">&nbsp;योजना शाखा प्रमुख &nbsp;</p>
			<p class="sign-line" style="float: left; margin-left: 25%;"> &nbsp;आर्थिक प्रसाशन शाखा प्रमुख &nbsp;</p>
			<p class="sign-line" style="float: right;"> &nbsp;कार्यालय प्रमुख &nbsp;</p>
		</footer>
  </div>
  </div>

</body>

</html>