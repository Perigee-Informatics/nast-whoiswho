<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta charset="utf-8">
  <style>
      @page{
        size: A4 portrait;
        margin-top: 10px;
        margin-left: 30px;
        margin-right: 20px;
      }

      .header{
          color: black;
          opacity:.8
      }
      .name{
          margin-left:20px;
          font-size:18px;
          font-weight: bold;
          color:rgb(29, 29, 170);
      }
      .table-data{
          margin-top: 20px;
          margin-left: 20px;
      }
      .row-title{
          padding:7px 0;
          font-weight:600;
      }
      .fa{
          font-size: 16px;
          color:black;
      }
  
  </style>
</head>


<body class="main">
    <div class="header">
        Who is Who in Science, Technology and Innovation of Nepal
    </div>
    <div class="hr-line">
        <hr/>
    </div>

    <div class="profile">
        <span class="name"><i class="fa fa-arrow-circle-right"></i>&nbsp;&nbsp;{{ $member->first_name .' '.$member->middle_name.' '. $member->last_name}}
            ({{ $member->genderEntity->name_en.'; '.$member->dob_ad}} 
            {{$member->district_id ?'; '.ucwords(strtolower($member->districtEntity->name_en)): ''}}
            {{$member->is_other_country==true ? '; '.$member->countryEntity->name_en : '; Nepal' }})
        </span>
        <div class="table-data">
            <table width="100%">
                <colgroup>
                    <col style="width: 30%;" />
                    <col style="width: 80%;" />
                </colgroup>
                    <tr>
                        <td class="row-title">Category:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Current Affiliation:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Past Experiences:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Education:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Awards:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Expertise:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Professional Affiliation:</td>
                        <td class="row-data"></td>
                    </tr>
                    <tr>
                        <td class="row-title">Correspondence:</td>
                        <td class="row-data"></td>
                    </tr>
            </table>
        </div>


    </div>
</body>

</html>