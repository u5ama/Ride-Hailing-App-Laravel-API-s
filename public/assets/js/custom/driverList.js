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
            url: APP_URL + '/driver_list',
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'logo', name: 'logo'},
            {data: 'on_boarding', name: 'on_boarding'},
            {data: 'd_vehicle_type', name: 'd_vehicle_type'},
            {data: 'd_vehicle', name: 'd_vehicle'},
            {data: 'd_license', name: 'd_license'},
            {data: 'd_name', name: 'd_name'},
            {data: 'd_mobile', name: 'd_mobile'},
            {data: 'd_email', name: 'd_email'},
            {data: 'd_success_rides', name: 'd_success_rides'},
            {data: 'd_cancel_rides', name: 'd_cancel_rides'},
            {data: 'd_wallet', name: 'd_wallet'},
            {data: 'd_rating', name: 'd_rating'},
            {data: 'd_last_ride', name: 'd_last_ride'},
            {data: 'd_last_online', name: 'd_last_online'},
            {data: 'd_last_location', name: 'd_last_location'},
            {data: 'd_company', name: 'd_company'},
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
    $(document).on('click', '.driver-details', function () {
        const rideid = $(this).data('rideid');
        loaderView();
        let effect = $(this).attr('data-effect');
        $('#globalModal').addClass(effect).modal('show');
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getDriverDetail'+ '/' + rideid,
            dataType: 'json',
            success: function (data) {

                $("#globalModalTitle").html(data.data.globalModalTitle);
                $("#globalModalDetails").html(data.data.globalModalDetails);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });
    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/driver_list' + '/' + value_id,
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

