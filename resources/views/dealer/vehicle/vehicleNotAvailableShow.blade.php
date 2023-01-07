<div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="border-bottom mb-3">
                        <h6 class="card-title">Vehicled Not Availability</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>Company</th>
                                <th>Company Address</th>
                                <th>Color</th>
                                <th>Start Date</th>
                                <th>End date</th>
                            </tr>
                            </thead>
                            <tbody>
                           
                            @foreach($vehicleNotAvailability as $vehicleNotAvailable)
                                <tr>
                                    <td>{{$vehicleNotAvailable->vehicle->companies->name}}</td>
                                    <td>{{$vehicleNotAvailable->vehicle->companyAddress->address}}</td>
                                    <td>{{$vehicleNotAvailable->vehicle->color->name}}</td>
                                    <td>{{$vehicleNotAvailable->start_date}}</td>
                                    <td>{{$vehicleNotAvailable->end_date}}</td>
                                </tr>
                                @endforeach
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>