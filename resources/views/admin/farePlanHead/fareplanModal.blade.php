<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tbls">
                        <thead>
                        <tr>
                            <th>Transport Type</th>
                            <th>Base.Fare</th>
                            <th>Base Cancel.Charges <a href="#" data-toggle="tooltip" data-placement="right"
                                                  title="Its cancel base fare charges"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Cancel.Charges Per Minute <a href="#" data-toggle="tooltip" data-placement="right"
                                                  title="Customer or Driver Cancel Charges (Based on Control add Minutes in Control if driver or passenger after that lets say 3 minutes cancel the ride after accepted he will be charged with this rate)!"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Per KM Charges <a href="#" data-toggle="tooltip" data-placement="right"
                                                  title="Per KM Fare (Drive Time Fare) This will x with the KM Driver Drive the car!"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Per Minute Charges <a href="#" data-toggle="tooltip" data-placement="right"
                                                      title="Per Minute Fare (Drive Time Fare)th the Minutes Driver Drive the car !"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Per KM Charges Before Pickup <a href="#" data-toggle="tooltip" data-placement="right"
                                                                title="Per KM Fare Before Pickup (Add the KM in Control lets say Value is 5 then the distance driver drive before pickup added to customer total!"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Per Minute Charges Before Pickup <a href="#" data-toggle="tooltip"
                                                                    data-placement="right"
                                                                    title="Per Minute Fare Before Pickup (Add the Minute in Control lets say Value is 5 then the time driver drive before pickup added to customer total!"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Initial Waiting Minutes <a href="#" data-toggle="tooltip" data-placement="right"
                                                           title="When Driver Arrived at Customer Location the wait time start after control app time added till he click on Start Ride (The Difference Between Arrive and before start Ride is wait Time)!"><i
                                        class="fa fa-question-circle-o" aria-hidden="true" style="font-size: 16px;"></i></a>
                            </th>
                            <th>Estimate.percentage</th>
                            <th>start.time</th>
                            <th>end.time</th>
                            <th>Extra.Fare.Key</th>
                            <th>Extra.Fare.Info</th>
                            <th>Extra.Fare.Charge</th>
                        </tr>
                        </thead>
                        <tbody id="fareDetail">
{{--                        {{dd($detailsPlan)}}--}}
                        @if(isset($detailsPlan) && count($detailsPlan) > 0)
                            @foreach($detailsPlan as $detail)
                                <tr>
                                    <td>{{$detail->transportType['name']}}</td>
                                    <td>{{$detail->fpd_base_fare}}</td>
                                    <td>{{$detail->fpd_cancel_charge}}</td>
                                    <td>{{$detail->fpd_cancel_minute}}</td>
                                    <td>{{$detail->fpd_per_km_fare}}</td>
                                    <td>{{$detail->fpd_per_minute_fare}}</td>
                                    <td>{{$detail->fpd_per_km_fare_before_pickup}}</td>
                                    <td>{{$detail->fpd_per_minutes_fare_before_pickup}}</td>
                                    <td>{{$detail->fpd_wait_cost_per_minute_fare}}</td>
                                    <td>{{$detail->fpd_estimate_percentage}}</td>
                                    <td>{{$detail->fpd_start_time}}</td>
                                    <td>{{$detail->fpd_end_time}}</td>
                                    @if($detail->extrafare)
                                        <td>{{$detail->extrafare->efc_key}}</td>
                                        <td>{{$detail->extrafare->efc_info}}</td>
                                        <td>{{$detail->extrafare->efc_charge}}</td>
                                    @else
                                        <td colspan="3">No Extra Charges</td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr style="text-align:center">
                                <td colspan="12">No Record Found!</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>
