$(document).ready(function () {
    //hide other details from employee form

    $('.searchselect').select2();
    $('.form-group1').hide();
    $('#to-hide').on('click', function () {
        $('.form-group1').toggle();
    });

    TMPP.getDistrict();
    TMPP.getFedLocalLevel();

    //
    TMPP.calculateProjectCost();
});

let TMPP = {

    fetchDetailsById: (item) => {
        let fed_local_level_id = item.value;
        let url = '/admin/appclient/getDetailsById';

        if (fed_local_level_id != '') {
            $.ajax({
                type: 'GET',
                url: url,
                data: { fed_local_level_id: fed_local_level_id },
                success: function (response) {
                    if (response.message === 'success') {
                        $('#code').val(response.details.code);
                        $('#lmbis-code').val(response.details.lmbiscode);
                        if (window.location.href.indexOf("ptproject") > -1) {
                            // $('#description-en').val(response.details.name_en);
                            $('#description-lc').val(response.details.name_lc);
                        } else {
                            $('#name-en').val(response.details.name_en);
                            $('#name-lc').val(response.details.name_lc);
                        }


                    }
                },
                error: function (error) { }
            });
        }
    },

    calculateDuration: () => {
        var start_date = $('#estimated-start-date-ad').val();
        var end_date = $('#estimated-end-date-ad').val();
        var start_moment = moment(start_date);
        var end_moment = moment(end_date);
        var diffDuration = moment.duration(end_moment.diff(start_moment));
        var years = diffDuration.years();
        var months = diffDuration.months();
        var days = diffDuration.days();

        $('#estimated_duration_year').val(years);
        $('#estimated_duration_months').val(months);
        // $('#estimated_duration_days').val(days);
    },

    calculateProjectCost: () => {
        var project_cost = parseFloat($('#project_cost').val());
        var source_federal_amount = parseFloat($('#source_federal_amount').val());
        var source_local_level_amount = parseFloat($('#source_local_level_amount').val());
        var source_donar_amount = parseFloat($('#source_donar_amount').val());

        var project_cost = source_federal_amount + source_local_level_amount + source_donar_amount;
        $('#project_cost').val(project_cost);
    },

    calculateFinancialPercent: () => {
        var project_id = $('#project_id').val();
        console.log(project_id);
        var financial_progress_amount = parseFloat($('#financial_progress_amount').val());

        var url = '/admin/ptproject/getDetailsById';
        if(project_id != null){
            $.get(url, {project_id:project_id},function (data) {
                if(data != ''){
                    var progress_percent = (financial_progress_amount/parseFloat(data))*100;

                    if(progress_percent > 100){
                        swal('Warning','कृपया, आयोजनाको कुल लागत भन्दा बढी रकम प्रबिस्ट गर्नुभएको छ !!');
                    }else{
                        $('#financial_progress_percent').val(progress_percent);
                    }
                }
            });
        }else{
            swal('Warning','कृपया, पहिला आयोजना छान्नुहोस् !!');
            $('#financial_progress_amount').val('')
           
        }
    },


    getDistrict: () => {
        $.urlParam = function (name) {
            try {
    
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                return results[1] || 0;
            } catch {
                return null;
            }
        }
        var state_id = $('#filter_provinceId').val();
        $('#filter_districtId').append('<option value="">-- Loading...  --</option>');

        //first remove district and client params on change
        $('#filter_districtId').val('').trigger('change');
        $('#filter_client').val('').trigger('change');

        if (state_id) {
            $.ajax({
                url: '/district/' + state_id,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {

                    if (data) {
                        $('#filter_districtId').empty();
                        $('#filter_districtId').focus;
                        $('#filter_districtId').append('<option value="">-- जिल्ला छान्नुहोस्  --</option>');
                        var selected_id = $.urlParam("district_id");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }

                            $('select[name="filter_districtId"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.code + '-' + value.name_lc + '-' +  '</option>');
                            if (selected == "SELECTED") {
                                $("#filter_districtId").trigger("change");
                            }
                        });
                    } else {
                        $('#filter_districtId').empty();

                    }
                }
            });
        } else {
            $('#filter_districtId').empty();
        }
    },


    
    getFedLocalLevel: () => {
        $.urlParam = function (name) {
            try {
    
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                return results[1] || 0;
            } catch {
                return null;
            }
        }

        var district_id = $('#filter_districtId').val();

        //first remove client params on change
        $('#filter_client').val('').trigger('change');

        if (district_id) {
            $('#filter_client').append('<option value="">-- Loading...  --</option>');
            $.ajax({
                url: '/app_client_filter/' + district_id,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#filter_client').empty();
                        $('#filter_client').focus;
                        $('#filter_client').append('<option value="">-- स्थानीय तह  छान्नुहोस् --</option>');
                        var selected_id = $.urlParam("client");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }
                            $('select[name="filter_client"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.lmbiscode + '-' + value.name_lc +  '</option>');
                            if (selected == "SELECTED") {
                                $("#filter_client").trigger("change");
                            }
                        });
                    } else {
                        $('#filter_client').empty();
                    }
                }
            });
        } else {
            $('#filter_client').empty();
        }
    }

    

}
window.TMPP = TMPP;
