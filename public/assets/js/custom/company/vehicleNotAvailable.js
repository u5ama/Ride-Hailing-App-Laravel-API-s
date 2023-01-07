$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#data-table').DataTable();

    let $vForm = $('#vehicleNotAvailableForm')
    $vForm.on('submit', function (e) {
        e.preventDefault()
        $vForm.parsley().validate();
        console.log($vForm.parsley().isValid());
        if ($vForm.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#vehicleNotAvailableForm')[0])
            $.ajax({
                url: APP_URL + '/updateVehicleNotAvailable',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $vForm[0].reset()
                        $vForm.parsley().reset();
                        successToast(data.message, 'success')
                         setTimeout(function () {
                            window.location.href = APP_URL + '/vehicleNotAvailable/'+data.id
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


    funTooltip();

    let $fancybox = $(".fancybox");

    if ($fancybox.length > 0) {
        $fancybox.fancybox();
    }
});




