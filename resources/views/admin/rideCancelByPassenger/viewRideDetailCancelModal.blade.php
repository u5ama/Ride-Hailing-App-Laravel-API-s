<div class="row">
    <div class="col-xl-12">
        <div class="row">
            @if(isset($rideCancelTotalCount))
                <div class="col-md-4">Total Cancel: <b>{{$rideCancelTotalCount}}</b></div>
            @else
                <div class="col-md-4">Total Cancel: <b>0</b></div>
            @endif

            @if(isset($rideCancelTotalWeekCount))
                @if($rideCancelTotalWeekCount > 7)
                    <div style="color: white; background: red;" class="col-md-4">Total Cancel in Week:
                        <b>{{$rideCancelTotalWeekCount}}</b></div>
                @elseif($rideCancelTotalWeekCount > 4)
                    <div style="color: white; background: yellow;" class="col-md-4">Total Cancel in Week:
                        <b>{{$rideCancelTotalWeekCount}}</b></div>
                @else
                    <div style="color: white; background: green;" class="col-md-4">Total Cancel in Week:
                        <b>{{$rideCancelTotalWeekCount}}</b></div>
                @endif
            @else
                <div style="color: white; background: green;" class="col-md-4">Total Cancel in Week: <b>0</b></div>
            @endif

            @if(isset($rideCancelTotalTodayCount))
                @if($rideCancelTotalTodayCount > 7)
                    <div style="color: white; background: red;" class="col-md-4">Total Cancel Today:
                        <b>{{$rideCancelTotalTodayCount}}</b></div>
                @elseif($rideCancelTotalTodayCount > 4)
                    <div style="color: white; background: yellow;" class="col-md-4">Total Cancel Today:
                        <b>{{$rideCancelTotalTodayCount}}</b></div>
                @else
                    <div style="color: white; background: green;" class="col-md-4">Total Cancel Today:
                        <b>{{$rideCancelTotalTodayCount}}</b></div>
                @endif
            @else
                <div style="color: white; background: green;" class="col-md-4">Total Cancel in Week: <b>0</b></div>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @if(isset($rideCancelDetails))

                        <table class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th>Driver</th>
                                <th>Passenger</th>
                                <th>Reason</th>
                                <th>Comments</th>
                                <th>Cancel AT</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($rideCancelDetails as $rideCancelDetail)
                                <tr>
                                    <td>
                                    @if(isset( $rideCancelDetail->du_full_name))
                                        {{ $rideCancelDetail->driver->du_full_name }}
                                    @endif
                                    </td>
                                    <td>
                                    @if(isset( $rideCancelDetail->passenger))
                                        {{ $rideCancelDetail->passenger->name }}
                                    @endif
                                    </td>
                                    <td>
                                    @if(isset($rideCancelDetail->reasonReference))
                                        {{ $rideCancelDetail->reasonReference->name }}
                                    @endif
                                    </td>

                                    <td>{{ $rideCancelDetail->pcrh_comments }}</td>


                                    <td>
                                        {{ Utility:: convertTimeToUSERzone( $rideCancelDetail->pcrh_created_at,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id))  }}
                                    </td>
                                    
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5>Ride Cancel Detail Not Found!</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>
