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
            url: APP_URL + '/rideStatisticsPassenger',
            type: 'GET',
        },

        columns: [
            {data: 'passenger_name', name: 'passenger_name'},
            {data: 'total_requested', name: 'total_requested'},
            {data: 'total_waiting', name: 'total_waiting'},
            {data: 'total_accepted', name: 'total_accepted'},
            {data: 'total_rejected', name: 'total_rejected'},
            {data: 'total_completed', name: 'total_completed'},

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

        $(document).on('click', '.view-ride-detail', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const totalcount = $(this).data('totalcount');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getRideDetailViewModal'+ '/' + id + '/' + status + '/' + totalcount,
            dataType: 'html',
            success: function (data) {

                $("#viewRideModelDetails").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    })
});


