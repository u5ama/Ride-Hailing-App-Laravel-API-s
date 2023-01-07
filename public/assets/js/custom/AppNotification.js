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
            url: APP_URL + '/appNotification',
            data: function (d) {
                d.start_date = $('#start_date_notification').val();
                d.end_date = $('#end_date_notification').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'sender_name', name: 'sender_name'},
            {data: 'recipient_name', name: 'recipient_name'},
            {data: 'ban_type_of_notification', name: 'ban_type_of_notification'},
            {data: 'notification_status', name: 'notification_status'},
            {data: 'ban_title_text', name: 'ban_title_text'},
            {data: 'ban_body_text', name: 'ban_body_text'},
            {data: 'ban_activity', name: 'ban_activity'},
            {data: 'ban_created_at', name: 'ban_created_at'},
            {data: 'ban_is_unread', name: 'ban_is_unread',"render": function (data, type, full, meta) {

                    if(data == 1){
                        data = "Yes";
                        return data;
                    }else {
                        data = "No";
                        return data;
                    }
                }},
            {data: 'ban_is_hidden', name: 'ban_is_hidden',"render": function (data, type, full, meta) {

                    if(data == 1){
                        data = "Yes";
                        return data;
                    }else {
                        data = "No";
                        return data;
                    }
                }},
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

    $('.filterNotifications').click(function(){
        $('#data-table').DataTable().draw(true);
    });


    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: 'Destroy App Notification?',
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

    let $form = $('#addEditForm');
    $form.on('submit', function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])

            $.ajax({
                url: APP_URL + '/appNotification',
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
                        successToast(data.message, 'success');

                            setTimeout(function () {
                                window.location.href = APP_URL + '/appNotification'
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
            url: APP_URL + '/appNotification' + '/' + value_id,
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
        url: APP_URL + '/appNotification' + '/' + id+ '/' + status,
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

    $(document).on('change', '#target_type', function () {
        var target_type = $("#target_type").val();

        if (target_type === 'all_whipp'){
            $('#country_drop').hide();
            $('#app_drop').hide();
            $('#user_drop').hide();
            $('#device_drop').hide();
        }
        if (target_type === 'app'){
            $('#country_drop').hide();
            $('#app_drop').show();
            $('#user_drop').hide();
            $('#device_drop').hide();
        }
        if (target_type === 'device'){
            $('#country_drop').hide();
            $('#app_drop').hide();
            $('#user_drop').hide();
            $('#device_drop').show();
        }
        if (target_type === 'select_country'){
            $('#country_drop').show();
            $('#app_drop').show();
            $('#user_drop').hide();
            $('#device_drop').hide();
        }
        if (target_type === 'select_customer'){
            $('#country_drop').show();
            $('#app_drop').show();
            $('#user_drop').show();
            $('#device_drop').hide();
        }

        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getUserList',
            data: {
                target_type: target_type,
            },
            dataType: 'html',
            success: function (data) {
                if(target_type =='select_customer'){
                    $("#user_id").show();

                    $("#userlist").html(data);
                    $("#countrylist").html('');
                }
                if(target_type =='select_country'){
                    $("#country_g").show();
                    $("#userlist").html('');

                }

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });


    $(document).on('change', '#country_id', function () {
        var country_id = $("#country_id").val();
        loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getUserList',
            data: {
                country_id: country_id,
                target_type: 'select_customer',
            },
            dataType: 'html',
            success: function (data) {
                    $("#user_id").show();
                    $("#userlist").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

$(document).on('change', '#allcheckbox', function () {

    if($("#allcheckbox").prop("checked") == true){
       $('input[class=source]').prop('checked', true);
    }else{
      $('input[class=source]').prop('checked', false);
    }
});


