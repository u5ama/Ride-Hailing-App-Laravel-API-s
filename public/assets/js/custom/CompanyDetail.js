$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#data-table').DataTable(
        {
            order: [[0, 'DESC']],
        }
    );

    funTooltip();


    let $fancybox = $(".fancybox");

    if ($fancybox.length > 0) {
        $fancybox.fancybox();
    }

    let $form = $('#addEditFormOTP');
    $form.on('submit', function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditFormOTP')[0]);
            $.ajax({
                url: APP_URL + '/updateManualOTP',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $form[0].reset();
                        $form.parsley().reset();
                        successToast(data.message, 'success')
                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    successToast('Manual OTP is not valid / Unique', 'error')
                    console.log('Error:', data)
                }
            })
        }
    });


    $(document).on('click', '.addOTP', function () {
        console.log('Hello');
        console.log($(this).data('id'));
        const driverId = $(this).data('id');
        $('#driverId').val(driverId);
    });

});

let $form = $('#addEditFormOTP');
$form.on('submit', function (e) {
    e.preventDefault();
    $form.parsley().validate();
    if ($form.parsley().isValid()) {
        loaderView();
        let formData = new FormData($('#addEditFormOTP')[0]);
        $.ajax({
            url: APP_URL + '/updateManualOTP',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                loaderHide();
                if (data.success === true) {
                    $form[0].reset();
                    $form.parsley().reset();
                    successToast(data.message, 'success')
                } else if (data.success === false) {
                    successToast(data.message, 'warning')
                }
            },
            error: function (data) {
                loaderHide();
                successToast('Manual OTP is not valid / Unique', 'error')
                console.log('Error:', data)
            }
        })
    }
});

$(document).on('click', '.driver-details', function () {
    const rideid = $(this).data('rideid');
    loaderView();
    let effect = $(this).attr('data-effect');
    $('#globalModal').addClass(effect).modal('show');
    $.ajax({
        type: 'GET',
        url: APP_URL + '/getDriverDetail'+ '/' + rideid,
        dataType: 'json',
        success: function (data) {

            $("#globalModalTitle").html(data.data.globalModalTitle);
            $("#globalModalDetails").html(data.data.globalModalDetails);

            loaderHide();
        }, error: function (data) {
            console.log('Error:', data)
        }
    })
});
$(document).on('click', '.addOTP', function () {
    const driverId = $(this).data('id');
    $('#driverId').val(driverId);
});

function updateDriverStatus(id,company_id) {
  var status = $("#driver_status_"+id).val();
  loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/updateDriverStatus' + '/' + id+ '/' + status + '/' + company_id,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');

            setTimeout(function () {
              window.location.href = APP_URL + '/company'+ '/' + company_id
            }, 1000);


        }, error: function (data) {
            console.log('Error:', data)
        }
    });
}

function updateCompanyStatus(elem) {

    if($(elem).val() == ""){
        return;
    }
    var company_id = $(elem).val();
    var driver_id = $('#driver_id').val();

    console.log(company_id);
    console.log(driver_id);
    loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/updateDriverCompany' + '/' + driver_id+ '/' + company_id,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');

            setTimeout(function () {
                location.reload();
            }, 1000);


        }, error: function (data) {
            console.log('Error:', data)
        }
    });
}


function changeDriverRegStatus(id,status,company_id) {
    loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/changeDriverRegStatus' + '/' + id+ '/' + status,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');
            setTimeout(function () {
              window.location.href = APP_URL + '/company'+ '/' + company_id
            }, 1000);
            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });

}




