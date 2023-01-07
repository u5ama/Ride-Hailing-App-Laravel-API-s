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
            url: APP_URL + '/currencies',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'cu_title', name: 'cu_title'},
            {data: 'cu_code', name: 'cu_code'},
            {data: 'cu_symbol_left', name: 'cu_symbol_left'},
            {data: 'cu_symbol_right', name: 'cu_symbol_right'},
            {data: 'cu_decimal_places', name: 'cu_decimal_places'},
            {data: 'cu_value', name: 'cu_value'},
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
            title: 'Destroy Currency?',
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
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0]);
            $.ajax({
                url: APP_URL + '/currencies',
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
            url: APP_URL + '/currencies' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw();
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
});
function updateStatus(id,status) {

    $.ajax({
        type: 'GET',
        url: APP_URL + '/currencies' + '/' + id+ '/' + status,
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



