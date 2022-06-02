<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta charset="utf-8">
  <style>
      @page{
        size: A4 portrait;
        margin-top: 10px;
        margin-left: 30px;
        margin-right: 30px;
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
          vertical-align:0%;
      }
      .inner-data{
          padding-bottom: 10px;
      }
      
      .inner-data li{
          padding-top: 5px;
      }
      .fa{
          font-size: 16px;
          color:black;
      }
      img{
          position: relative;
      }
      span.bracket-text{
          font-size: 14px !important;
          font-style: italic;
      }
      .subject{
          padding-top: 5px;
          margin-left: 30px;
      }
      .subject-title{
          font-weight: 600;
          font-size: 15px;
          padding-top: 5px;
          text-decoration: underline;
      }
      .subject-head{
          font-weight: 600;
          font-size: 15px;
          padding-top: 5px;
      }
      .subject-data{
        font-size: 15px;
        padding-top: 5px;
      }

      .education li {
          padding-bottom: 10px;
      }
      .contact div{
          padding-top: 10px;
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
                    <col style="width: 25%;" />
                    <col style="width: 60%;" />
                    <col style="width: 15%;" />
                </colgroup>
                    <tr>
                        <td class="row-title">Category:</td>
                        <td class="inner-data">
                            @foreach($json_data['expertise'] as $expertise)
                                @if($expertise->name !='')
                                    <li>{{ $expertise->name }}</li>
                                @endif
                            @endforeach
                        </td>
                        <td class="row-data">
                            <img style="border-radius:7px" src="{{$photo_encoded}}" 
                            width="100" height="100" class="size-thumbnail p-1"></td>
                        </td>
                    </tr>
               
                    <tr>
                        <td class="row-title">Current Affiliation:</td>
                        <td class="row-data" colspan="2">{{ $json_data['current_organization'][0]->position}}, 
                            {{$json_data['current_organization'][0]->organization}}, {{$json_data['current_organization'][0]->address}}</td>
                    </tr>
                    <tr>
                        <td class="row-title">Past Experiences:</td>
                        <td class="inner-data" colspan="2">
                            @foreach($json_data['past_organization'] as $dt)
                                @if($dt->position !='')
                                <li>{{ $dt->position }} <span class="bracket-text"> ( {{ $dt->organization}} )</span></li>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="row-title">Education:</td>
                        <td class="inner-data education" colspan="2">
                            @foreach($json_data['doctorate_degree'] as $dt)
                            <li>{{ $dt->degree_name }} <span class="bracket-text"> - {{ $dt->university_or_institution}}, {{$dt->country}}, {{$dt->year}}</span><br>
                            <div class="subject"><span class="subject-title">Subject / Research Title</span> : <span class="subject-data">{{$dt->subject_or_research_title}}</span></div></li>
                            @endforeach

                            @foreach($json_data['masters_degree'] as $dt)
                            <li>{{ ($dt->others_degree)? $dt->others_degree: $dt->degree_name }} <span class="bracket-text"> - {{ $dt->university_or_institution}}, {{$dt->country}}, {{$dt->year}}</span><br>
                            <div class="subject"><span class="subject-title">Subject / Research Title</span> : <span class="subject-data">{{$dt->subject_or_research_title}}</span></div></li>
                            @endforeach

                            @foreach($json_data['bachelors_degree'] as $dt)
                            <li>{{ ($dt->others_degree) ? $dt->others_degree: $dt->degree_name }} <span class="bracket-text"> - {{ $dt->university_or_institution}}, {{$dt->country}}, {{$dt->year}}</span><br>
                            <div class="subject"><span class="subject-title">Subject / Research Title</span> : <span class="subject-data">{{$dt->subject_or_research_title}}</span></div></li>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="row-title">Awards:</td>
                        <td class="inner-data" colspan="2">
                            @foreach($json_data['awards'] as $award)
                                @if($award->award_name !='')
                                    <li>{{ $award->award_name }} <span class="bracket-text"> ( {{ $award->awarded_by}}, {{ $award->awarded_year}} )</span></li>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="row-title">Expertise:</td>
                        <td class="inner-data" colspan="2">
                            @foreach($json_data['expertise'] as $expertise)
                                @if($expertise->name !='')
                                    <li>{{ $expertise->name }}</li>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="row-title">Professional Affiliation:</td>
                        <td class="inner-data" colspan="2">
                            @foreach($json_data['affiliation'] as $affiliation)
                                @if($affiliation->name !='')
                                    <li>{{ $affiliation->name }}</li>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="row-title">Correspondence:</td>
                        <td class="row-data contact" colspan="2">
                            <div>
                                <span class="subject-head">Mailing Address : </span><span class="subject-data">{{$member->mailing_address}}</span>
                            </div>
                            <div>                                
                                <span class="subject-head">Phone/Cell Number : </span><span class="subject-data">{{$member->phone}}</span>
                            </div>
                            <div>
                             <span class="subject-head"> E-mail : </span><span class="subject-data">{{$member->email}}</span>
                            </div>
                            <div>
                             <span class="subject-head"> Link to Google Scholar : </span><span class="subject-data">{{$member->link_to_google_scholar}}</span>
                            </div>
                        </td>
                    </tr>
            </table>
        </div>


    </div>
</body>

</html>