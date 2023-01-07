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
            url: APP_URL + '/passenger',
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'logo', name: 'logo'},
            {data: 'on_boarding', name: 'on_boarding'},
            {data: 'p_name', name: 'p_name'},
            {data: 'p_mobile', name: 'p_mobile'},
            {data: 'p_email', name: 'p_email'},
            {data: 'p_success_rides', name: 'p_success_rides'},
            {data: 'p_cancel_rides', name: 'p_cancel_rides'},
            {data: 'p_wallet', name: 'p_wallet'},
            {data: 'p_rating', name: 'p_rating'},
            {data: 'p_last_ride', name: 'p_last_ride'},
            {data: 'p_last_online', name: 'p_last_online'},
            // {data: 'action', name: 'action', orderable: false, searchable: false,width: "10%"},
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
    $(document).on('click', '.passenger-details', function () {
        const rideid = $(this).data('rideid');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/showPassenger'+ '/' + rideid,
            dataType: 'html',
            success: function (data) {

                $("#viewPassengerModelId").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });
    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/company' + '/' + value_id,
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

