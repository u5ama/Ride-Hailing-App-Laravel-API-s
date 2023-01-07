@extends('admin.layouts.master')

@section('content')
    <div class="card  mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h4 class="panel-heading">{{ 'Edit Permission Role' }}</h4>

                        <div class="panel-body">
                            <form method="post" class="ajax-submit" autocomplete="off"
                                  action="{{route('admin::roles.update', $id)}}" enctype="multipart/form-data">
                                {{ csrf_field()}}
                                <input name="_method" type="hidden" value="PATCH">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{'Role Name' }}</label>
                                        <input type="text" class="form-control" name="role_name"
                                               value="{{ $role->role_name }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ 'Note' }}</label>
                                        <textarea class="form-control" name="note">{{ $role->note }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
