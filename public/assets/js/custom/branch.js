$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        swal({
            title: 'Destroy Dealer?',
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
    })

    let $form = $('#addEditForm')
    $form.on('submit', function (e) {
        e.preventDefault()
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($('#addEditForm')[0])
            $.ajax({
                url: APP_URL + '/branchAdd',
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
                            window.location.href = APP_URL + '/dealer/' + $("#user_id").val()
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
    })

    function deleteRecord(value_id) {
        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/dealer' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }

    $('.dropify').dropify();


    integerOnly();


    $('.main-toggle').on('click', function () {
        let toggle_id = $(this).attr('id');
        $(this).toggleClass('on');
        if ($(this).hasClass('on')) {
            $("#start_" + toggle_id).attr('disabled', false);
            $("#end_" + toggle_id).attr('disabled', false);
            $("#day_value_" + toggle_id).val(1);
        } else {
            $("#start_" + toggle_id).attr('disabled', true);
            $("#end_" + toggle_id).attr('disabled', true);
            $("#day_value_" + toggle_id).val(0);
        }
    })

    $("#same_as_contact_details").on('change', function () {
        if ($(this).is(':checked')) {
            $(".same_contact").addClass('d-none');
        } else {
            $(".same_contact").removeClass('d-none');
        }
    });

    $('.clockpicker').clockpicker({
        placement: 'top',
        align: 'left',
        donetext: 'Done',
        autoclose: true
    })

});

var marker;
var map;
var infowindow;
var address = ''

function initMap() {
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: {lat: 29.3117, lng: 47.4818},
        zoom: 8
    });
    //var card = document.getElementById('pac-card');
    var input = document.getElementById('address');
    // map.controls[google.maps.ControlPosition.TOP_CENTER].push(card);

    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo('bounds', map);

    // Set the data fields to return when the user selects a place.
    autocomplete.setFields(
        ['address_components', 'geometry', 'icon', 'name']);

    infowindow = new google.maps.InfoWindow();
    var infowindowContent = document.getElementById('infowindow-content');
    infowindow.setContent(infowindowContent);
    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29)
    });

    autocomplete.addListener('place_changed', function () {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {

            map.fitBounds(place.geometry.viewport);
        } else {

            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].long_name || ''),
                (place.address_components[1] && place.address_components[1].long_name || ''),
                (place.address_components[2] && place.address_components[2].long_name || ''),
                (place.address_components[3] && place.address_components[3].long_name || ''),
                (place.address_components[4] && place.address_components[4].long_name || ''),
                (place.address_components[5] && place.address_components[5].long_name || ''),
                (place.address_components[6] && place.address_components[6].long_name || ''),
                (place.address_components[7] && place.address_components[7].long_name || ''),
                (place.address_components[8] && place.address_components[8].long_name || '')
            ].join(' ');
        }

        google.maps.event.addListener(marker, 'click', function () {
            if (marker.formatted_address) {
                infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));

                $("#address").val(marker.formatted_address);
                //console.log(marker.formatted_address);
                $("#pac-input").val(marker.formatted_address);

            } else {
                infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                $("#address").val(address);
                $("#pac-input").val(address);
            }
            $("#latitude").val(marker.getPosition().lat());
            $("#longitude").val(marker.getPosition().lng());
            infowindow.open(map, marker);
        });
        google.maps.event.trigger(marker, 'click');

        google.maps.event.addListener(marker, 'dragend',
            function () {
                geocodePosition(marker.getPosition());
                //console.log(marker.getPosition().toUrlValue(6));
                if (marker.formatted_address) {
                    infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                    $("#address").val(marker.formatted_address);
                    $("#pac-input").val(marker.formatted_address);

                } else {
                    infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                    $("#address").val(address);
                    $("#pac-input").val(address);
                }
                $("#latitude").val(marker.getPosition().lat());
                $("#longitude").val(marker.getPosition().lng());

                infowindow.open(map, marker);
            }
        );
    });
}

function geocodePosition(pos) {
    geocoder.geocode({
        latLng: pos
    }, function (responses) {
        if (responses && responses.length > 0) {
            marker.formatted_address = responses[0].formatted_address;
        } else {
            marker.formatted_address = 'Cannot determine address at this location.';
        }
    });
}

$('#address').keydown(function (e) {
    if (e.which === 13 && $('.pac-container:visible').length) return false;
});

function addMarker(lat, long, location) {
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, long),
        draggable: true,
        animation: google.maps.Animation.DROP,
        map: map
    });
    map.setCenter(new google.maps.LatLng(lat, long));
    map.setZoom(17);

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(location);
        infowindow.open(map, marker);
    });

    google.maps.event.addListener(marker, 'select', function () {
        infowindow.setContent(location);
        infowindow.open(map, marker);
    });

    google.maps.event.addListener(marker, 'dragend',
        function () {
            geocodePosition(marker.getPosition());
            //console.log(marker.getPosition().toUrlValue(6));
            if (marker.formatted_address) {
                infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                $("#address").val(marker.formatted_address);
                $("#pac-input").val(marker.formatted_address);

            } else {
                infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
                $("#address").val(address);
                $("#pac-input").val(address);
            }
            $("#latitude").val(marker.getPosition().lat());
            $("#longitude").val(marker.getPosition().lng());

            infowindow.open(map, marker);
        }
    );
}



