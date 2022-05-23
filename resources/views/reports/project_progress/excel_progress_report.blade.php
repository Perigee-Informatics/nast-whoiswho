<html>
<div class="reports">
    <table class="table table-bordered table-striped table-sm" border="1" style="border-collapse: collapse;background-color: #f1f1c1;" id='anusuchi'>
    <thead>
      <tr>
        <th>क्र.सं.</th>
        @if(backpack_user()->isClientUser() === false)
        <th>प्रदेश </th>
        <th>जिल्ला </th>
        <th>स्थानीय तह </th>
        @endif
        <th style="text-align:center">योजना/कार्यक्रमको नाम </th>
        <th style="text-align:center">प्रतिबेदन अबधि </th>
        <th style="text-align:center">आर्थिक वर्ष</th>
        </tr>
  
    </thead>
    <tbody>
      @if($data !== null )
      @foreach($data as $row)
      <tr>
          <td >{{$loop->iteration}}</td>
          @if(backpack_user()->isClientUser() === false)
          <td>{{$row->province}}</td>
          <td>{{$row->district}}</td>
          <td>{{$row->local_level}}</td>
          @endif
          <td>{{$row->name_lc}}</td>
          <td>{{$row->reporting_interval}}</td>
          <td>{{$row->fiscal_year_name}}</td>
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