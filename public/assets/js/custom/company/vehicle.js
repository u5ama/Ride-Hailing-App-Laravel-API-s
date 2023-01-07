$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP_URL + '/vehicle',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'address', name: 'address'},
            {data: 'color', name: 'color'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: "30%"},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
            processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
        },
        order: [[0, 'DESC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    })

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: 'Destroy Body?',
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
            }
        });
    })

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        console.log($form.parsley().isValid());
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/vehicle',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        $form[0].reset()
                        $form.parsley().reset();
                        successToast(data.message, 'success')
                        setTimeout(function () {
                            window.location.href = APP_URL + '/vehicle'
                        }, 1000);

                    } else if (data.success === false) {
                        successToast(data.message, 'warning')
                    }
                },
                error: function (data) {
                    loaderHide();
                    console.log('Error:', data)
                }
            })
        }
    })

    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/vehicle' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }

    $("#feature").select2({
        placeholder: "Please Select Feature"
    });
    $("#featured").select2({
        placeholder: "Please Select Featured"
    });

    $(document).on('change', '#company_id', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getBranch',
            data: {company_id: $(this).val()},
            dataType: 'html',
            success: function (data) {
                $("#company_address_id").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    })


    $("#company_id,#company_address_id,#make,#year,#model,#color").select2();
    $('.dropify').dropify();

    floatOnly();
    integerOnly();

    $(document).on('change', '#make', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getModel',
            data: {brand_id: $(this).val()},
            dataType: 'html',
            success: function (data) {
                $("#model").html(data);
                $("#ryde").html('');
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });


     $(document).on('change', '#model,#year', function () {
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getRyde',
            data: {brand_id: $("#make").val(),model_id: $("#model").val(),year_id: $("#year").val()},
            dataType: 'html',
            success: function (data) {
                $("#ryde").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

      $(document).on('click', '.status-change', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('status');

        swal({
            title: 'Change Body?',
            text: 'Are you sure you want to change status of this record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                changeStatus(value_id,status)
            }
        });
    });

      function changeStatus(value_id,status) {
        $.ajax({
            type: 'GET',
            url: APP_URL + '/changeStatus/' + value_id + '/' + status,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }

     $(document).on('click', '.vehicle-details', function () {
        const value_id = $(this).data('id');

        loaderView();
        let effect = $(this).attr('data-effect');
        $('#globalModal').addClass(effect).modal('show');

        $.ajax({
            type: 'GET',
            url: APP_URL + '/vehicleDetails'+ '/' + value_id,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#globalModalTitle").html(data.data.globalModalTitle);
                $("#globalModalDetails").html(data.data.globalModalDetails);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    })

})



