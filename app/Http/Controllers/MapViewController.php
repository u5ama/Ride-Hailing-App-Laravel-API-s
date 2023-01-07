<?php

namespace App\Http\Controllers;

use App\RideBookingSchedule;
use Illuminate\Http\Request;
use App\Utility\Utility;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;

class MapViewController extends Controller
{
    public function viewMap($id)
    {
//        $hash = new Hashids();
//        $id = $hash->decode($id);

        $ride = RideBookingSchedule::with('driver')->where('id', $id)->first();
        $DriverdisAndtime = Utility::timeAndDistance($ride->rbs_source_lat,$ride->rbs_source_long,$ride->rbs_driver_lat,$ride->rbs_driver_long);
        $DestinationdisAndtime = Utility::timeAndDistance($ride->rbs_source_lat,$ride->rbs_source_long,$ride->rbs_driver_lat,$ride->rbs_driver_long);
        $disDri = $DriverdisAndtime->routes[0]->legs[0]->distance->value;
        $durDri = $DriverdisAndtime->routes[0]->legs[0]->duration->value;
        $disDes = $DestinationdisAndtime->routes[0]->legs[0]->distance->value;
        $durDes = $DestinationdisAndtime->routes[0]->legs[0]->duration->value;
        $ride['driver_duration'] = number_format((float)$durDri/60, 2, '.', '') . " Minutes";
        $ride['driver_distance'] = number_format((float)$disDri/1000, 2, '.', ''). " KM";
        $ride['destination_distance'] = number_format((float)$disDes/1000, 2, '.', ''). " KM";
        $ride['destination_duration'] = number_format((float)$durDes/60, 2, '.', ''). " Minutes";
        return view('viewmap',['id'=>$id, 'ride' => $ride]);
    }
}
