$(function () {
    let $settingsFormOne = $('#settingsFormOne');
    $settingsFormOne.on('submit', function (e) {
        e.preventDefault();
        $settingsFormOne.parsley().validate();
        if ($settingsFormOne.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#settingsFormOne')[0]);
            $.ajax({
                url: APP_URL + '/welcome_email_settings_form_one',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $settingsFormOne[0].reset();
                        $settingsFormOne.parsley().reset();
                        successToast(data.message, 'success');
                        if ($('#form-method').val() === 'edit') {
                        $("#edit_value").val('');
                        $("#form-method").val('add');
                        $("#btn_txt").text('Add');
                    }
                        setTimeout(function () {
                            $('#data-table').DataTable().ajax.reload();
                        }, 1000);

                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    console.log('Error:', data)
                }
            })
        }
    });

    let $settingsFormTwo = $('#settingsFormTwo');
    $settingsFormTwo.on('submit', function (e) {
        e.preventDefault();
        $settingsFormTwo.parsley().validate();
        if ($settingsFormTwo.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#settingsFormTwo')[0]);
            $.ajax({
                url: APP_URL + '/welcome_email_settings_form_two',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $settingsFormTwo[0].reset();
                        $settingsFormTwo.parsley().reset();
                        successToast(data.message, 'success');
                        if ($('#form-method').val() === 'edit') {
                        $("#edit_value").val('');
                        $("#form-method").val('add');
                        $("#btn_txt").text('Add');
                    }
                        setTimeout(function () {
                            $('#data-table').DataTable().ajax.reload();
                        }, 1000);

                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    console.log('Error:', data)
                }
            })
        }
    });

    let $settingsFormThree = $('#settingsFormThree');
    $settingsFormThree.on('submit', function (e) {
        e.preventDefault();
        $settingsFormThree.parsley().validate();
        if ($settingsFormThree.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#settingsFormThree')[0]);
            $.ajax({
                url: APP_URL + '/welcome_email_settings_form_three',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $settingsFormThree[0].reset();
                        $settingsFormThree.parsley().reset();
                        successToast(data.message, 'success');
                        if ($('#form-method').val() === 'edit') {
                        $("#edit_value").val('');
                        $("#form-method").val('add');
                        $("#btn_txt").text('Add');
                    }
                        setTimeout(function () {
                            $('#data-table').DataTable().ajax.reload();
                        }, 1000);

                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    console.log('Error:', data)
                }
            })
        }
    });

});

function editFarePlanHead(id){
 loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/getFarePlanHeadByid' + '/' + id,
        async: false,
        success: function (data) {
           $("#edit_value").val(id);
           $("#form-method").val('edit');
           $("#btn_txt").text('Edit');
           $('#fph_plan_name').val(data.fph_plan_name);
           $('#fph_description').val(data.fph_description);
           $('#fph_vat_per').val(data.fph_vat_per);
           $('#fph_tax_per').val(data.fph_tax_per);
           $('#start_date').val(data.start_date);
           $('#end_date').val(data.end_date);
           var fph_country_id = data.fph_country_id;
           var countryOption = document.getElementById("fph_country_id");
            optionLength = $("#fph_country_id option").length;

            for (var x = 0; x < optionLength; x++) {
              if (countryOption.options[x].value == fph_country_id) {
                countryOption.options[x].selected = true;
              }
            }

           if(data.fph_fare_type == 'intercity'){
            document.getElementById("fph_fare_type").selectedIndex = "0";
        }else{
            document.getElementById("fph_fare_type").selectedIndex = "1";
        }
            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
}



