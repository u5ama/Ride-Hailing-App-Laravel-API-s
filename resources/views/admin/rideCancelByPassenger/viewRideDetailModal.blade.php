<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @if(isset($rideBookSchedule))
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th>Driver</th>
                                <th>Passenger</th>
                                <th>Transport Type</th>
                                <th>Driving Start Time</th>
                                <th>Driving End Time</th>
                                <th>Total Driving Time</th>
                                <th>Wait Start Time</th>
                                <th>Wait End Time</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>{{ $rideBookSchedule->driver->du_full_name }}</td>
                                <td>{{ $rideBookSchedule->passenger->name }}</td>
                                <td>{{ $rideBookSchedule->rbs_transport_type }}</td>
                                
                                

                                <td>{{ Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_start_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}</td>
                                    
                                <td>{{ Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_end_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}</td>



                                <td>
                                    @php
                                        $to_time = strtotime($rideBookSchedule->rbs_driving_start_time);
                                        $from_time = strtotime($rideBookSchedule->rbs_driving_end_time);
                                        $drivnig_total_time = round(abs($to_time - $from_time) / 60). " minute";
                                    @endphp
                                    {{ $drivnig_total_time }}
                                </td>
                                
                                 <td>
                                    {{ Utility:: convertTimeToUSERzone( $rideBookSchedule->rbs_driving_wait_start_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}
                                 </td>
                                <td>
                                    {{ Utility:: convertTimeToUSERzone( $rideBookSchedule->rbs_driving_wait_end_time,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}
                                </td>


                                <td>{{ $rideBookSchedule->rbs_ride_status }}</td>
                            </tr>

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
