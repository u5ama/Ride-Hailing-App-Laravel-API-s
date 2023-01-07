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
            url: APP_URL + '/languageScreen',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'family_name', name: 'family_name'},
            {data: 'blsc_title', name: 'blsc_title'},
            {data: 'blsc_image', name: 'blsc_image',
                "render": function (data, type, full, meta) {
                    var base_url = window.location.origin
                    if (data != null){
                        var str_contains = data.includes(".pdf");
                        if(str_contains){
                            return "<a href="+ base_url + "/" + data + "> <iframe src=\"" + base_url + "/" + data + "\" height=\"50\"></iframe> </a>";
                        }else {
                            return "<img src=\"" + base_url + "/" + data + "\" height=\"50\"/>";
                        }
                    }else{
                        return "empty";
                    }
                }},
            {data: 'name', name: 'name'},
            {data: 'viewScreen', name: 'viewScreen'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
            processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
        },
        order: [[0, 'ASC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: 'Destroy Language String?',
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
    });

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/languageScreen',
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
                        if ($('#form-method').val() === 'edit') {
                            setTimeout(function () {
                                window.location.href = APP_URL + '/languageScreen'
                            }, 1000);
                        }
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
    });

    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/languageScreen' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
    var url = window.location.pathname;
    var id = url.substring(url.lastIndexOf('/') + 1);
        const table_2 = $('#data-table_2').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: APP_URL + '/languageScreenView/'+ id,
                type: 'GET',
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'for', name: 'for'},
                {data: 'bls_screen_name', name: 'bls_screen_name'},
                {data: 'bls_string_type_id', name: 'bls_string_type_id',
                    "render": function (data, type, full, meta) {

                        if (data != null){

                            if(data == 1){
                                return "App Screen";
                            }else {
                                return "Back end";
                            }
                        }else{
                            return "empty";
                        }
                    }},
                {data: 'bls_screen_info', name: 'bls_screen_info'},
                {data: 'bls_name_key', name: 'bls_name_key'},
                {data: 'name', name: 'name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function () {
                funTooltip()
            },
            language: {
                processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
            },
            order: [[0, 'ASC']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
        })


});
function updatestatus(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/languageScreen' + '/' + id+ '/' + status,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');
            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
    setTimeout(function () {
        $('#data-table').DataTable().ajax.reload();
    }, 1000);
}
function updatestatuslangstring(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/languageString' + '/' + id+ '/' + status,
        async: false,
        success: function (data) {
            successToast(data.message, 'success');
            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
    setTimeout(function () {
        $('#data-table_2').DataTable().ajax.reload();
    }, 1000);
}



