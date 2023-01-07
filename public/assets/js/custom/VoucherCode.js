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
            url: APP_URL + '/voucher',
            data: function (d) {
                d.start_date = $('#start_date_voucher').val();
                d.end_date = $('#end_date_voucher').val();
                d.voucher_status = $('#voucher_status').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'vc_voucher_code', name: 'vc_voucher_code'},
            {data: 'vc_amount', name: 'vc_amount'},
            {data: 'vc_issue_date', name: 'vc_issue_date'},
            {data: 'vc_expiry_date', name: 'vc_expiry_date'},
            {data: 'vc_issue_time', name: 'vc_issue_time'},
            {data: 'vc_expiry_time', name: 'vc_expiry_time'},
            {data: 'user', name: 'user'},
            {data: 'vc_voucher_used_status', name: 'vc_voucher_used_status'},
            {data: 'vc_redeemed_at', name: 'vc_redeemed_at'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
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

    $('.filterVoucher').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    let $form = $('#addVoucherForm');
    $form.on('submit', function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addVoucherForm')[0]);
            console.log(formData);
            $.ajax({
                url: APP_URL + '/voucher',
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
                        successToast(data.message, 'success');
                        setTimeout(function () {
                            window.location.href = APP_URL + '/voucher'
                        }, 1000);
                        if ($('#form-method').val() === 'edit') {
                            setTimeout(function () {
                                window.location.href = APP_URL + '/voucher'
                            }, 1000);
                        }
                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    console.log('Error:', data);
                    successToast(data.message, 'error');
                }
            })
        }
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('cardid');

        swal({
            title: 'Destroy Voucher Code?',
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
              //  table.draw();
            }
        });
    });

    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/voucher' + '/' + value_id,
            success: function (data) {
                if(data.message != ''){
                    successToast('Voucher Code Deleted Successfully', 'success');
                    table.draw()
                }else{
                    successToast(data.message, 'error');
                }

            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
});


