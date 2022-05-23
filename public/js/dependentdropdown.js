$(document).ready(function () {
    $.urlParam = function (name) {
        try {

            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results[1] || 0;
        } catch {
            return null;
        }
    }
    $('#district').append('<option value="">-- जिल्ला छान्नुहोस्  --</option>');
    $('#local_level').append('<option value="">-- स्थानीय तह  छान्नुहोस् --</option>');
    $('#sub_category_id').append('<option value="">--आयोजना उप-प्रकार छान्नुहोस्--</option>');
    $('#project_id').append('<option value="">--Select Project--</option>');
    $('#month_id').append('<option value="">--Select Time Of Report--</option>');


    $('#province').on('change', function () {
        var stateID = $(this).val();
        $('#district').append('<option value="">-- Loading...  --</option>');
        if (stateID) {
            $.ajax({
                url: '/district/' + stateID,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {

                    if (data) {
                        $('#district').empty();
                        $('#district').focus;
                        $('#district').append('<option value="">-- जिल्ला छान्नुहोस्  --</option>');
                        var selected_id = $.urlParam("district");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }

                            $('select[name="district"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.name_lc + '</option>');
                            if (selected == "SELECTED") {
                                $("#district").trigger("change");
                            }
                        });
                    } else {
                        $('#district').empty();

                    }
                }
            });
        } else {
            $('#district').empty();
        }
    });

    $('#district').on('change', function () {
        var districtID = $(this).val();
        if (districtID) {
            $('#local_level').append('<option value="">-- Loading...  --</option>');
            $.ajax({
                url: '/local_level/' + districtID,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#local_level').empty();
                        $('#local_level').focus;
                        $('#local_level').append('<option value="">-- स्थानीय तह  छान्नुहोस् --</option>');
                        var selected_id = $.urlParam("local_level");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }
                            $('select[name="local_level"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.name_lc + '</option>');
                            if (selected == "SELECTED") {
                                $("#local_level").trigger("change");
                            }
                        });
                    } else {
                        $('#local_level').empty();
                    }
                }
            });
        } else {
            $('#local_level').empty();
        }
    });


    $('#local_level').on('change', function () {
        var projectID = $(this).val();
        if (projectID) {
            $('#project_id').append('<option value="">-- Loading...  --</option>');
            $.ajax({
                url: '/projectid/' + projectID,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#project_id').empty();
                        $('#project_id').focus;
                        $('#project_id').append('<option value="">-- परियोजना छान्नुहोस् --</option>');
                        var selected_id = $.urlParam("project_id");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }
                            $('select[name="project_id"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.code + '-' + value.name_lc + '-' + value.name_en + '</option>');
                            if (selected == "SELECTED") {
                                $("#project_id").trigger("change");
                            }
                        });
                    } else {
                        $('#project_id').empty();
                    }
                }
            });
        } else {
            $('#project_id').empty();
        }
    });





    $('#category_id').on('change', function () {
        var stateID = $(this).val();
        if (stateID) {
            $('#sub_category_id').append('<option value="">-- Loading...  --</option>');
            $.ajax({
                url: '/sub_category/' + stateID,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#sub_category_id').empty();
                        $('#sub_category_id').focus;
                        $('#sub_category_id').append('<option value="">--आयोजना उप-प्रकार छान्नुहोस्--</option>');
                        var selected_id = $.urlParam("sub_category_id");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }
                            $('select[name="sub_category_id"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.code + '-' + value.name_lc + '-' + value.name_en + '</option>');
                            if (selected == "SELECTED") {
                                $("#sub_category_id").trigger("change");
                            }
                        });
                    } else {
                        $('#sub_category_id').empty();
                    }
                }
            });
        } else {
            $('#sub_category_id').empty();
        }
    });

    //reporting_interval
    $('#reporting_interval_id').on('change', function () {
        var stateID = $(this).val();
        if (stateID) {
            $('#month_id').append('<option value="">-- Loading...  --</option>');
            $.ajax({
                url: '/month_id/' + stateID,
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#month_id').empty();
                        $('#month_id').focus;
                        $('#month_id').append('<option value="">-- Select Time Of Report --</option>');
                        var selected_id = $.urlParam("month_id");
                        $.each(data, function (key, value) {
                            var selected = "";
                            if (selected_id == value.id) {
                                selected = "SELECTED";
                            }
                            $('select[name="month_id"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.code + '-' + value.name_en + '</option>');
                            if (selected == "SELECTED") {
                                $("#month_id").trigger("change");
                            }
                        });
                    } else {
                        $('#month_id').empty();
                    }
                }
            });
        } else {
            $('#month_id').empty();
        }
    });
});


