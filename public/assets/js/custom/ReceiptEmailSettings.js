$(function () {
    let $settingsFormOne = $('#settingsFormOne');
    $settingsFormOne.on('submit', function (e) {
        e.preventDefault();
        $settingsFormOne.parsley().validate();
        if ($settingsFormOne.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#settingsFormOne')[0]);
            $.ajax({
                url: APP_URL + '/receipt_email_settings_form_one',
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
                url: APP_URL + '/receipt_email_settings_form_two',
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
                url: APP_URL + '/receipt_email_settings_form_three',
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




