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
            url: APP_URL + '/inProgressScheduleRides',
            data: function (d) {
                d.start_date = $('#start_date_booking').val();
                d.end_date = $('#end_date_booking').val();
                d.categoryFilter = $('#filterWithCategory').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'date_time', name: 'date_time'},
            {data: 'transaction_id', name: 'transaction_id'},
            {data: 'ride_status', name: 'ride_status'},
            {data: 'payment_mode', name: 'payment_mode'},
            {data: 'category', name: 'category'},
            {data: 'schedule_ride_on', name: 'schedule_ride_on'},
            {data: 'schedule_for', name: 'schedule_for'},
            {data: 'tracking_url', name: 'tracking_url'},
            {data: 'pickup_location', name: 'pickup_location'},
            {data: 'dropoff_location', name: 'dropoff_location'},
            {data: 'distance_and_time', name: 'distance_and_time'},
            {data: 'passenger_detail', name: 'passenger_detail'},
            {data: 'driver_detail', name: 'driver_detail'},
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

    $('.filterBooking').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id');

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

});
