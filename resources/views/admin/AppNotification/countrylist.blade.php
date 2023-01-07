<div class="form-group">
    <label for="app_or_panel">Country<span class="error">*</span></label>
    <select class="form-control" id="country_id" name="country_id" required="">

        <option value="">Select One</option>
        @foreach($countries as $row)
            <option value="{{$row->code}}">{{$row->name}}</option>
        @endforeach
    </select>
</div>
