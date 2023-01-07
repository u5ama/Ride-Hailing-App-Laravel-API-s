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
            url: APP_URL + '/dailyEarning',
            data: function (d) {
                d.start_date = $('#start_date_earning').val();
                d.end_date = $('#end_date_earning').val();
                d.company = $('#company_filter').val();
                d.filterWithCategory = $('#companyfilterWithCategory').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'invoice_date', name: 'invoice_date'},
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
            // {data: 'action', name: 'action', orderable: false, searchable: false,width: "10%"},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalcustomerinvoice = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            customerInvoicepageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                ''+  customerInvoicepageTotal.toFixed(2)
            );


            // Total over all pages
            totalbankcomm = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            bankcommpageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 5 ).footer() ).html(
                ''+  bankcommpageTotal.toFixed(2)
            );


            // Total over all pages
            totalnetinvoice = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            netInvoicepageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 6 ).footer() ).html(
                ''+  netInvoicepageTotal.toFixed(2)
            );


            // Total over all pages
            totaldriveramount = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            driverAmountpageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 7 ).footer() ).html(
                ''+  driverAmountpageTotal.toFixed(2)
            );

            // Total over all pages
            totaldriveramount = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            driverAmountpageTotal = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 8 ).footer() ).html(
               ''+  driverAmountpageTotal.toFixed(2)
            );


            // Total over all pages
            totaldriveramount = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            driverAmountpageTotal = api
                .column( 9, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 9 ).footer() ).html(
               ''+  driverAmountpageTotal.toFixed(2)
            );


            // Total over all pages
            totaldriveramount = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            driverAmountpageTotal = api
                .column( 10, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 10 ).footer() ).html(
                ''+  driverAmountpageTotal.toFixed(2)
            );


        },
        drawCallback: function () {
            funTooltip()
        },

        language: {
           processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
         //  processing: ''
        },
        order: [[0, 'DESC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    });

    $('.filterDailyEarning').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    $('.company_filter').change(function(){
        $('#data-table').DataTable().draw(true);
    });

    $('#companyfilterWithCategory').change(function(){
        $('#data-table').DataTable().draw(true);
    });


    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id');

        swal({
            title: 'Destroy Default Record?',
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
    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/dailyEarning' + '/' + value_id,
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

