$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#extra_charges').hide();

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

            {data: 'status', name: 'status'},
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
    })

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
    let $head = $('#EditPlanHeadForm');
    $head.on('submit', function (e) {
        e.preventDefault();
        $head.parsley().validate();
        if ($head.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#EditPlanHeadForm')[0])
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
                        $head[0].reset()
                        $head.parsley().reset();
                        successToast(data.message, 'success');
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

    let $form = $('#addEditForm');
    $form.on('submit', function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/FarePlanDetail',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        successToast(data.message, 'success');
                        getFareDetailAfterAddEdit();
                        setTimeout(function () {
                            window.location.href = APP_URL + '/farePlanHead'
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


    let $form_extra = $('#addEditExtraForm');
    $form_extra.on('submit', function (e) {
        e.preventDefault();
        $form_extra.parsley().validate();
        if ($form_extra.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditExtraForm')[0])
            $.ajax({
                url: APP_URL + '/fareExtraCharge',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    loaderHide();
                    if (data.success === true) {
                        successToast(data.message, 'success');
                        getFareExtraChargeAfterAddEdit(data.planDetailId,data.FarePlanHeadId);
                        setTimeout(function () {
                            window.location.href = APP_URL + '/farePlanHead'
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



    $(document).on('change', '#fpd_country_id,#fpd_transport_type_id', function () {

        if($("#fpd_country_id").val() != '' && $("#fpd_transport_type_id").val() != ''){
            $('#selectAlert').html('');
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getFareDetailData',
            data: {FarePlanHeadId: $("#FarePlanHeadId").val(),fpd_country_id: $("#fpd_country_id").val(),fpd_transport_type_id: $("#fpd_transport_type_id").val()},
            dataType: 'html',
            success: function (data) {
                $('#extra_charges').show();
                $("#fareDetail").html(data);
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
    });

    $('.clockpicker').clockpicker({
        placement: 'top',
        align: 'left',
        donetext: 'Done',
        autoclose: true
    })
// $("#fpd_transport_type_id").select2();


$('[data-toggle="tooltip"]').tooltip();
    var actions = '  <a class="delete"  data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a>';
    // Append table with add row form on add new button click
    $(".add-new").click(function(){
        if($("#fpd_transport_type_id").val() != ''){
        $('#selectAlert').html('');
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getFareDetailData',
            data: {FarePlanHeadId: '',fpd_country_id: '',fpd_transport_type_id: ''},
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

    $(".add-new_extra").click(function(){


       var index = $("#tbl_add tbody tr:last-child").index();
        var row = '<tr>' +
            '<input type="hidden" class="form-control" name="fareExtraId[]" id="fareExtraId" value="0">'+
            '<td><input type="text" class="form-control" name="efc_key[]" id="efc_key"></td>' +
            '<td><input type="text" class="form-control" name="efc_info[]" id="efc_info"></td>' +
            '<td><input type="text" class="form-control" name="efc_charge[]" id="efc_charge"></td>' +
            '<td>' + '<a class="delete"  data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a>' + '</td>' +
        '</tr>';
        $("#tbl_add").append(row);



    });
    // Add row on add button click
   // Edit row on edit button click
    $(document).on("click", ".edit", function(){
        $(this).parents("tr").find("td:not(:last-child)").each(function(){
            if($(this).text() == 'Out' || $(this).text() == 'In'){
                if($(this).text() == 'Out'){
                   $(this).html('<select  class="form-control"><option value="Out">Out</option><option value="In">In</option></select>');
                }else{
                    $(this).html('<select  class="form-control"><option value="In">In</option><option value="Out">Out</option></select>');
                }

            }else{
            $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
            }
        });
        $(this).parents("tr").find(".add, .edit").toggle();

    });

    // Delete row on delete button click
    $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();

    });

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


 function deleteRecord(value_id) {

    $.ajax({
            type: 'GET',
            url: APP_URL + '/checkExistExtraCharges' + '/' + value_id+'/' + $("#FarePlanHeadId").val(),
            success: function (data) {
           if(data.message == 'yes'){
            swal({
            title: 'Destroy Fare Plan Detail?',
            text: 'Are you sure you want to permanently remove this record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: 'Yes Delete It',
            cancelButtonText: 'No Cancel Plx',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                deleteDetailFare(value_id);
            }
        });
                }else{
                    successToast(data.message, 'error');
                }

            }, error: function (data) {
                console.log('Error:', data)
            }
        })


    }

    function deleteDetailFare(value_id){
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/FarePlanDetail' + '/' + value_id,
            success: function (data) {
               successToast(data.message, 'success');
               $('#deletePlanDetail_'+value_id).remove();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }

    function getFareDetailAfterAddEdit(){
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getFareDetailData',
            data: {FarePlanHeadId: $("#FarePlanHeadId").val(),fpd_country_id: $("#fpd_country_id").val(),fpd_transport_type_id: $("#fpd_transport_type_id").val()},
            dataType: 'html',
            success: function (data) {
                $("#fareDetail").html(data);

            }, error: function (data) {
                console.log('Error:', data)
            }
        })

    }


    function extraFareCharege(planDetailId){

        $("#planDetailID").text(planDetailId);
        $("#efc_plan_detail_id").val(planDetailId);
        $("#efc_plan_head_id").val($("#FarePlanHeadId").val());

        getFareExtraChargeAfterAddEdit(planDetailId,$("#FarePlanHeadId").val() );
    }

    function getFareExtraChargeAfterAddEdit(planDetailId,efc_plan_head_id){
       loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getFareExtraModalData',
            data: {efc_plan_detail_id: planDetailId,efc_plan_head_id: efc_plan_head_id},
            dataType: 'html',
            success: function (data) {
                $("#fareExtraChargeModal").html(data);
                loaderHide();

            }, error: function (data) {
                console.log('Error:', data)
            }
        })

    }

    function deleteExtraFareCharge(value_id) {

        swal({
            title: 'Destroy Extra Fare Charges?',
            text: 'Are you sure you want to permanently remove this record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: 'Yes Delete It',
            cancelButtonText: 'No Cancel Plx',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                deleteExtraFare(value_id);
            }
        });
    }

    function deleteExtraFare(value_id){
        $.ajax({
            type: 'GET',
            url: APP_URL + '/deleteExtraFareCharge' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                getFareExtraChargeAfterAddEdit($("#efc_plan_detail_id").val(),$("#FarePlanHeadId").val() );

            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }







