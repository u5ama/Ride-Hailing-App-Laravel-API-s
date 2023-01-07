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
            url: APP_URL + '/roles',
            type: 'GET',
        },

        columns: [
            {data: 'role_name', name: 'role_name'},
            {data: 'role_note', name: 'role_note'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
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
        const value_id = $(this).data('roleid');

        swal({
            title: 'Destroy Role?',
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
        const rideid = $(this).data('roleid');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/roles'+ '/' + rideid,
            dataType: 'html',
            success: function (data) {

                $("#viewRideModelId").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });
    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/roles' + '/' + value_id,
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

