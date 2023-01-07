@if(isset($farePlanDetail) && count($farePlanDetail) > 0)
    @foreach($farePlanDetail as $farPlnDetail)
        <tr id="deletePlanDetail_{{$farPlnDetail->id}}">
            <input type="hidden" name="farePlanDetailId[]" id="farePlanDetailId" value="{{$farPlnDetail->id}}">
            <td><input type="text" class="form-control" name="fpd_base_fare[]" id="fpd_base_fare"
                       value="{{$farPlnDetail->fpd_base_fare}}" required></td>

            <td><input type="text" class="form-control" name="fpd_cancel_charge[]" id="fpd_cancel_charge"
                       value="{{$farPlnDetail->fpd_cancel_charge}}" required></td>

            <td><input type="text" class="form-control" name="fpd_cancel_minute[]" id="fpd_cancel_minute"
                       value="{{$farPlnDetail->fpd_cancel_minute}}" required></td>

            <td><input type="text" class="form-control" name="fpd_per_km_fare[]" id="fpd_per_km_fare"
                       value="{{$farPlnDetail->fpd_per_km_fare}}" required></td>

            <td><input type="text" class="form-control" name="fpd_per_minute_fare[]" id="fpd_per_minute_fare"
                       value="{{$farPlnDetail->fpd_per_minute_fare}}" required></td>

            <td><input type="text" class="form-control" name="fpd_per_km_fare_before_pickup[]"
                       id="fpd_per_km_fare_before_pickup" value="{{$farPlnDetail->fpd_per_km_fare_before_pickup}}" required></td>


            <td><input type="text" class="form-control" name="fpd_per_minutes_fare_before_pickup[]"
                       id="fpd_per_minutes_fare_before_pickup"
                       value="{{$farPlnDetail->fpd_per_minutes_fare_before_pickup}}" required></td>

            <td><input type="text" class="form-control" name="fpd_wait_cost_per_minute_fare[]"
                       id="fpd_wait_cost_per_minute_fare" value="{{$farPlnDetail->fpd_wait_cost_per_minute_fare}}" required></td>

            <td><input type="text" class="form-control" name="fpd_estimate_percentage[]" id="fpd_estimate_percentage"
                       value="{{$farPlnDetail->fpd_estimate_percentage}}" required></td>


            <td><input type="text" class="form-control  clockpicker" name="fpd_start_time[]" id="fpd_start_time"
                       value="{{$farPlnDetail->fpd_start_time}}" autocomplete="off" required></td>

            <td><input type="text" class="form-control  clockpicker" name="fpd_end_time[]" id="fpd_end_time"
                       value="{{$farPlnDetail->fpd_end_time}}" autocomplete="off" required></td>
            <td>

                <button type="button" class="btn btn-info" data-target="#select2modal" data-toggle="modal"
                        onclick="extraFareCharege('{{$farPlnDetail->id}}')"><i class="fa fa-eye"></i> Extra Charges
                </button>
            </td>
            <td>
                <a class="delete_detail" onclick="deleteRecord('{{$farPlnDetail->id}}')" data-toggle="tooltip"
                   title="Delete"><i class="material-icons">&#xE872;</i></a>
            </td>


        </tr>
    @endforeach
@else
    <tr>
        <input type="hidden" name="farePlanDetailId[]" id="farePlanDetailId" value="0">
        <td><input type="text" class="form-control" name="fpd_base_fare[]" id="fpd_base_fare" required></td>
        <td><input type="text" class="form-control" name="fpd_cancel_charge[]" id="fpd_cancel_charge" required></td>
        <td><input type="text" class="form-control" name="fpd_cancel_minute[]" id="fpd_cancel_minute" required></td>

        <td><input type="text" class="form-control" name="fpd_per_km_fare[]" id="fpd_per_km_fare" required></td>
        <td><input type="text" class="form-control" name="fpd_per_minute_fare[]" id="fpd_per_minute_fare" required></td>

        <td><input type="text" class="form-control" name="fpd_per_km_fare_before_pickup[]"
                   id="fpd_per_km_fare_before_pickup" required></td>

        <td><input type="text" class="form-control" name="fpd_per_minutes_fare_before_pickup[]"
                   id="fpd_per_minutes_fare_before_pickup" required></td>

        <td><input type="text" class="form-control" name="fpd_wait_cost_per_minute_fare[]"
                   id="fpd_wait_cost_per_minute_fare" required></td>

        <td><input type="text" class="form-control" name="fpd_estimate_percentage[]" id="fpd_estimate_percentage" required></td>

        <td><input type="text" class="form-control  clockpicker" name="fpd_start_time[]" id="fpd_start_time"
                   autocomplete="off" required></td>
        <td><input type="text" class="form-control  clockpicker" name="fpd_end_time[]" id="fpd_end_time"
                   autocomplete="off" required></td>

        <td></td>

        <td>
            <a class="delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a>
        </td>
    </tr>
@endif
<style type="text/css">
    table.table td a.add {
        color: #27C46B;
    }
</style>
<script>
    $('.clockpicker').clockpicker({
        placement: 'top',
        align: 'left',
        donetext: 'Done',
        autoclose: true
    })
</script>

