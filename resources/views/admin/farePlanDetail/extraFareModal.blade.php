@if(isset($extraFareCharges) && count($extraFareCharges) > 0)
    @foreach($extraFareCharges as $extraFare)
        <tr>
            <input type="hidden" class="form-control" name="fareExtraId[]" id="fareExtraId" value="{{$extraFare->id}}">
            <td><input type="text" class="form-control" name="efc_key[]" id="efc_key" value="{{$extraFare->efc_key}}"
                       required=""></td>
            <td><input type="text" class="form-control" name="efc_info[]" id="efc_info" value="{{$extraFare->efc_info}}"
                       required=""></td>
            <td><input type="text" class="form-control" name="efc_charge[]" id="efc_charge"
                       value="{{$extraFare->efc_charge}}" required=""></td>
            <td>
                <a class="delete_extra" onclick="deleteExtraFareCharge('{{$extraFare->id}}')" data-toggle="tooltip"><i
                        class="material-icons">&#xE872;</i></a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <input type="hidden" class="form-control" name="fareExtraId[]" id="fareExtraId" value="0">
        <td><input type="text" class="form-control" name="efc_key[]" id="efc_key" required=""></td>
        <td><input type="text" class="form-control" name="efc_info[]" id="efc_info" required=""></td>
        <td><input type="text" class="form-control" name="efc_charge[]" id="efc_charge" required=""></td>
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

