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
            url: APP_URL + '/farePlanHead',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'fph_plan_name', name: 'fph_plan_name'},
            {data: 'fph_fare_type', name: 'fph_fare_type'},
            {data: 'country_name', name: 'country_name'},
            {data: 'fph_vat_per', name: 'fph_vat_per'},
            {data: 'fph_tax_per', name: 'fph_tax_per'},
            {data: 'start_date', name: 'start_date'},
            {data: 'end_date', name: 'end_date'},
            {data: 'fph_is_default', name: 'fph_is_default'},
            {data: 'status', name: 'status'},
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
        const value_id = $(this).data('planid');

        swal({
            title: 'Destroy Fare Plan?',
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

    let $form = $('#addEditForm');
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/farePlanHead',
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
                            window.location.href = APP_URL + '/farePlanHead'
                        }, 1000);
                        if ($('#form-method').val() === 'edit') {
                        $("#edit_value").val('');
                        $("#form-method").val('add');
                        $("#btn_txt").text('Add');
                    }
                        setTimeout(function () {
                            $('#data-table').DataTable().ajax.reload();
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

    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/farePlanHead' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw();
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
                {data: 'action', name: 'action', orderable: false, searchable: false,width: "30%"},
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

$(document).on('click', '.plan-details', function () {
    const planId = $(this).data('planid');

    loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/FarePlanDetailViewModal'+ '/' + planId,
        dataType: 'html',
        success: function (data) {
            $("#globalModalDetails").html(data);

            loaderHide();
        }, error: function (data) {
            console.log('Error:', data)
        }
    })
});

function updateStatus(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/farePlanHead' + '/' + id+ '/' + status,
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

function editFarePlanHead(id){
 loaderView();
    $.ajax({
        type: 'GET',
        url: APP_URL + '/getFarePlanHeadByid' + '/' + id,
        async: false,
        success: function (data) {
           $("#edit_value").val(id);
           $("#form-method").val('edit');
           $("#btn_txt").text('Edit');
           $('#fph_plan_name').val(data.fph_plan_name);
           $('#fph_description').val(data.fph_description);
           $('#fph_vat_per').val(data.fph_vat_per);
           $('#fph_tax_per').val(data.fph_tax_per);
           $('#start_date').val(data.start_date);
           $('#end_date').val(data.end_date);
           var fph_country_id = data.fph_country_id;
           var countryOption = document.getElementById("fph_country_id");
            optionLength = $("#fph_country_id option").length;

            for (var x = 0; x < optionLength; x++) {
              if (countryOption.options[x].value == fph_country_id) {
                countryOption.options[x].selected = true;
              }
            }

           if(data.fph_fare_type == 'intercity'){
            document.getElementById("fph_fare_type").selectedIndex = "0";
        }else{
            document.getElementById("fph_fare_type").selectedIndex = "1";
        }

            loaderHide();

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
}
