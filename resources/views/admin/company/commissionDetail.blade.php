
@if(isset($commission) && count($commission) > 0)
    @foreach($commission as $comm)
        <tr id="deletePlanDetail_{{$comm->id}}">
            <input type="hidden" name="companyCommissionId[]" id="companyCommissionId" value="{{$comm->id}}">
            <td><input type="text" class="form-control" name="whipp_commission[]" id="whipp_commission"
                       value="{{$comm->whipp_commission}}" required></td>

            <td><input type="text" class="form-control" name="company_commission[]" id="company_commission"
                       value="{{$comm->company_commission}}" required></td>

            <td><input type="text" class="form-control" name="driver_commission[]" id="driver_commission"
                       value="{{$comm->driver_commission}}" required></td>

            <td><input type="date" class="form-control" name="start_date[]" id="start_date"
                       value="{{$comm->start_date}}" required></td>

            <td><input type="date" class="form-control" name="end_date[]" id="end_date"
                       value="{{$comm->end_date}}" required></td>

            <td>
                @if ($comm->commission_status == 1)
                    <a type="button" onclick="updateCommissionStatus('{{$comm->id}}',1)" class="badge badge-success" data-toggle="tooltip" data-placement="top" title="Inactive">Active </a>
                @endif
                @if ($comm->commission_status == 0)
                    <a type="button" onclick="updateCommissionStatus('{{$comm->id}}',0)" class="badge badge-warning" data-toggle="tooltip" data-placement="top" title="Active"> Inactive </a>
                @endif
            </td>

            <td>
                <a class="delete_detail" onclick="deleteRecordCommission('{{$comm->id}}')" data-toggle="tooltip"
                   title="Delete"><i class="material-icons">&#xE872;</i></a>
            </td>

        </tr>
    @endforeach
@else
    <tr>
        <input type="hidden" name="companyCommissionId[]" id="companyCommissionId" value="0">
        <td><input type="text" class="form-control" name="whipp_commission[]" id="whipp_commission" required></td>
        <td><input type="text" class="form-control" name="company_commission[]" id="company_commission" required></td>
        <td><input type="text" class="form-control" name="driver_commission[]" id="driver_commission" required></td>

        <td><input type="date" class="form-control" name="start_date[]" id="start_date" required></td>
        <td><input type="date" class="form-control" name="end_date[]" id="end_date" required></td>

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
