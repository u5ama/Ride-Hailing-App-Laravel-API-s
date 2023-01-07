<div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="border-bottom mb-3">
                        <h6 class="card-title">Ryde</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Model Image</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Year</th>
                                <th>Body</th>
                                <th>Engine</th>
                                <th>Door</th>
                                <th>Fuel</th>
                                <th>Gearbox</th>
                                <th>Seats</th>
                            </tr>
                            </thead>
                            <tbody>
                            
                                <tr>
                                    <td><input type="hidden" name="ryde_id" id="ryde_id" value="{{$id}}">{{ $id }}</td>
                                    <td><img src="{{asset($model_image)}}" width=100px height=100px> </td>
                                    <td>{{ $brand }}</td>
                                    <td>{{ $model }}</td>
                                    <td>{{ $year }}</td>
                                    <td>{{ $body }}</td>
                                    <td>{{ $engine }}</td>
                                    <td>{{ $door }}</td>
                                    <td>{{ $fuel }}</td>
                                    <td>{{ $gearbox }}</td>
                                    <td>{{ $seats }}</td>
                                    
                                </tr>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>