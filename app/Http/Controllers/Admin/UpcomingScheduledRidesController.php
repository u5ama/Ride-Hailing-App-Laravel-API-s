<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppSocialLinks;
use App\Company;
use App\CustomerInvoice;
use App\Driver;
use App\PassengerContactList;
use App\RideBookingSchedule;
use App\TransactionId;
use App\TransportType;
use App\UpcomingScheduleRides;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Utility\Utility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class UpcomingScheduledRidesController extends Controller
{
    /**
     * Display a listing of the RideBookingSchedule.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        $categories = $categories = TransportType::listsTranslations('name')
            ->select('transport_types.id')
            ->get();

        if ($request->ajax()) {
            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
            $category = (!empty($_GET["categoryFilter"])) ? ($_GET["categoryFilter"]) : ('');

            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $rideBookSchedule = UpcomingScheduleRides::query()->whereRaw("date(upcoming_schedule_rides.usr_created_at) >= '" . $start_date . "' AND date(upcoming_schedule_rides.usr_created_at) <= '" . $end_date . "'")->with('driver', 'passenger')->get();
            }elseif ($category) {
                $currentDate = date('Y-m-d');
                $rideBookSchedule = UpcomingScheduleRides::with('passenger', 'driver')->whereDate("upcoming_schedule_rides.usr_created_at", $currentDate)->where('usr_transport_type', $category)->orderBy('upcoming_schedule_rides.usr_created_at', 'DESC')->get();
            } else {
                $currentDate = date('Y-m-d');
                $rideBookSchedule = UpcomingScheduleRides::whereDate("upcoming_schedule_rides.usr_created_at", $currentDate)->with('driver', 'passenger')->get();
            }

            return Datatables::of($rideBookSchedule)
                ->addColumn('date_time', function ($rideBookSchedule) {
                    return $rideBookSchedule->usr_created_at;
                })
                ->addColumn('transaction_id', function ($rideBookSchedule) {
                        if (!empty($rideBookSchedule->usr_Trx_id)){
                            return $rideBookSchedule->usr_Trx_id;
                        }
                })
                ->addColumn('schedule_ride_on', function ($rideBookSchedule) {
                    return $rideBookSchedule->usr_schedule_start_date.' '.$rideBookSchedule->usr_schedule_start_time;
                })
                ->addColumn('ride_status', function ($rideBookSchedule) {
                    return $rideBookSchedule->usr_ride_status;
                })
                ->addColumn('payment_mode', function ($rideBookSchedule) {
                    return $rideBookSchedule->usr_payment_method;
                })
                ->addColumn('category', function ($rideBookSchedule) {
                    return $rideBookSchedule->usr_transport_type;
                })
                ->addColumn('schedule_for', function ($rideBookSchedule) {
                    if (!empty($rideBookSchedule->usr_contact_id)){
                        $pass = PassengerContactList::where('id',$rideBookSchedule->usr_contact_id)->first();
                        return $pass->pcl_contact_name.' '.$pass->pcl_contact_number;
                    }else{
                        $self = 'Self';
                        return $self;
                    }
                })
//                ->addColumn('tracking_url', function ($rideBookSchedule) {
//                    if (!empty($rideBookSchedule->passenger)){
//                        return $rideBookSchedule->passenger->name;
//                    }
//                })
                ->addColumn('pickup_location', function ($rideBookSchedule) {
                    if (!empty($rideBookSchedule)) {
                        $sourceLat = floatval($rideBookSchedule->usr_source_lat);
                        $sourceLong = floatval($rideBookSchedule->usr_source_long);
                        $pickup_location = app('geocoder')->reverse($sourceLat, $sourceLong)->get()->first();
                        $address = $pickup_location->getFormattedAddress();
                        return $address;
                    }
                })
                ->addColumn('dropoff_location', function ($rideBookSchedule) {
                    if (!empty($rideBookSchedule)) {
                        $desLat = floatval($rideBookSchedule->usr_destination_lat);
                        $desLong = floatval($rideBookSchedule->usr_destination_long);
                        $drop_off = app('geocoder')->reverse($desLat, $desLong)->get()->first();
                        $address = $drop_off->getFormattedAddress();
                        return $address;
                    }

                })
                ->addColumn('distance_and_time', function ($rideBookSchedule) {
                    $latitude = $rideBookSchedule->usr_source_lat;
                    $longitude =  $rideBookSchedule->usr_source_long;
                    $selected_for_estimate_dis_time = Utility::timeAndDistance($rideBookSchedule->usr_destination_lat, $rideBookSchedule->usr_destination_long, $latitude, $longitude);
                    $selected_for_estimate_rate['distance'] = $selected_for_estimate_dis_time->routes[0]->legs[0]->distance->value/1000;
                    $selected_for_estimate_rate['time'] = $selected_for_estimate_dis_time->routes[0]->legs[0]->duration->value/60;
                    return number_format($selected_for_estimate_rate['distance'], 3, ".", ","). 'Km'.' '. number_format($selected_for_estimate_rate['time'], 3, ".", ","). 'mins';
                })
                ->addColumn('passenger_detail', function ($rideBookSchedule) {
                    if (!empty($rideBookSchedule->passenger)) {
                        $passenger = '<b>Customer Id:</b><span>' . $rideBookSchedule->passenger->id . '</span><br>' . '<b> Mobile#</b><span>' . $rideBookSchedule->passenger->mobile_no . '</span><br>' . '<b>Name:</b><span>' . $rideBookSchedule->passenger->name . '</span><br>';
                        return $passenger;
                    }
                })
                ->addColumn('driver_detail', function ($rideBookSchedule) {
                    if (!empty($rideBookSchedule->driver)) {
                        $driver = '<b>Vehicle#</b><span>123</span><br>' . '<b>Mobile:</b><span>' . $rideBookSchedule->driver->du_full_mobile_number . '</span><br>' . '<b>Name:</b><span>' . $rideBookSchedule->driver->du_full_name . '</span><br>' . '<b>Company:</b><span>' . $rideBookSchedule->driver->du_com_id . '</span><br>';
                        return $driver;
                    }
                })

                ->rawColumns(['date_time', 'transaction_id','schedule_ride_on','ride_status','payment_mode','category','schedule_for','tracking_url','pickup_location','dropoff_location','distance_and_time','passenger_detail','driver_detail'])
                ->make(true);
        }
        return view('admin.upcomingScheduleRides.index', compact('categories'));
    }

    /**
     * Show the form for creating a new RideBookingSchedule.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.upcomingScheduleRides.create');
    }


    /**
     * Update the specified RideBookingSchedule in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified RideBookingSchedule from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

}
