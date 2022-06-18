    <table id="members_data_table" class="table table-bordered table-sm table-striped mr-2 pr-2 mt-3" style="background-color:#f8f9fa; overflow:scroll;">
        <thead>
            <tr>
                <th class="report-heading">Action</th>
                <th class="report-heading">S.N.</th>
                <th class="report-heading th_large">Full Name</th>
                <th class="report-heading th_large">Gender</th>
                <th class="report-heading th_large">NRN Number</th>
                <th class="report-heading th_small">Is other country?</th>
                <th class="report-heading th_large">Country</th>
                <th class="report-heading th_large">Province</th>
                <th class="report-heading th_large">District</th>
                <th class="report-heading th_large">Mailing Address</th>
                <th class="report-heading th_large">E-mail</th>
                <th class="report-heading th_large">Link to Google Schloar</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $key=>$member)
                @php
                    $rowId = 'member-'.$key;
                    $basic = $member['basic'];
                    $json = $member['json_data'];
                    $member_full_name = $basic->first_name.' '.$basic->middle_name.' '.$basic->last_name;
                @endphp

                <tr data-toggle="collapse" data-target="{{ '#'.$rowId}}" class="accordion-toggle">
                    <td class="text-center"><button class="btn btn-secondary btn-sm px-1 mb-1"><i class="la la-eye font-weight-bold p-0"></i></button></td>
                    <td class="report-data text-center">{{$loop->iteration}}</td>
                    <td class="report-data">{{$member_full_name}}</td>
                    <td class="report-data">{{$basic->genderEntity->name_en}}</td>
                    <td class="report-data">{{$basic->nrn_number}}</td>
                    <td class="report-data">{{$basic->is_other_country==true ? 'Yes' : 'No'}}</td>
                    <td class="report-data">{{$basic->countryEntity ? $basic->countryEntity->name_en : 'Nepal'}}</td>
                    <td class="report-data">{{$basic->provinceEntity->name_en}}</td>
                    <td class="report-data">{{$basic->districtEntity->name_en}}</td>
                    <td class="report-data">{{$basic->mailing_address}}</td>
                    <td class="report-data">{{$basic->email}}</td>
                    <td class="report-data">{{$basic->link_to_google_scholar}}</td>
                </tr>
                <tr>
                    <td colspan="12" class="hiddenRow">
                        <div class="accordian-body collapse ml-5 pr-2" id="{{$rowId}}"> 
                        <table class="table table-striped table-bordered table-hover my-4 mr-2" style="background-color:#eefdf9; display:inline-table !important;">
                                {{-- current organization --}}
                                <thead>
                                    <tr><th colspan="11" class="text-center font-weight-bold text-white bg-success">{{ $member_full_name}}</th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Current Organization</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th colspan="2" class="report-heading-second th_large">Position</th>
                                        <th colspan="2" class="report-heading-second th_large">Organization</th>
                                        <th colspan="2" class="report-heading-second th_large">Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    @foreach($json['current_organization'] as $current)
                                    <tr>
                                        <td class="report-data text-center">{{  $j++ }}</td>
                                        <td colspan="2" class="report-data-second">{{$current->position}}</td>
                                        <td colspan="2" class="report-data-second">{{$current->organization}}</td>
                                        <td colspan="2" class="report-data-second">{{$current->address}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                {{-- past organization --}}
                                <thead>
                                    <tr><th colspan="11" style="background-color: darkgray !important;"></th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Past Organization</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th colspan="3" class="report-heading-second th_large">Position</th>
                                        <th colspan="3" class="report-heading-second th_large">Organization</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    @foreach($json['past_organization'] as $past)
                                    <tr>
                                        <td class="report-data text-center">{{  $j++ }}</td>
                                        <td colspan="3" class="report-data-second">{{$past->position}}</td>
                                        <td colspan="3" class="report-data-second">{{$past->organization}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                {{-- Educational Qualifications --}}
                                <thead>
                                    <tr><th colspan="11" style="background-color: darkgray !important;"></th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Educational Qualifications</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th class="report-heading-second th_large">Degree Name</th>
                                        <th class="report-heading-second th_large">Others (If any)</th>
                                        <th class="report-heading-second th_large">Subject/Research Title</th>
                                        <th class="report-heading-second th_large">Name of University/Institution</th>
                                        <th class="report-heading-second th_large">Address</th>
                                        <th class="report-heading-second th_large">Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    {{-- doctorate --}}
                                    @foreach($json['doctorate_degree'] as $doctorate)
                                    <tr>
                                        <td class="report-data text-center">{{  $j++ }}</td>
                                        <td class="report-data-second">{{$doctorate->degree_name}}</td>
                                        <td class="report-data-second">{{$doctorate->degree_name}}</td>
                                        <td class="report-data-second">{{$doctorate->subject_or_research_title}}</td>
                                        <td class="report-data-second">{{$doctorate->university_or_institution}}</td>
                                        <td class="report-data-second">{{$doctorate->country}}</td>
                                        <td class="report-data-second">{{$doctorate->year}}</td>
                                    </tr>
                                    @endforeach

                                    {{-- masters --}}
                                    @foreach($json['masters_degree'] as $masters)
                                    <tr>
                                        <td class="report-data text-center">{{  $j++ }}</td>
                                        <td class="report-data-second">{{$masters->degree_name}}</td>
                                        <td class="report-data-second">{{$masters->degree_name}}</td>
                                        <td class="report-data-second">{{$masters->subject_or_research_title}}</td>
                                        <td class="report-data-second">{{$masters->university_or_institution}}</td>
                                        <td class="report-data-second">{{$masters->country}}</td>
                                        <td class="report-data-second">{{$masters->year}}</td>
                                    </tr>
                                    @endforeach

                                    {{-- bachelors --}}
                                    @foreach($json['bachelors_degree'] as $bachelors)
                                    <tr>
                                        <td class="report-data text-center">{{  $j++ }}</td>
                                        <td class="report-data-second">{{$bachelors->degree_name}}</td>
                                        <td class="report-data-second">{{$bachelors->degree_name}}</td>
                                        <td class="report-data-second">{{$bachelors->subject_or_research_title}}</td>
                                        <td class="report-data-second">{{$bachelors->university_or_institution}}</td>
                                        <td class="report-data-second">{{$bachelors->country}}</td>
                                        <td class="report-data-second">{{$bachelors->year}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                {{-- awards --}}
                                <thead>
                                    <tr><th colspan="11" style="background-color: darkgray !important;"></th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Awards</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th colspan="2" class="report-heading-second th_large">Award Name</th>
                                        <th colspan="2" class="report-heading-second th_large">Awarded By</th>
                                        <th colspan="2" class="report-heading-second th_large">Awarded Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    @foreach($json['awards'] as $award)
                                        @if($award->award_name != '')
                                            <tr>
                                                <td class="report-data text-center">{{  $j++ }}</td>
                                                <td colspan="2" class="report-data-second">{{$award->award_name}}</td>
                                                <td colspan="2" class="report-data-second">{{$award->awarded_by}}</td>
                                                <td colspan="2" class="report-data-second">{{$award->awarded_year}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>

                                {{-- expertise --}}
                                <thead>
                                    <tr><th colspan="11" style="background-color: darkgray !important;"></th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Expertise</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th colspan="6" class="report-heading-second th_large">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    @foreach($json['expertise'] as $expertise)
                                        @if($expertise->name != '')
                                            <tr>
                                                <td class="report-data text-center">{{  $j++ }}</td>
                                                <td colspan="6" class="report-data-second">{{$expertise->name}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                {{-- affiliation --}}
                                <thead>
                                    <tr><th colspan="11" style="background-color: darkgray !important;"></th></tr>
                                    <tr><th colspan="11" class="text-left font-weight-bold text-dark bg-bisque">Affiliation</th></tr>
                                    <tr>
                                        <th class="report-heading-second">S.N.</th>
                                        <th colspan="6" class="report-heading-second th_large">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $j = 1;
                                        @endphp
                                    @foreach($json['affiliation'] as $affiliation)
                                        @if($affiliation->name != '')
                                            <tr>
                                                <td class="report-data text-center">{{  $j++ }}</td>
                                                <td colspan="6" class="report-data-second">{{$affiliation->name}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>

                            </table>
                        </div> 
                    </td>
                </tr>
            @endforeach    
        </tbody>
    </table>

<style>
    .report-heading {
        font-size: 14px;
    }

    .bg-bisque{
        background-color: bisque !important;
        text-decoration: underline;
        text-decoration-style: double;
        text-decoration-color: blue;
        text-align: center !important;
    }
    .report-heading-second{
        font-size:13px;
        padding:0px;
        color:black !important;
        background-color:lightgray !important;
    }

    .report-data {
        font-size: 14px;
        font-weight: 600;
        color: black;
        max-width:100px !important;

        /* font-family: 'Kalimati'; */
    }
    .report-data-second{
        text-align: left;
        padding-right:20px !important;
    }

    tr>th {
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
        background-color: #3B72A0 !important;
        color: white;
    }
    tr>td {
        border-bottom: 1px solid grey !important;
        border-right: 1px solid grey !important;
    }
    tr>td:hover{
        cursor: pointer;
    }

    .th_large {
        /* min-width: 100px !important; */
        /* max-width:100px !important; */
    }
    .th_small{
        max-width: 75px;
    }
    .num-data{
        text-align: right;
    }
    .th_footer{
        text-align: right;
        font-size:16px;
    }
    .table th{
        padding:.5rem .75rem !important;
    }
</style>