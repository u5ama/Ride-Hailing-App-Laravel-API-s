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
            url: APP_URL + '/passenger_payments',
            data: function (d) {
                d.start_date = $('#start_date_payment').val();
                d.end_date = $('#end_date_payment').val();
            },
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'passenger_name', name: 'passenger_name'},
            {data: 'ppd_PaymentID', name: 'ppd_PaymentID'},
            {data: 'ppd_Result', name: 'ppd_Result'},
            {data: 'ppd_PostDate', name: 'ppd_PostDate'},
            {data: 'ppd_TranID', name: 'ppd_TranID'},
            {data: 'ppd_Ref', name: 'ppd_Ref'},
            {data: 'ppd_TrackID', name: 'ppd_TrackID'},
            {data: 'ppd_Auth', name: 'ppd_Auth'},
            {data: 'ppd_created_at', name: 'ppd_created_at'},
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

    $('.filterPayment').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('planid');

        swal({
            title: 'Destroy Payment?',
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
                url: APP_URL + '/passenger_payments',
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
            url: APP_URL + '/passenger_payments' + '/' + value_id,
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
        url: APP_URL + '/passenger_payments' + '/' + id+ '/' + status,
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



