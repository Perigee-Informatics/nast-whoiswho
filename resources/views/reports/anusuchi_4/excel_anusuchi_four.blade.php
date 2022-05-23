<html>
<div class="reports">
    <table class="table table-bordered table-striped table-sm" border="1" style="border-collapse: collapse;background-color: #f1f1c1;" id='anusuchi'>
    <thead>
      <tr>
        <th>क्र.सं.</th>
        <th style="text-align:center">संचालित आयोजना/कार्यक्रमको नाम </th>
        <th colspan="3" style="text-align:center">श्रोत</th>
        <th colspan="3" style="text-align:center">कार्यक्रम संचालन प्रक्रिया</th>
        <th>इकाई </th>
        <th colspan="3" style="text-align:center">बार्षिक लक्ष्य </th>
        <th colspan="3" style="text-align:center">यस अबधिसम्मको प्रगति</th>
        <th colspan="2" style="text-align:center">यस अबधिसम्मको खर्च</th>
        <th style="text-align:center">लाभान्वित समुदाय जनसंख्या </th>
        <th>कैफियत </th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td style="text-align:center"><b>केन्द्रीय अनुदान रकम </b></td>
        <td style="text-align:center"><b>स्थानीय तह लागत साझेदारी </b></td>
        <td style="text-align:center"><b>कुल रकम</b></td>
        <td style="text-align:center"><b>उपभोक्ता समिति </b></td>
        <td style="text-align:center"><b>ठेक्का </b></td>
        <td style="text-align:center"><b>अन्य</b></td>
        <td></td>
        <td style="text-align:center"><b>परिमाण</b></td>
        <td style="text-align:center">भार </td>
        <td style="text-align:center">बजेट </td>

        <td style="text-align:center"><b>परिमाण</b></td>
        <td style="text-align:center">भार </td>
        <td style="text-align:center">प्रतिशत (%)</td>
        <td>रकम रु. </td>
        <td>प्रतिशत (%)</td>
        <td></td>
        <td></td>
    </tr>
    </thead>
    <tbody>
      @if($data != null)
     @foreach($data as $row)
   <tr>
    <td class="nepali_td">{{$loop->iteration}}</td>
    <td>{{$row->name_lc}} ({{$row->description_lc}})</td>
    <td class="nepali_td">@englishToNepali($row->source_federal_amount)</td>
    <td class="nepali_td">@englishToNepali($row->source_local_level_amount)</td>
    <td>@englishToNepali($row->project_cost)</td>

    <td>{{$row->executing_type_uc}}</td>
    <td>{{$row->executing_type_contract}}</td>
    <td>{{$row->executing_type_other}}</td>
    <td>@englishToNepaliDigits($row->unit)</td>
    <td class="nepali_td">@englishToNepaliDigits($row->quantity)</td>
    <td class="nepali_td">@englishToNepaliDigits($row->weightage)</td>
    <td></td>
    <td class="nepali_td">@englishToNepaliDigits($row->pragati_quantity)</td>
    <td class="nepali_td">@englishToNepaliDigits($row->pragati_weightage)</td>
    <td>@englishToNepaliDigits($row->physical_progress_percent)</td>
    <td class="nepali_td">@englishToNepali($row->financial_progress_amount)</td>
    <td class="nepali_td">@englishToNepaliDigits($row->financial_progress_percent)</td>
    <td class="nepali_td">@englishToNepali($row->project_affected_population)</td>
    <td>{{$row->remarks}}</td>
   </tr>
   @endforeach
   @endif
 </tbody>
    </table>
    <!--Table-->
<!--Table-->
</div>

</body>
</html>