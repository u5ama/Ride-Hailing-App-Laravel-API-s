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
            url: APP_URL + '/driverStatusReport',
            data: function (d) {
                d.filterWithCountry = $('#filterWithCountry').val();
                d.filterByCompany = $('#filterByCompany').val();
                d.driverNumber =   $('#filterByNumber').val();
                d.driverVehicle = $('#filterByVehicle').val();
            },
            type: 'GET',
        },

        columns: [
            {data: 'vehicle_type', name: 'vehicle_type'},
            {data: 'vehicle_no', name: 'vehicle_no'},
            {data: 'license_no', name: 'license_no'},
            {data: 'mobile_no', name: 'mobile_no'},
            {data: 'captain_name', name: 'captain_name'},
            {data: 'company_name', name: 'company_name'},
            {data: 'driver_status', name: 'driver_status'},
            {data: 'country', name: 'country'},
            {data: 'city', name: 'city'},

        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
           processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
         //  processing: ''
        },
        order: [],
        lengthMenu: [[100, 125, 150, -1], [100, 125, 150, 'All']]
    });




    $(document).on('change', '#filterWithCountry', function () {
        var _token=$('input[name=_token]').val();
        const country_id = $('#filterWithCountry').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDriverStatusFilter',
            data:{_token:_token,country_id:country_id},
            success: function(data){
                console.log(data);
                $('#onlineDrivers').html(data.onlineDrivers);
                 $('#offlineDrivers').html(data.offlineDrivers);
                 $('#busyDrivers').html(data.busyDrivers);
                $('#data-table').DataTable().draw(true);
            }
        });
    });

    $(document).on('change', '#filterByCompany', function () {
        var _token=$('input[name=_token]').val();
        const company_id = $('#filterByCompany').val();
        const country_id = $('#filterWithCountry').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDriverStatusFilter',
            data:{_token:_token,company_id:company_id,country_id:country_id},
            success: function(data){
                console.log(data);
               $('#onlineDrivers').html(data.onlineDrivers);
                 $('#offlineDrivers').html(data.offlineDrivers);
                 $('#busyDrivers').html(data.busyDrivers);
                $('#data-table').DataTable().draw(true);
            }
        });
    });



    $('#filterForm').on('submit', function(e){
    e.preventDefault();
    const country_id =  $('#filterWithCountry').val();
    const company_id =  $('#filterByCompany').val();
    const driverNumber =  $('#filterByNumber').val();
    const driverVehicle =  $('#filterByVehicle').val();
    var _token=$('input[name=_token]').val();
    if (company_id !== '' || driverNumber !== ''|| driverVehicle !== ''){
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getDriverStatusFilter',
            data:{_token:_token,country_id:country_id,company_id:company_id,driverNumber:driverNumber,driverVehicle:driverVehicle},
            success: function (data) {
                $('#onlineDrivers').html(data.onlineDrivers);
                 $('#offlineDrivers').html(data.offlineDrivers);
                 $('#busyDrivers').html(data.busyDrivers);
                $('#data-table').DataTable().draw(true);

            }, error: function (data) {
                $('#companyId').text(data.responseJSON.errors.company_id);
                $('#driverNumberError').text(data.responseJSON.errors.driverNumber);
                $('#driverVehicleError').text(data.responseJSON.errors.driverVehicle);
            }
        });
    }
});





});

function getCompanies(country_id){
    var _token=$('input[name=_token]').val();
    $.ajax({
        type: "POST",
        url:  APP_URL + '/getCompanies',
        data:{_token:_token,com_country_id:country_id},
        beforeSend: function(){
            // $("#preloader").css("display","block");
        },success: function(data){
            // $("#preloader").css("display","none");
            $('#filterByCompany').html(data);
        }
    });
}

function getDriversNumbers(company_id){
    var _token=$('input[name=_token]').val();
    const country_id =  $('#filterWithCountry').val();
    $.ajax({
        type: "POST",
        url:  APP_URL + '/getCompanyDrivers',
        data:{_token:_token,com_id:company_id,country_id:country_id},
        beforeSend: function(){
            // $("#preloader").css("display","block");
        },success: function(data){
            // $("#preloader").css("display","none");
            $('#filterByNumber').html(data);
           // $('#filterByNumber').multiselect('refresh');
        }
    });
}

function getDriversVehicles(company_id){
    var _token=$('input[name=_token]').val();
    const country_id =  $('#filterWithCountry').val();
    $.ajax({
        type: "POST",
        url:  APP_URL + '/getCompanyDriversVeh',
        data:{_token:_token,com_id:company_id,country_id:country_id},
        beforeSend: function(){
            // $("#preloader").css("display","block");
        },success: function(data){
            // $("#preloader").css("display","none");
            $('#filterByVehicle').html(data);
          //  $('#filterByVehicle').multiselect('dataprovider', data);
        }
    });
}

