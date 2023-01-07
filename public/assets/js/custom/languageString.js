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
            url: APP_URL + '/languageString',
            data: function (d) {
                d.panel = $('#panel').val();
            },
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

    // $("#app_or_panel").change(function ()
    $(document).on('change', '.app_or_panel', function (){
        $.ajax({
            type: "POST",
            url: APP_URL + '/getLanguageScreen',
            data: {app_or_panel: $("#app_or_panel").val()},
            dataType: "html",
            success: function (data) {
                $("#language_screen_id").html(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })

    $("#filter").on('click' ,function () {
        table.draw();
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
                url: APP_URL + '/languageString',
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
                            window.location.href = APP_URL + '/languageString'
                        }, 1000);
                        if ($('#form-method').val() === 'edit') {
                            setTimeout(function () {
                                window.location.href = APP_URL + '/languageString'
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
            url: APP_URL + '/languageString' + '/' + value_id,
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
function updatestatus(id,status) {

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
        $('#data-table').DataTable().ajax.reload();
    }, 1000);
}

