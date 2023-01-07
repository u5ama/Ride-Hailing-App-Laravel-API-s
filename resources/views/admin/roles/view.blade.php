<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @if(isset($role))
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>

                                <th>Role Name</th>
                                <th>Note</th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>{{ $role->role_name }}</td>
                                <td>{{ $role->note }}</td>

                            </tr>

                            </tbody>
                        </table>
                    @else
                        <h5>Role Detail Not Found!</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>
