$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP_URL + '/transportModelYear',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'type', name: 'type'},
            {data: 'make', name: 'make'},
            {data: 'model', name: 'model'},
            {data: 'model_color', name: 'model_color'},
            {data: 'tmy_name', name: 'tmy_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
            processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
        },
        order: [[0, 'ASC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: title,
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText,
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                deleteRecord(value_id)
            }
        });
    });

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/transportModelYear',
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
                        successToast(data.message, 'success')
                        setTimeout(function () {
                            window.location.href = APP_URL + '/transportModelYear'
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

   // $("#type_id,#make_id,#model_id,#model_color_id").select2();
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



    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/transportModelYear' + '/' + value_id,
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

