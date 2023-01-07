@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Company</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->

    <title>Event Click LatLng</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvRPR8W93pV4cHO6iEabc61OgS3-JPscY&callback=initMap&libraries=&v=weekly"
        defer
    ></script>
    <style type="text/css">
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }

        /*!* Optional: Makes the sample page fill the window. *!*/
        /*html,*/
        /*body {*/
        /*    height: 100%;*/
        /*    margin: 0;*/
        /*    padding: 0;*/
        /*}*/
    </style>
    <script>

        function initMap() {
            const myLatlng = { lat:29.3117 , lng: 47.4818 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: myLatlng,
            });
            // Create the initial InfoWindow.
            let infoWindow = new google.maps.InfoWindow({
                content: "Click the map to get Lat/Lng!",
                position: myLatlng,
            });
            infoWindow.open(map);
            // Configure the click listener.
            map.addListener("click", (mapsMouseEvent) => {
                // Close the current InfoWindow.
                infoWindow.close();
                // Create a new InfoWindow.
                infoWindow = new google.maps.InfoWindow({
                    position: mapsMouseEvent.latLng,
                });
                infoWindow.setContent(
                    JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)

                );
                infoWindow.open(map);
                var latandlongselected = mapsMouseEvent.latLng.toJSON();
                 $('#com_long').val(latandlongselected.lng);
                 $('#com_lat').val(latandlongselected.lat);
                $('#modaldemo3').modal('toggle');

            });

        }

    </script>


@endsection
@section('content')
    <!-- row -->
    <style>
        .iti__flag-container {
            max-height: 40px !important;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Company Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="com_name"
                                           id="com_name"
                                           placeholder="Company Name" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Contact Number<span
                                            class="error">*</span></label>
                                    <input class="form-control"
                                           name="com_contact_number"
                                           id="phone"
                                           placeholder="Company Contact Number" value="" required type="text" maxlength="10" onkeyup="getCode()" />
                                    <input type="hidden" name="country_code" id="country_code">
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Full Contact Number<span
                                            class="error">*</span></label>
                                    <input class="form-control"
                                           name="com_full_contact_number"
                                           id="com_full_contact_number"
                                           placeholder="Company Full Contact Number" value="" required type="text" readonly="" />
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company License Number<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="com_license_no"
                                           id="com_license_no"
                                           placeholder="Company License Number" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="com_service_type">Company Service Type<span
                                            class="error">*</span></label>

                                    <select class="form-control" id="com_service_type" name="com_service_type"
                                            required>
                                        <option value="">Select Company Services Type</option>
                                        <option value="Ride hailing">Ride hailing</option>
                                        <option value="Taxi">Taxi</option>
                                        <option value="Executive cars">Executive cars</option>
                                        <option value="Airport Shuttle">Airport Shuttle</option>
                                        <option value="Bikes or Motorbikes">Bikes or Motorbikes</option>
                                        <option value="Delivery">Delivery</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <div class="help-block with-errors error">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Time Zone</label>
{{--                                    <input type="text" class="form-control"--}}
{{--                                           name="com_time_zone"--}}
{{--                                           id="com_time_zone"--}}
{{--                                           placeholder="Company Time Zone" value=""/>--}}
                                    <select name="com_time_zone" id="com_time_zone" class="form-control select2" required="">
                                        <option value="">Select Timezone</option>
                                            {{ \App\Utility\Utility::create_option("time_zones","id","time_zone") }}
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Radius</label>
                                    <input type="text" class="form-control"
                                           name="com_radius"
                                           id="com_radius"
                                           placeholder="Company Radius" value=""/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12" id="Locations">
                                <div class="form-group">
                                    <label for="screen_info">Add Location<span
                                            class="error">*</span></label>
                                    <a type="button"  class="btn btn-sm btn-outline-success waves-effect waves-light"  data-placement="top" title="User Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fa fa-map-marker font-size-16 align-middle"></i></a>
                                    <input type="hidden"  class="form-control"
                                           name="Location"
                                           id="Location_input"
                                           value="1"
                                           placeholder="Location" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Latitude</label>
                                    <input type="text" class="form-control"
                                           name="com_lat"
                                           id="com_lat"
                                           placeholder="Company Latitude" readonly/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Longitude</label>
                                    <input type="text" class="form-control"
                                           name="com_long"
                                           id="com_long"
                                           placeholder="Company Longitude" readonly/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company User Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="com_user_name"
                                           id="com_user_name"
                                           placeholder="Company User Name" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Email<span
                                            class="error">*</span></label>
                                    <input type="email" class="form-control"
                                           name="email"
                                           id="email"
                                           placeholder="Company Email" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">Password<span
                                            class="error">*</span></label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           id="password"
                                           placeholder="Company Password" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="com_logo">Company Logo<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="com_logo"
                                           id="com_logo"
                                           placeholder="Company Logo" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('admin/company') }}"
                                           class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->

    <!-- main-content closed -->
    <div class="modal" id="modaldemo3">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">View Locations</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="map" style="height: 600px">
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/company.js')}}?v={{ time() }}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/telephoneinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/inttelephoneinput.js')}}"></script>
    <script>

        function getCode() {
            var country_code_string = $(".iti__active").text();
            countrycode = country_code_string.split("+");
            console.log(countrycode[1]);
            $("#country_code").val('+' + countrycode[1]);
            var new_countrycode = "+"+countrycode[1];
            $('#com_full_contact_number').val(new_countrycode+$("#phone").val());
        }




        $('#phone').bind('keyup paste', function(){
            this.value = this.value.replace(/[^0-9]/g, '');
            $("#com_full_contact_number").replace(/[^0-9]/g, '');
        });
    </script>
@endsection
