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
            url: APP_URL + '/earningAnalysis',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.categoryFilter = $('#filterWithCategory').val();
                d.filterWithStatus = $('#filterWithStatus').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'invoice_date', name: 'invoice_date'},
            {data: 'inv_id', name: 'inv_id'},
            {data: 'trx_id', name: 'trx_id'},
            {data: 'ride_status', name: 'ride_status'},
            {data: 'category', name: 'category'},
            {data: 'payment_mode', name: 'payment_mode'},
            {data: 'customer_invoice_amount', name: 'customer_invoice_amount'},
            {data: 'bank_commission', name: 'bank_commission'},
            {data: 'net_invoice', name: 'net_invoice'},
            {data: 'driver', name: 'driver'},
            {data: 'whipp', name: 'whipp'},
            {data: 'company_gross_earning', name: 'company_gross_earning'},
            {data: 'company_net_earning', name: 'company_net_earning'},
            {data: 'passenger_detail', name: 'passenger_detail'},
            {data: 'driver_details', name: 'driver_details'},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
           processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
          // processing: ''
        },
        order: [[0, 'DESC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    });
    $('.filterDate').click(function(){
        var _token=$('input[name=_token]').val();
        const start_date = $('#start_date').val();
        const end_date = $('#end_date').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDateFilter',
            data:{_token:_token,start_date:start_date, end_date:end_date},
            success: function(data){
                $('#bankCom').html(data.bankCom);
                $('#netInvoice').html(data.netInvoice);
                $('#whippInc').html(data.whipp);
                $('#driverInc').html(data.driver);
                $('#data-table').DataTable().draw(true);
            }
        });
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('invid');

        swal({
            title: 'Destroy Invoice?',
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


    $(document).on('change', '#filterWithCategory', function () {
        var _token=$('input[name=_token]').val();
        const category = $('#filterWithCategory').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDateFilter',
            data:{_token:_token,category:category},
            success: function(data){
                console.log(data);
                $('#bankCom').html(data.bankCom);
                $('#netInvoice').html(data.netInvoice);
                $('#whippInc').html(data.whipp);
                $('#driverInc').html(data.driver);
                $('#data-table').DataTable().draw(true);
            }
        });
    });

    $(document).on('change', '#filterWithStatus', function () {
        var _token=$('input[name=_token]').val();
        const status = $('#filterWithStatus').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDateFilter',
            data:{_token:_token,status:status},
            success: function(data){
                console.log(data);
                $('#bankCom').html(data.bankCom);
                $('#netInvoice').html(data.netInvoice);
                $('#whippInc').html(data.whipp);
                $('#driverInc').html(data.driver);
                $('#data-table').DataTable().draw(true);
            }
        });
    });


    $(document).on('click', '.invoice-details', function () {
        const invoiceId = $(this).data('invoiceid');

        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/invoicesDetails'+ '/' + invoiceId,
            dataType: 'html',
            success: function (data) {
                $("#globalModalInvoiceDetails").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/earningAnalysis' + '/' + value_id,
            success: function (data) {
                if(data.message != ''){
                    successToast('Invoice Deleted Successfully', 'success');
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

