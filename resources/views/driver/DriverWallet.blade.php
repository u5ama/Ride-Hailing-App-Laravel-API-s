@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        <div class="row mb-1 justify-content-center text-center" style="padding: 15px;">
            <div class="col-md-12 col-sm-12">
                <h5><b>Your Current Balance: {{$driverBalance}}</b></h5>
            </div>
        </div>
        <h5>Today Rides</h5>
        <div class="row mb-2">

            @if(count($crrDateRides)>0)
                @foreach($crrDateRides as $ride)
                    <div class="card shadow p-1 mb-2 bg-white rounded" style="display: inline-block; width: 100%">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-6">
                                    <p><b>Pickup # {{$ride->ride_location['from']}}</b></p>
                                    <p><b>Dropoff # {{$ride->ride_location['to']}}</b></p>
                                </div>
                                <div class="col-md-6 col-sm-6 text-right col-6">
                                    <span><img src="{{asset($ride->payment_image)}}" alt=""
                                               style="width: 35px"></span><span><b>Amount : {{$ride->driver_income}}</b> </span>
                                    <p class="pt-3"><b>Date : {{$ride->date}}</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="pl-5">No New Rides Available!</p>
            @endif
        </div>

        <h5>Recent Rides</h5>
        <div class="row mb-2">
            @if(count($lastDateRides)>0)
                @foreach($lastDateRides as $lastDateRide)
                    <div class="card shadow p-1 mb-2 bg-white rounded" style="display: inline-block; width: 100%">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-6">
                                    <p><b>Pickup # {{$lastDateRide->ride_location['from']}}</b></p>
                                    <p><b>Dropoff # {{$lastDateRide->ride_location['to']}}</b></p>
                                </div>
                                <div class="col-md-6 col-sm-6 text-right col-6">
                                    <span><img src="{{asset($lastDateRide->payment_image)}}" alt="" style="width: 35px"></span><span><b>Amount : {{$lastDateRide->driver_income}}</b> </span>
                                    <p class="pt-3"><b>Date : {{$lastDateRide->date}}</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="pl-5">No Rides Available!</p>
            @endif
        </div>

        <div class="row text-center justify-content-center">
            <span style="font-size: 10px">Contact Us to get all transactions!</span>
        </div>
    </div>
@endsection
