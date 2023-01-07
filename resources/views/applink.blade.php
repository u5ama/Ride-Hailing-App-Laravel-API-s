
@extends('admin.layouts.master2')
@section('css')

@endsection
@section('content')

    <input type="hidden" name="url" id="url" value="{{$url}}">
@endsection
@section('js')

    <script src = "{{URL::asset('assets/js/jquery-ui.js')}}"></script>
    <script type="text/javascript">

        var ulr1 = $("#url").val();
        // Your application has indicated there's an error
        window.setTimeout(function(){

            // Move to a new location or you can do something else
            window.location.href = ulr1;

        }, 3000);


    </script>
@endsection



