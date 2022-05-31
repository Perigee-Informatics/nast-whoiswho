<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\MstGender;
use App\Models\MstFedDistrict;
use App\Models\MstFedProvince;
use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class MembersImport implements ToCollection,WithHeadingRow
{
    /**
    * @param array $row
    *
    */

    public function collection(Collection $rows)
    {

        $province_mapping = [
            'Province 1'=>1,
            'Province 2'=>2,
            'Province 3'=>3,
            'Province 4'=>4,
            'Province 5'=>5,
            'Province 6'=>6,
            'Province 7'=>7,
        ];
        foreach($rows as $row){
            $data['gender_id']= MstGender::whereNameEn($row['gender'])->first()->id;

            $dob_ad = Carbon::parse(Date::excelToDateTimeObject($row['dob']))->toDateString();
            $data['dob_ad'] = $dob_ad;
            $data['dob_bs'] = convert_bs_from_ad($dob_ad);
            $data['nrn_number'] =$row['nri_number'];
            $data['first_name'] =$row['first_name'];
            $data['middle_name'] =$row['middle_name'];
            $data['last_name'] =$row['surname'];
            $data['is_other_country'] =$row['country']=='Nepal' ? false : true;
            $data['province_id']=$province_mapping[$row['province']];
            $data['district_id']=MstFedDistrict::where('name_en','iLIKE',"%".$row['district']."%")->first()->id;

            $data['current_organization']=json_encode([(object)([
                                            "position"=>$row['current_position'],
                                            "organization"=>$row['current_organization'],
                                            "address"=>$row['current_address'],
                                        ])]);

            $data['past_organization']=json_encode([(object)([
                                            "position"=>$row['past_position_one'],
                                            "organization"=>$row['past_organization_one'],
                                        ]),
                                        (object)([
                                            "position"=>$row['past_position_two'],
                                            "organization"=>$row['past_organization_two'],
                                        ]),
                                        (object)([
                                            "position"=>$row['past_position_three'],
                                            "organization"=>$row['past_organization_three'],
                                        ])]);

            $data['doctorate_degree']=json_encode([(object)([
                                            "degree_name"=>$row['doctorate_degree'],
                                            "others_degree"=>$row['others_degree'],
                                            "subject_or_research_title"=>$row['subjectresearch_title'],
                                            "university_or_institution"=>$row['name_of_universityinstitution'],
                                            "country"=>$row['doctorate_country'],
                                            "year"=>$row['year'],
                                        ])]);

            $data['masters_degree']=json_encode([(object)([
                                            "degree_name"=>$row['masters_degree'],
                                            "others_degree"=>$row['m_others_degree'],
                                            "subject_or_research_title"=>$row['masters_subjectresearch_title'],
                                            "university_or_institution"=>$row['masters_universityinstitution'],
                                            "country"=>$row['masters_country'],
                                            "year"=>$row['m_year'],
                                        ])]);

            $data['bachelors_degree']=json_encode([(object)([
                                            "degree_name"=>$row['bachelors_degree'],
                                            "others_degree"=>$row['b_others_degree'],
                                            "subject_or_research_title"=>$row['bachelors_subjectresearch_title'],
                                            "university_or_institution"=>$row['bachelors_universityinstitution'],
                                            "country"=>$row['bachelors_country'],
                                            "year"=>$row['b_year'],
                                        ])]);

            $data['awards']=json_encode([(object)([
                                "award_name"=>$row['name_of_award_one'],
                                "awarded_year"=>$row['awarded_year_one'],
                                "awarded_by"=>$row['awarded_by_one'],
                            ]),
                            (object)([
                                "award_name"=>$row['name_of_award_two'],
                                "awarded_year"=>$row['awarded_year_two'],
                                "awarded_by"=>$row['awarded_by_two'],
                            ]),
                            (object)([
                                "award_name"=>$row['name_of_award_three'],
                                "awarded_year"=>$row['awarded_year_three'],
                                "awarded_by"=>$row['awarded_by_three'],
                            ])]);

            $data['expertise']=json_encode([(object)([
                                "name"=>$row['expertise_one'],
                            ]),
                            (object)([
                                "name"=>$row['expertise_two'],
                            ]),
                            (object)([
                                "name"=>$row['expertise_three'],
                            ])]);

            $data['affiliation']=json_encode([(object)([
                                "name"=>$row['affiliation_one'],
                            ]),
                            (object)([
                                "name"=>$row['affiliation_two'],
                            ]),
                            (object)([
                                "name"=>$row['affiliation_three'],
                            ])]);

            $data['mailing_address']=$row['mailing_address'];
            $data['phone']=$row['phonecell'];
            $data['email']=$row['email_address'];
            $data['link_to_google_scholar']=$row['link_to_google_scholar'];

            Member::create($data);
        }
    }
}
