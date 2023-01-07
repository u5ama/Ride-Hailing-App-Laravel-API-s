<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-6 py-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="card-title mb-3">Passenger Detail</h6>
                <div class="row border-top border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0"> Passenger Profile Image </h6>
                        @if(!empty($passenger->profile_pic))
                            <img src="{{ url($passenger->profile_pic) }}" alt=""
                                 style="margin-top: 12px;height: 130px;">
                        @endif
                    </div>
                </div>

                <div class="row border-top border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0"> Passenger Name </h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passenger->name))
                            <h6 class="mb-0">{{ $passenger->name }}</h6>
                        @endif
                    </div>
                </div>

                <div class="row  border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Number</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passenger->mobile_no))
                            <h6 class="mb-0">{{ $passenger->mobile_no }}</h6>
                        @endif
                    </div>
                </div>

                <div class="row  border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Email</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passenger->email))
                            <h6 class="mb-0">{{ $passenger->email }}</h6>
                        @endif
                    </div>
                </div>

                <div class="row border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Full Address</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passenger->pa_address_text))
                            <h6 class="mb-0">{{ $passenger->address->pa_address_text }}</h6>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-6 py-2">
        <div class="card card h-100">
            <div class="card-body">
                <h6 class="card-title mb-3">Passenger Further Detail</h6>
                <div class="row border-top border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Total Rides</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passengerTotalCount))
                            <h6 class="mb-0">{{ $passengerTotalCount }}</h6>
                        @endif
                    </div>
                </div>
                <div class="row border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Total Cancel Rides</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passengerCancelCount))
                            <h6 class="mb-0">{{ $passengerCancelCount }}</h6>
                        @endif
                    </div>
                </div>
                <div class="row border-bottom p-2">
                    <div class="col-md-6">
                        <h6 class="mb-0">Passenger Total Topup</h6>
                    </div>
                    <div class="col-md-6">
                        @if(!empty($passengerTotalTopupCount))
                            <h6 class="mb-0">{{ $passengerTotalTopupCount}}</h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- Container closed -->
</div>

