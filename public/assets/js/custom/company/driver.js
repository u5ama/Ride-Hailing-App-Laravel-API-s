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
            url: APP_URL + '/driver',
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
             {data: 'image', name: 'image'},
             {data: 'type', name: 'type'},
            {data: 'du_full_name', name: 'du_full_name'},
            {data: 'email', name: 'email'},
            {data: 'du_full_mobile_number', name: 'du_full_mobile_number'},
            {data: 'du_created_at', name: 'du_created_at'},
            {data: 'dr_reg_status', name: 'dr_reg_status'},
            {data: 'status', name: 'status'},
            {data: 'change_status', name: 'change_status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
            processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
        },
        order: [[0, 'DESC']],
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
            }else{
                table.draw();
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
                url: APP_URL + '/driver',
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
                                window.location.href = APP_URL + '/editDriverRegistration/' + data.driver_id
                            }, 1000);
                        }else{
                           setTimeout(function () {
                                window.location.href = APP_URL + '/driverRegistration/' + data.driver_id
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
    });

    $(document).on('click', '.driver-detail', function () {
        const value_id = $(this).data('id');

        loaderView();
        let effect = $(this).attr('data-effect');
        $('#globalModal').addClass(effect).modal('show');

        $.ajax({
            type: 'GET',
            url: APP_URL + '/getDriverDetail'+ '/' + value_id,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#globalModalTitle").html(data.data.globalModalTitle);
                $("#globalModalDetails").html(data.data.globalModalDetails);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

     $('.dropify').dropify();


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


function changeDriverRegStatus(id,status) {
    loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/changeDriverRegStatus' + '/' + id+ '/' + status,
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

