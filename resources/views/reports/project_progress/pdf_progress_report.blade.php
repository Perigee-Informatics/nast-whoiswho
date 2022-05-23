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
    td,
    th {
      padding: 4px;
    }

    .nepali_td {
      text-align: right;
    }
  </style>
</head>

<body class="main">
  <div class="box">
    <div class="heading-report">
      <h1 style="text-align:center"><b>प्रगति प्रबिस्ट प्रतिबेदन</b></h1>
     </div>
    <div class="reports">
      <table class="table table-bordered table-striped table-sm" border="1" style="border-collapse: collapse;" id='anusuchi'>
        <thead style="background-color: #f1f1c1;">
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
    </div>

  </div>

</body>
</html>