<html>
<div class="reports">
    <table class="table table-bordered table-striped table-sm" border="1" style="border-collapse: collapse;background-color: #f1f1c1;" id='anusuchi'>
    <thead>
      <tr>
        <th>क्र.सं.</th>
        @if(!backpack_user()->isClientUser())
        <th style="text-align:center">जिल्ला</th>
        <th style="text-align:center">पालिकाको नाम</th>
        @endif
        <th style="text-align:center">प्रस्तावित आयोजना/कार्यक्रमको नाम </th>
        <th style="text-align:center">कार्यक्रम सन्चालन हुने स्थान (वडा न. समेत) </th>
        <th style="text-align:center">आयोजना क्षेत्र</th>
        <th style="text-align:center">भौतिक लक्ष्य परिमाणमा</th>
        <th colspan="3" style="text-align:center">अनुमानित लागत</th>
        <th style="text-align:center">आयोजना सम्पन्न हुने अबधि (महिना)</th>
        <th style="text-align:center">आयोजनाबाट लाभान्वित जनसंख्या</th>
        <th style="text-align:center">डी.पि. आर/ ल.ई भए/नभएको</th>
        <th style="text-align:center">कैफियत</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align:center"><b>केन्द्रीय अनुदान </b></td>
        <td style="text-align:center"><b>स्थानीय तहबाट बेहोरिने रकम ( न्यूनतम १०% ) </b></td>
        <td style="text-align:center"><b>कुल लागत</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </thead>
    <tbody>
      @if($data != null)
     @foreach($data as $row)
     <tr>
      <td class="nepali_td">{{$loop->iteration}}</td>
      @if(!backpack_user()->isClientUser())
      <td>{{$row->district}}</td>
      <td>{{$row->local_level}}</td>
      @endif
      <td>{{$row->name_lc}}</td>
      <td>{{$row->description_lc}}</td>
      <td>{{$row->category_name}}</td>
      <td>@englishToNepaliDigits($row->quantity)</td>

      <td>@englishToNepali($row->source_federal_amount)</td>
      <td>@englishToNepali($row->source_local_level_amount)</td>
      <td>@englishToNepali($row->project_cost)</td>
      <td>{{$row->proposed_duration_months !== null ? $row->proposed_duration_months : '-'}}</td>
      <td>@englishToNepaliDigits($row->project_affected_population)</td>
      <td>{{$row->dpr_details}}</td>
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