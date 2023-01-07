<html>
  <head>
      <meta charset="UTF-8">
      <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta http-equiv="X-UA-Compatible" content="IE=9"/>

      @include('admin.layouts.head')
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvRPR8W93pV4cHO6iEabc61OgS3-JPscY&callback=initMap"
      defer
    ></script>

    <!-- jsFiddle will insert css and js -->
    <style type="text/css">
        #map {
          height: 100%;
        }
        html,
        body {
          height: 100%;
          margin: 0;
          padding: 0;
        }
        .mt-5, .my-5 {
            margin-top: 5rem !important;
        }
    </style>
  </head>
  <body>
  <div class="container">
      <div class="row text-center justify-content-center" style="margin: 5%;">
          <h4>Ride Tracking</h4>
      </div>
      @if(!empty($ride))
      <div class="row">
          <div class="col-md-6">
              <div class="card shadow p-1 mb-2 bg-white rounded" style="display: inline-block; width: 100%">
                  <div class="card-body">
                      <div class="row border-top border-bottom border-left border-right p-2">
                          <div class="col-md-12 col-xl-12 col-sm-12 col-12">
                              <h5 class="border-bottom">Driver Details</h5>
                              <div class="row">
                                  <div class="col-md-3 col-4">
                                      @if(!empty($ride->driver->du_profile_pic))
                                          <img src="{{ url($ride->driver->du_profile_pic) }}" alt="" style="height: 100px;">
                                      @endif
                                  </div>
                                  <div class="col-md-9 col-8">
                                      <p class="border-bottom border-left border-right"><b>Driver Name: </b> {{$ride->driver->du_full_name}} </p>
                                      <p class="border-bottom border-left border-right"><b>Driver Contact: </b> {{$ride->driver->du_full_mobile_number}}</p>
                                      <p class="border-bottom border-left border-right"><b>Ride Estimated Time # </b> {{$ride->destination_duration}}</p>
                                      <p class="border-bottom border-left border-right"><b>Ride Estimated Distance # </b> {{$ride->destination_distance}}</p>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-md-6">
              <div class="card shadow p-1 mb-2 bg-white rounded" style="display: inline-block; width: 100%">
                  <div class="card-body">
                      <div class="row border-top border-bottom border-left border-right p-2">
                          <div class="col-md-12 col-sm-12 col-12">
                              <h5 class="border-bottom ">Ride Details</h5>
                              <p class="border-bottom border-left border-right"><b>Ride ID # </b> {{$ride->rbs_Trx_id}} </p>
                              <p class="border-bottom border-left border-right"><b>Ride Status # </b> {{$ride->rbs_ride_status}} </p>
                              @if($ride->rbs_payment_method == 'cash')
                                    <p class="border-bottom border-left border-right"><b>Payment # </b> Cash</p>
                              @elseif($ride->rbs_payment_method == 'wallet')
                                    <p class="border-bottom border-left border-right"><b>Payment # </b> Wallet</p>
                              @endif
                              <p class="border-bottom border-left border-right"><b>Estimated Amount # </b> {{$ride->rbs_estimated_cost}} KWD</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
        @endif
      <div id="map" style="height: 100%;"></div>
      <input type="hidden" id="ride_id" name="ride_id" value="{{$id}}">

  </div>

    <!-- JQuery min js -->
    @include('admin.layouts.footer')
    @include('admin.layouts.footer-scripts')
    <script src="{{URL::asset('assets/js/custom/mapView.js')}}"></script>
  </body>
</html>
