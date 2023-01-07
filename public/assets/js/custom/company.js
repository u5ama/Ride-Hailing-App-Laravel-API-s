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
            url: APP_URL + '/company',
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'logo', name: 'logo'},
            {data: 'com_name', name: 'com_name'},
            {data: 'email', name: 'email'},
            {data: 'com_contact_number', name: 'com_contact_number'},
            {data: 'com_license_no', name: 'com_license_no'},
            {data: 'drivers_count', name: 'drivers_count'},
            {data: 'com_radius', name: 'com_radius'},
            {data: 'status', name: 'status'},
            {data: 'change_status', name: 'change_status'},
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
            title: 'Destroy Company?',
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
    })

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])

            $.ajax({
                url: APP_URL + '/company',
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
                                window.location.href = APP_URL + '/company'
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
    })
    $(document).on('change', '#fpd_transport_type_id', function () {

        if($("#fpd_transport_type_id").val() != ''){
            $('#selectAlert').html('');
            loaderView();
            $.ajax({
                type: 'POST',
                url: APP_URL + '/getCommissionDetailData',
                data: {companyId: $("#companyId").val(),fpd_transport_type_id: $("#fpd_transport_type_id").val()},
                dataType: 'html',
                success: function (data) {
                    $("#commissionDetail").html(data);
                    loaderHide();
                }, error: function (data) {
                    console.log('Error:', data)
                }
            })
        }
    });

    let $formComm = $('#addEditCommission');
    $formComm.on('submit', function (e) {
        e.preventDefault();
        $formComm.parsley().validate();
        if ($formComm.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditCommission')[0])
            $.ajax({
                url: APP_URL + '/storeCommissionData',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        successToast(data.message, 'success');
                        setTimeout(function () {
                            window.location.href = APP_URL + '/company'
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
    });


     $(document).on('click', '.company-status', function () {
        const value_id = $(this).data('id');

        loaderView();
        let effect = $(this).attr('data-effect');
        $('#globalModal').addClass(effect).modal('show');

        $.ajax({
            type: 'GET',
            url: APP_URL + '/getCompanyStatus'+ '/' + value_id,
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


    $('[data-toggle="tooltip"]').tooltip();
    var actions = '  <a class="delete"  data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a>';
    // Append table with add row form on add new button click
    $(".add-new").click(function(){
        if($("#fpd_transport_type_id").val() != ''){
            $('#selectAlert').html('');
            loaderView();
            $.ajax({
                type: 'POST',
                url: APP_URL + '/getCommissionDetailData',
                data: {companyId: '',fpd_transport_type_id: ''},
                dataType: 'html',
                success: function (data) {
                    var index = $("#tbls tbody tr:last-child").index();
                    var row =data;
                    $("#tbls").append(row);
                    loaderHide();
                }, error: function (data) {
                    console.log('Error:', data)
                }
            })
        }else{
            $('#selectAlert').show();
            $('#selectAlert').html('<span style="color:red">**Please Transport Type From given above selection**</span>')
        }



    });
});

function updatestatus(id) {
  var status = $("#com_status_"+id).val();
  loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/company' + '/' + id+ '/' + status,
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

