jQuery(function($) {

    var script = document.createElement('script');
    script.type = "text/javascript";
    script.src = "https://maps.google.com/maps/api/js?key=AIzaSyBvRPR8W93pV4cHO6iEabc61OgS3-JPscY&callback=initialize";
    document.body.appendChild(script);

    getDrivers();
});
 let allDrivers = [];

 function getDrivers(){
     const country_id = $('#filterWithCountry').val();

     const company_id =  $('#filterByCompany').val();
     const driverNumber =  $('#filterByNumber').val();
     const driverVehicle =  $('#filterByVehicle').val();
     var _token=$('input[name=_token]').val();
     if (company_id != '' && driverNumber != '' && driverVehicle != ''){
         $.ajax({
             type: 'POST',
             url: APP_URL + '/getDriversFilter',
             data:{_token:_token,country_id:country_id,company_id:company_id,driverNumber:driverNumber,driverVehicle:driverVehicle},
             success: function (data) {
                 allDrivers = data.drivers;
                 $('#onlineDrivers').html(data.onlineDrivers);
                 $('#offlineDrivers').html(data.offlineDrivers);
                 $('#busyDrivers').html(data.busyDrivers);
                 initialize();
                 loaderHide();
             }, error: function (data) {
                 $('#companyId').text(data.responseJSON.errors.company_id);
                 $('#driverNumberError').text(data.responseJSON.errors.driverNumber);
                 $('#driverVehicleError').text(data.responseJSON.errors.driverVehicle);
             }
         });
     }
     else {
         $.ajax({
             type: 'GET',
             url: APP_URL + '/getDriversRecord/'+country_id,
             success: function (data) {
                 allDrivers = data.drivers;
                 $('#onlineDrivers').html(data.onlineDrivers);
                 $('#offlineDrivers').html(data.offlineDrivers);
                 $('#busyDrivers').html(data.busyDrivers);

                 $('#companyId').text('');
                 $('#driverNumberError').text('');
                 $('#driverVehicleError').text('');

                 initialize();
                 loaderHide();
             }, error: function (data) {
                 console.log('Error:', data)
             }
         });
     }
 }


var driverData = '';
var driverStatus = '';
var driverTime = '';
var driverCrrCity = '';

function driverDetail(driverId)
{
    'use strict';
    $.ajax({
        type: 'GET',
        url: APP_URL + '/getDriversDetail/'+driverId,
        success: function (data) {
            driverData = data.driver;
            driverStatus = data.status;
            driverTime = data.time;
            driverCrrCity = data.driverCity;
        }, error: function (data) {
            console.log('Error:', data)
        }
    });
    if (driverData !== ''){
        let box = '';
        let veh_no = '';
        let licence = '';
        let driverCrrLoc = '';
        if (driverData.driver_prof === null){
            veh_no = ' null';
            licence = ' null';
        }else{
            veh_no = driverData.driver_prof.car_registration;
            licence = driverData.driver_prof.dp_license_number;
        }
        let timeAgo;
        if (driverStatus === 'online'){
            timeAgo = '<span> Online '+driverTime +'mins ago</span>';
        }else{
            timeAgo = '<span> Offline '+driverTime +'mins ago</span>';
        }
        driverCrrLoc = driverCrrCity;
        box = '<b>Vehicle#</b><span>'+veh_no+'</span><br>'+'<b>Licence#</b><span>'+licence+'</span><br>'+'<b>Mobile:</b><span>'+driverData.du_full_mobile_number+'</span><br>'+'<b>Name:</b><span>'+driverData.du_full_name+'</span><br>'+'<b>Company:</b><span>'+driverData.company.com_name+'</span><br>'+'<b>Driver Status:</b>'+timeAgo+'<br>'+'<b>Driver City: </b>'+driverCrrLoc+'<br>';
        return box;
    }
}

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
            url: APP_URL + '/getDriversFilter',
            data:{_token:_token,country_id:country_id,company_id:company_id,driverNumber:driverNumber,driverVehicle:driverVehicle},
            success: function (data) {
                allDrivers = data.drivers;
                $('#onlineDrivers').html(data.onlineDrivers);
                $('#offlineDrivers').html(data.offlineDrivers);
                $('#busyDrivers').html(data.busyDrivers);
                initialize();
                loaderHide();
            }, error: function (data) {
                $('#companyId').text(data.responseJSON.errors.company_id);
                $('#driverNumberError').text(data.responseJSON.errors.driverNumber);
                $('#driverVehicleError').text(data.responseJSON.errors.driverVehicle);
            }
        });
    }
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

 function initialize() {
    let map;
    let bounds = new google.maps.LatLngBounds();
    let mapOptions = {
        mapTypeId: 'roadmap',
        zoom: 20
    };

   // Display a map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    map.setTilt(45);

    // Multiple Markers

    var markers = [...allDrivers];
    var infoWindowContent = [...allDrivers];
    var getUrl = window.location;
   // const iconBase = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" + getUrl.pathname.split('/')[2] + "/" + getUrl.pathname.split('/')[3];
    const iconBase = getUrl .protocol + "//" + getUrl.host;

    const icons = {
        2: {
            icon: iconBase + "/assets/img/yellow.png",
        },
        1: {
            icon: iconBase + "/assets/img/green.png",
        },
        0: {
            icon: iconBase + "/assets/img/red.png",
        }
    };


    var infoWindow = new google.maps.InfoWindow(), marker, i;


    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng(markers[i].dcl_lat, markers[i].dcl_long);
        bounds.extend(position);
        if (markers[i].dcl_app_active == 1 && markers[i].isBusy == true){
            markers[i].dcl_app_active = 2;
        }
        marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: icons[markers[i].dcl_app_active].icon,
            title: markers[i].dcl_user_type
        });

        // Allow each marker to have an info window
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(driverDetail(infoWindowContent[i].dcl_user_id));
                infoWindow.open(map, marker);
            }
        })(marker, i));
        // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
    }
    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(9);
        google.maps.event.removeListener(boundsListener);
    });
}

