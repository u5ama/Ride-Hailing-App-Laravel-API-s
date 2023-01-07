$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])

            $.ajax({
                url: APP_URL + '/addEditDriverRegistration',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $form[0].reset()
                        $form.parsley().reset();
                        successToast(data.message, 'success');

                            setTimeout(function () {
                                window.location.href = APP_URL + '/driver'
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


     // $("#make_id,#model_id,#model_color_id,#model_year_id").select2();
    $('.dropify').dropify();

    floatOnly();
    integerOnly();


    $(document).on('change', '#type_id', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getMake',
            data: {type_id: $("#type_id").val()},
            dataType: 'html',
            success: function (data) {
                $("#make_id").html(data);
                $("#model_id").html('');
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });


    $(document).on('change', '#make_id', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getModel',
            data: {type_id: $("#type_id").val(),make_id: $("#make_id").val()},
            dataType: 'html',
            success: function (data) {
                $("#model_id").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    $(document).on('change', '#model_id', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getModelColor',
            data: {type_id: $("#type_id").val(),make_id: $("#make_id").val(),model_id: $("#model_id").val()},
            dataType: 'html',
            success: function (data) {
                $("#model_color_id").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    $(document).on('change', '#model_color_id', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getModelYear',
            data: {type_id: $("#type_id").val(),make_id: $("#make_id").val(),model_id: $("#model_id").val(),model_color_id: $("#model_color_id").val()},
            dataType: 'html',
            success: function (data) {
                $("#model_year_id").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/driver' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
});


function updatestatus(id) {
  var status = $("#driver_status_"+id).val();
  loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/driver' + '/' + id+ '/' + status,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');
            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
    setTimeout(function () {
        $('#data-table').DataTable().ajax.reload();
    }, 1000);
}

function addMoreFiles(){

         var new_image = $("#countimage").val();

         $("#newimage_"+new_image).show();
         j = 1;
          inc =   Number(new_image) + Number(j)

         $("#countimage").val(inc);
    }

