$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#data-table').DataTable({

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
        const value_id = $(this).data('cardid');

        swal({
            title: 'Destroy Customer Credit Card?',
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
    })

    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/EmailSettings' + '/' + value_id,
            success: function (data) {
                if(data.message != ''){
                    successToast('Email Settings Deleted Successfully', 'success');
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


