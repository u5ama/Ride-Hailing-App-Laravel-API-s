@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3" style="text-align: center;display: flex;justify-content: center;">
                    <h4 class="content-title mb-0 mt-4">All Rides</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">

                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date_booking" id="start_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date_booking" id="end_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                        </div>
                        <div class="col-md-2">
                                <button class="filterBooking btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%">
                                    Filter
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Passenger</th>
                                <th>Transport Type</th>
                                <th>Driving Start Time</th>
                                <th>Driving End Time</th>
                                <th>Total Driving Time</th>
                                <th>Waite Start Time</th>
                                <th>Waite End Time</th>
                                <th>Status</th>
                                <th>View Map</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
    <div class="modal" id="modaldemo3">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">View Map</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div style="width: 600px; height: 400px;" id="map_canvas"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/rideBookingSchedule.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvRPR8W93pV4cHO6iEabc61OgS3-JPscY"></script>

    <script>
        var map = null;
        var myMarker;
        var myLatlng;

        function initializeGMap(lat, lng) {
            myLatlng = new google.maps.LatLng(lat, lng);

            var myOptions = {
                zoom: 12,
                zoomControl: true,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            myMarker = new google.maps.Marker({
                position: myLatlng
            });
            myMarker.setMap(map);
        }

        // Re-init map before show modal
        $('#modaldemo3').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            initializeGMap(button.data('lat'), button.data('lng'));

            $("#map_canvas").css("width", "100%");
        });

        // Trigger map resize event after modal shown
        $('#modaldemo3').on('shown.bs.modal', function () {
            google.maps.event.trigger(map, "resize");
            map.setCenter(myLatlng);
        });
    </script>
@endsection
