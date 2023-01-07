<?php

namespace App\Http\Controllers;

use App\Company;
use App\CustomerInvoice;
use App\Driver;
use App\DriverAccount;
use App\DriverCancelRideHistory;
use App\DriverCurrentLocation;
use App\DriverCurrentLogs;
use App\DriverProfile;
use App\DriverRating;
use App\PassengerAccount;
use App\PassengerAddress;
use App\PassengerCancelRideHistory;
use App\PassengerContactList;
use App\PassengerCurrentLocation;
use App\PassengerPaymentDetail;
use App\PassengerRating;
use App\RideBookingSchedule;
use App\TransactionId;
use App\User;
use Illuminate\Http\Request;
use App\Utility\Utility;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;

class SQLQuriesController extends Controller
{
    public function transactionsRemoval()
    {
        PassengerAccount::query()->truncate();
        DriverAccount::query()->truncate();
        CustomerInvoice::query()->truncate();
        TransactionId::query()->truncate();
        PassengerPaymentDetail::query()->truncate();

        return 'true';
    }

    public function UsersRemoval()
    {
        //Passengers
        User::query()->truncate();
        PassengerContactList::query()->truncate();
        PassengerAddress::query()->truncate();
        PassengerRating::query()->truncate();
        PassengerCancelRideHistory::query()->truncate();
        PassengerCurrentLocation::query()->truncate();
        PassengerContactList::query()->truncate();
        PassengerAccount::query()->truncate();
        CustomerInvoice::query()->truncate();
        TransactionId::query()->truncate();
        PassengerPaymentDetail::query()->truncate();

        //Drivers
        Driver::query()->truncate();
        DriverAccount::query()->truncate();
        DriverCancelRideHistory::query()->truncate();
        DriverProfile::query()->truncate();
        DriverCurrentLocation::query()->truncate();
        DriverRating::query()->truncate();
        DriverCurrentLogs::query()->truncate();

        return 'true';
    }

    public function AllRemoval()
    {
        //Passengers
        User::query()->truncate();
        PassengerContactList::query()->truncate();
        PassengerAddress::query()->truncate();
        PassengerRating::query()->truncate();
        PassengerCancelRideHistory::query()->truncate();
        PassengerCurrentLocation::query()->truncate();
        PassengerContactList::query()->truncate();
        PassengerAccount::query()->truncate();
        CustomerInvoice::query()->truncate();
        TransactionId::query()->truncate();
        PassengerPaymentDetail::query()->truncate();

        //Drivers
        Driver::query()->truncate();
        DriverAccount::query()->truncate();
        DriverCancelRideHistory::query()->truncate();
        DriverProfile::query()->truncate();
        DriverCurrentLocation::query()->truncate();
        DriverRating::query()->truncate();
        DriverCurrentLogs::query()->truncate();

        //Company
        Company::where('id', '!=', 1 )->delete();

        return 'true';
    }

    public function RemoveCompany($id)
    {
        if ($id != 1){
            $company = Company::where('id', $id)->first();
            $drivers = Driver::where('du_com_id', $company->id)->pluck('id')->toArray();
            DriverAccount::whereIn('dc_target_id',$drivers)->delete();
            DriverCancelRideHistory::whereIn('dcrh_driver_id',$drivers)->delete();
            DriverProfile::whereIn('dp_user_id',$drivers)->delete();
            DriverCurrentLocation::whereIn('dcl_user_id',$drivers)->delete();
            DriverRating::whereIn('dr_driver_id',$drivers)->delete();
            DriverCurrentLogs::whereIn('dcl_user_id',$drivers)->delete();

            Company::where('id', $id)->delete();
            Driver::where('du_com_id', $id)->delete();

            return 'true';
        }
        else{
            return 'Cannot delete Whipp Company';
        }

    }
}
