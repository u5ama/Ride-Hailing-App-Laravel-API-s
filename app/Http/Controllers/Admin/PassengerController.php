<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\CustomerInvoice;
use App\Driver;
use App\PassengerAccount;
use App\PassengerCancelRideHistory;
use App\PassengerCurrentLocation;
use App\PassengerPaymentDetail;
use App\RideBookingSchedule;
use App\User;
use App\Utility\Utility;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PassengerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $passenger = User::with('address','PassengerRating')->get();
            return Datatables::of($passenger)
                ->addColumn('logo', function ($passenger) {
                    $url = asset($passenger->profile_pic);
                    if (!empty($passenger->profile_pic)) {
                        return '<img   src="' . $url . '" style="height:60px;">';
                    }
                })
                ->addColumn('on_boarding', function ($passenger) {
                    if (!empty($passenger->created_at)) {
                        return $passenger->created_at;
                    }
                })
                ->addColumn('p_name', function ($passenger) {
                    if (!empty($passenger->name)) {
                        return $passenger->name;
                    }
                })
                ->addColumn('p_mobile', function ($passenger) {
                    if (!empty($passenger->mobile_no)) {
                        $mob = $passenger->country_code . $passenger->mobile_no;
                        return $mob;
                    }
                })
                ->addColumn('p_email', function ($passenger) {
                    if (!empty($passenger->email)) {
                        return $passenger->email;
                    }
                })

                ->addColumn('p_success_rides', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $ridesCount = RideBookingSchedule::where(['rbs_passenger_id'=>$passenger->id, 'rbs_ride_status' => 'Completed'])->count();
                        $ridesTotal = RideBookingSchedule::leftJoin('customer_invoices', 'ride_booking_schedules.id', '=', 'customer_invoices.ci_ride_id')->where(['ride_booking_schedules.rbs_passenger_id'=>$passenger->id, 'ride_booking_schedules.rbs_ride_status' => 'Completed'])->get()->sum('ci_customer_invoice_amount');
                        $ridesTotal = number_format($ridesTotal, 3, ".", ",");
                        return $ridesCount .' '.$ridesTotal ;
                    }
                })

                ->addColumn('p_cancel_rides', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $ridesCount = RideBookingSchedule::where(['rbs_passenger_id'=>$passenger->id, 'rbs_ride_status' => 'Cancelled'])->count();
                        $ridesTotal = RideBookingSchedule::leftJoin('customer_invoices', 'ride_booking_schedules.id', '=', 'customer_invoices.ci_ride_id')->where(['ride_booking_schedules.rbs_passenger_id'=>$passenger->id, 'ride_booking_schedules.rbs_ride_status' => 'Cancelled'])->get()->sum('ci_customer_invoice_amount');
                        $ridesTotal = number_format($ridesTotal, 3, ".", ",");
                        return $ridesCount .' '.$ridesTotal ;
                    }
                })

                ->addColumn('p_wallet', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $wallet = PassengerAccount::where(['pc_target_id'=>$passenger->id, 'pc_operation_type' => 'ride'])->get()->last();
                        if (isset($wallet)){
                            $walletAmount = $wallet->pc_balance;
                        }else{
                            $walletAmount = 0;
                        }

                        $walletAmount = number_format($walletAmount, 3, ".", ",");
                        return $walletAmount;
                    }
                })

                ->addColumn('p_rating', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $crRating = $passenger->PassengerRating->sum('pr_rating');
                        $total = $passenger->PassengerRating->count();

                        $rating = (isset($crRating) && $crRating != null) ? number_format((float)$crRating/$total , 2, '.', '') : '0.00';

                        return $rating;
                    }
                })

                ->addColumn('p_last_ride', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $currentDateTime = date('Y-m-d H:s:i');
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $currentDateTime);

                        $rides = RideBookingSchedule::where(['rbs_passenger_id'=>$passenger->id])->get()->last();

                        if (isset($rides)){
                            $last = $rides->rbs_created_at;

//                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $last);
                            $to = Utility:: convertTimeToUSERzone($last,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));

                            $format = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
                            $diff_in_minutes = $format->diffInMinutes($from);

                            $hrs_format = intdiv($diff_in_minutes, 60);

                            if($hrs_format !== 0){
                                $diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60).'mins ago';
                            }else{
                                $diff_in_minutes = ($diff_in_minutes % 60).'mins ago';
                            }
                        }else{
                            $diff_in_minutes = 'No Ride';
                        }

                        return $diff_in_minutes;
                    }
                })

                ->addColumn('p_last_online', function ($passenger) {
                    if (!empty($passenger->id)) {
                        $location = PassengerCurrentLocation::where('pcl_passenger_id', $passenger->id)->first();
                        if (isset($location)){
                            $city = $location->pcl_country;
                        }else{
                            $city = 'none';
                        }
                        return $city;
                    }
                })

                ->addColumn('action', function ($passenger) {
                    $view_driver_btn = '<a type="button" data-rideid="' . $passenger->id . '" class=" passenger-details btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Passenger Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    return $view_driver_btn;
                })
                ->rawColumns(['action', 'logo','p_last_online','p_last_ride','p_rating','p_wallet','p_cancel_rides','p_success_rides','p_email','p_mobile','p_name','on_boarding'])
                ->make(true);
        }
        return view('admin.passenger.index');
    }

    /**
     * Show the form for creating a new Passenger.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.passenger.create');
    }

    /**
     * Store a newly created Passenger in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified Passenger.
     *
     * @param int $id
     * @return Factory|View
     */
    public function showPassenger($id)
    {
        $passenger = User::where('id', $id)->with('address')->first();
        $passengerCancelCount = PassengerCancelRideHistory::where('pcrh_passenger_id', $id)->count();
        $passengerTotalCount = RideBookingSchedule::where('rbs_passenger_id', $id)->count();
        $passengerTotalTopupCount = PassengerAccount::where('pc_target_id', $id)->sum('pc_balance');

        return view('admin.passenger.show', ['passenger' => $passenger, 'passengerCancelCount' => $passengerCancelCount, 'passengerTotalCount' => $passengerTotalCount, 'passengerTotalTopupCount' => $passengerTotalTopupCount]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified Passenger from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

}
