<div class="row">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-4">Total Ride {{$status}}: <b>{{$totalCount}}</b></div>


        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h4>User Detail</h4>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Country Code</th>
                            <th>Contact#</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->country_code }}</td>
                            <td>{{ $user->mobile_no }}</td>
                        </tr>
                        </tbody>
                    </table>


                    @if(isset($rideBookSchedule))
                        <h4>Ride Details: {{$status}}</h4>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th>Driver</th>
                                <th>Passenger</th>
                                <th>Transport Type</th>
                                <th>Driving Start Time</th>
                                <th>Driving End Time</th>
                                <th>Totla Driving Time</th>
                                <th>Waite Start Time</th>
                                <th>Waite End Time</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($rideBookSchedule as $rideBookSchedul)
                                <tr>
                                    <td>
                                    @if(isset($rideBookSchedul->driver->du_full_name)) 
                                    {{ $rideBookSchedul->driver->du_full_name }} 
                                  @endif
                                  </td>
                                    <td>{{ $rideBookSchedul->passenger->name }}</td>
                                    <td>{{ $rideBookSchedul->rbs_transport_type }}</td>
                                    <td>{{ Utility:: convertTimeToUSERzone($rideBookSchedul->rbs_driving_start_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}</td>
                                    
                                    <td>{{ Utility:: convertTimeToUSERzone($rideBookSchedul->rbs_driving_end_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}</td>
                                    <td>
                                        @php
                                            $to_time = strtotime($rideBookSchedul->rbs_driving_start_time);
                                            $from_time = strtotime($rideBookSchedul->rbs_driving_end_time);

                                            $drivnig_total_time = round(abs($to_time - $from_time) / 60). " minute";
                                        @endphp
                                        {{ $drivnig_total_time }}
                                    </td>
                                    <td>
                                        {{ Utility:: convertTimeToUSERzone( $rideBookSchedul->rbs_driving_wait_start_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}
                                    </td>
                                    <td>{{ Utility:: convertTimeToUSERzone( $rideBookSchedul->rbs_driving_wait_end_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5>Ride Detail Not Found!</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>
