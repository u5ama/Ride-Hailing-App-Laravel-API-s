$(function () {
    loadChartData();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: APP_URL + '/rideCancelByPassenger',
            data: function (d) {
                d.start_date = $('#start_date_cancel_pass').val();
                d.end_date = $('#end_date_cancel_pass').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'id', name: 'id'},
            {data: 'passenger_name', name: 'passenger_name'},
            {data: 'driver_name', name: 'driver_name'},
            {data: 'reason', name: 'reason'},
            {data: 'pcrh_comments', name: 'pcrh_comments'},
            {data: 'cancel_at', name: 'cancel_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false,width: "10%"},
            {data: 'totalCancel', name: 'totalCancel', orderable: false, searchable: false,width: "10%"},
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

    $('.filterCancel').click(function(){
        $('#data-table').DataTable().draw(true);
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: 'Destroy Default Image?',
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
    });


     $(document).on('click', '.ride-details', function () {
        const rideid = $(this).data('rideid');
        const driverid = $(this).data('driverid');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getRideCancelByPassenger'+ '/' + rideid + '/' + driverid,
            dataType: 'html',
            success: function (data) {

                $("#viewRideModelId").html(data);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    $(document).on('click', '#recGraph', function () {
        const filterType =  $(this).data('type');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getGraphRecordPassenger'+ '/' + filterType ,
            dataType: 'html',
            success: function (data) {
                let newData = JSON.parse(data);
                let graphData = newData.record;
                my_chart(graphData);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    });

    function loadChartData(){
        const filterType =  'monthly';
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getGraphRecordPassenger'+ '/' + filterType ,
            dataType: 'html',
            success: function (data) {
                let newData = JSON.parse(data);
                let graphData = newData.record;
                my_chart(graphData);

                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }

    function my_chart(graphData){
        const arr1 = graphData;
        const arr2 = graphData;

        const chartObject = {
            title: {
                text: ''
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            labels: {

            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },

            series: [{
                type: 'column',
                name: 'Passengers',
                data: []
            }, {
                type: 'spline',
                name: 'Average',
                data: [],
                marker: {
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[3],
                    fillColor: 'white'
                }
            }]
        };

         chartObject.series[0].data = arr1;
         chartObject.series[1].data = arr2;

         if (arr1.length > 7) {
             chartObject.xAxis.categories = [
                 'Jan',
                 'Feb',
                 'Mar',
                 'Apr',
                 'May',
                 'Jun',
                 'Jul',
                 'Aug',
                 'Sep',
                 'Oct',
                 'Nov',
                 'Dec'
             ];
         }else{
             chartObject.xAxis.categories = ['Mon','Tue', 'Wed','Thu', 'Fri', 'Sat', 'Sun'];
         }

        Highcharts.chart('container', chartObject);
    }


    $(document).on('click', '.ride-cancel-details', function () {
        const rideid = $(this).data('rideid');
        const driverid = $(this).data('driverid');
        loaderView();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/getRideCancelByPassengerDetails'+ '/' + rideid + '/' + driverid,
            dataType: 'html',
            success: function (data) {

                $("#viewRideModelDetails").html(data);

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


});

function viewMap(id){

    loaderView();
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getViewMapModal',
            data: {id:id},
            dataType: 'html',
            success: function (data) {
                $("#vie_map_id").html(data);
                loaderHide();

            }, error: function (data) {
                console.log('Error:', data)
            }
        })
}

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

