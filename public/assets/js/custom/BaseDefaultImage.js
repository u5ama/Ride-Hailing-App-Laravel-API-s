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
            url: APP_URL + '/BaseDefaultImage',
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'bdi_screen_info', name: 'bdi_screen_info'},
            {data: 'bdi_description', name: 'bdi_description'},
            {data: 'bdi_key', name: 'bdi_key'},

            {data: 'bdi_image', name: 'bdi_image',
                "render": function (data, type, full, meta) {
                    var base_url = window.location.origin
                    var str_contains = data.includes(".pdf");
                    if(str_contains){
                        return "<a href="+ base_url + "/" + data + "> <iframe src=\"" + base_url + "/" + data + "\" height=\"50\"></iframe> </a>";
                    }else {
                        return "<img src=\"" + base_url + "/" + data + "\" height=\"50\"/>";
                    }
                }},
            {data: 'bdi_file_name', name: 'bdi_file_name'},
            {data: 'bdi_device_type', name: 'bdi_device_type'},
            {data: 'status', name: 'status'},
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
            title: 'Destroy Default Image?',
            text: 'Are you sure you want to permanently remove this record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                deleteRecord(value_id)
            }
        });
    })

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])

            $.ajax({
                url: APP_URL + '/BaseDefaultImage',
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
                        if ($('#form-method').val() === 'edit') {
                            setTimeout(function () {
                                window.location.href = APP_URL + '/BaseDefaultImage'
                            }, 1000);
                        }
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
    })

    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/languageString' + '/' + value_id,
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
function updatestatus(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/BaseDefaultImage' + '/' + id+ '/' + status,
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
