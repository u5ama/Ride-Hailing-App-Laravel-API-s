
var lat = '';
var long = '';
var des_lat = '';
var des_long = '';
var APP_URL = 'https://app.apis.ridewhipp.com/api/v1';
var BASE_URL = 'https://app.ridewhipp.com';
getRideAddressUpdate();
function getRideAddressUpdate() {
    // var ride_id = $("#driver_status_"+id).val();
    var ride_id = $("#ride_id").val();

    $.ajax({
        type: 'GET',
        url:  APP_URL + '/getRideUpdatedAddress' + '/' + ride_id,
        async: false,
        success: function (data) {
            if (JSON.stringify(data) !== '{}'){
                 lat = data.rbs_driver_lat;
                 long = data.rbs_driver_long;
                 des_lat = parseFloat(data.rbs_destination_lat);
                 des_long = parseFloat(data.rbs_destination_long);
                initMap();

            }else{
                window.location.href = BASE_URL + '/success';
                initMap();
            }

        }, error: function (data) {
            console.log('Error:', data)
        }
    });
}

setInterval(function () {
    getRideAddressUpdate();
}, 10000);
function loaderView() {
    $.blockUI({
        message: '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>',
        css: {
            padding: 0,
            margin: 0,
            width: "25%",
            top: "40%",
            left: "35%",
            textAlign: "center",
            color: "#000",
            border: "none",
            backgroundColor: "transparent",
            cursor: "wait",
            "z-index": "999999"
        }
    });
}
function loaderHide() {
    $.unblockUI();
}
function initMap() {

    var start = '';
    var end = '';

    console.log(lat);
    console.log(long);
    console.log(des_lat);
    console.log(des_long);
    if (lat !== '' && long !== '' && des_lat !== '' && des_long !== ''){
         start = [ lat, long];
         end = [ des_lat, des_long];
    }
    else{
          start = [ 29.385728444753358, 47.98690777228286];
          end = [ 29.388573880366785, 47.99089880668841];
    }
    var map;

    // Initialise the map
    var icon1 = {
        url: "https://app.ridewhipp.com/assets/location/pin.png", // url
        scaledSize: new google.maps.Size(30, 30), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(0, 0) // anchor
    };
    var icon2 = {
        url: "https://app.ridewhipp.com/assets/location/location.png", // url
        scaledSize: new google.maps.Size(30, 30), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(0, 0) // anchor
    };
    map = new google.maps.Map(document.getElementById('map'));
    // new google.maps.Marker({
    //     position: new google.maps.LatLng(start[0], start[1]),
    //     map: map,
    //   //  icon:icon1,
    //     // animation:google.maps.Animation.BOUNCE
    // });

    // new google.maps.Marker({
    //     position: new google.maps.LatLng(end[0], end[1]),
    //     map: map,
    //    // icon:icon2,
    //     // animation:google.maps.Animation.BOUNCE
    // });

    directionsService = new google.maps.DirectionsService;
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);

    directionsService.route({
        origin      : new google.maps.LatLng(start[0], start[1]),
        destination : new google.maps.LatLng(end[0], end[1]),
        travelMode  : google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        }
        else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}
