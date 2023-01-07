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
            url: APP_URL + '/paymentSettings',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'pgs_payment_gateway', name: 'pgs_payment_gateway'},
            {data: 'pgs_currency_code', name: 'pgs_currency_code'},
            {data: 'pgs_api_key', name: 'pgs_api_key'},
            {data: 'pgs_username', name: 'pgs_username'},
            {data: 'pgs_merchant_id', name: 'pgs_merchant_id'},
            {data: 'status', name: 'status'},
            {data: 'pgs_base_url', name: 'pgs_base_url'},
            {data: 'pgs_whitelabled', name: 'pgs_whitelabled'},
            {data: 'pgs_success_url', name: 'pgs_success_url'},
            {data: 'pgs_error_url', name: 'pgs_error_url'},
            {data: 'pgs_gateway_type', name: 'pgs_gateway_type'},
            {data: 'action', name: 'action', orderable: false, searchable: false,width: "10%"},
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
        const value_id = $(this).data('planid');

        swal({
            title: 'Destroy Payment Settings?',
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

    let $form = $('#addEditForm');
    $form.on('submit', function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0]);
            $.ajax({
                url: APP_URL + '/paymentSettings',
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


    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/paymentSettings' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw();
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
});
function updateStatus(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/paymentSettings' + '/' + id+ '/' + status,
        async: false,
        success: function (data) {
            if (data.success === true) {
                successToast(data.message, 'success');
                loaderHide();
            }else{
                successToast(data.message, 'warning');
                loaderHide();
            }

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
    setTimeout(function () {
        $('#data-table').DataTable().ajax.reload();
    }, 1000);
}



