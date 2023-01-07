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
            url: APP_URL + '/promoCode',
            data: function (d) {
                d.start_date = $('#start_date_promo').val();
                d.end_date = $('#end_date_promo').val();
                d.promo_status = $('#promo_status').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'pco_promo_code', name: 'pco_promo_code'},
            {data: 'country', name: 'country'},
            {data: 'status', name: 'status'},
            {data: 'pco_start_date', name: 'pco_start_date'},
            {data: 'pco_end_date', name: 'pco_end_date'},
            {data: 'pco_end_date', name: 'pco_end_date'},
            {data: 'pco_start_time', name: 'pco_start_time'},
            {data: 'pco_end_time', name: 'pco_end_time'},
            {data: 'pco_promo_value', name: 'pco_promo_value'},
            {data: 'pco_promo_value_type', name: 'pco_promo_value_type'},
            {data: 'pco_admin_remarks', name: 'pco_admin_remarks'},
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

    $('.filterPromo').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    let $form = $('#addPromoForm');
    $form.on('submit', function (e) {
        console.log('hello');
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addPromoForm')[0]);
            console.log(formData);
            $.ajax({
                url: APP_URL + '/promoCode',
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
                            window.location.href = APP_URL + '/promoCode'
                        }, 1000);
                        if ($('#form-method').val() === 'edit') {
                            setTimeout(function () {
                                window.location.href = APP_URL + '/promoCode'
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
            title: 'Destroy Promo code?',
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
            url: APP_URL + '/promoCode' + '/' + value_id,
            success: function (data) {
                if(data.message != ''){
                    successToast('Promo Code Deleted Successfully', 'success');
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


