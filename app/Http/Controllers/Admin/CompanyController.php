<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppNotification;
use App\BaseAppSocialLinks;
use App\Company;
use App\CompanyCommission;
use App\Country;
use App\Device;
use App\Driver;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\LanguageString;
use App\Mail\CompanyStatusEmail;
use App\Mail\CompanyWelcomeEmail;
use App\Mail\DriverStatusEmail;
use App\Notification\Notification;
use App\Roles;
use App\TransportType;
use App\User;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of the Company.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $company = Company::all();
            return Datatables::of($company)
                ->addColumn('logo', function ($company) {
                    $url = asset($company->com_logo);
                    $image = '';
                    if (!empty($company->com_logo)) {
                        $image = '<img   src="' . $url . '" >';
                    }

                    return $image;
                })
                ->addColumn('drivers_count', function ($company) {
                    $drivers = Driver::where(['du_com_id' => $company->id, 'du_driver_status' => 'driver_status_when_approved'])->count();
                    return $drivers;
                })
                ->addColumn('status', function ($company) {
                    if ($company->com_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    if ($company->com_status == 1) {
                        $class = "badge badge-info";
                        $name = "Temporary Inactive";
                    }
                    if ($company->com_status == 2) {
                        $class = "badge badge-warning";
                        $name = "Temporary Block";
                    }
                    if ($company->com_status == 3) {
                        $class = "badge badge-warning";
                        $name = "Permanenet Block";
                    }
                    if ($company->com_status == 4) {
                        $class = "badge badge-warning";
                        $name = "Pending for Approval";
                    }
                    if ($company->com_status == 5) {
                        $class = "badge badge-success";
                        $name = "Verified Approved";
                    }

                    $status_button = '<a type="button" data-id="' . $company->id . '" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Change Status">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('change_status', function ($company) {
                    $select_option = '<select class="form-control" onchange="updatestatus(' . $company->id . ')" id="com_status_' . $company->id . '">';
                    $select_option .= ($company->com_status == 0) ? "<option value='0' selected>Inactive</option>" : "<option value='0'>Inactive</option>";
                    $select_option .= ($company->com_status == 1) ? "<option value='1' selected>Temporary Inactive</option>" : "<option value='1'>Temporary Inactive</option>";
                    $select_option .= ($company->com_status == 2) ? "<option value='2' selected>Temporary Block</option>" : "<option value='2'>Temporary Block</option>";
                    $select_option .= ($company->com_status == 3) ? "<option value='3' selected>Permanent Block</option>" : "<option value='3'>Permanenet Block</option>";
                    $select_option .= ($company->com_status == 4) ? "<option value='4' selected>Pending for Approval</option>" : "<option value='4'>Pending for Approval</option>";
                    $select_option .= ($company->com_status == 5) ? "<option value='5' selected>Verified Approved</option>" : "<option value='5'>Verified Approved</option>";
                    $select_option .= "</select>";

                    return $select_option;
                })
                ->addColumn('action', function ($company) {
                    $edit_button = '<a type="button" href="' . route('admin::company.edit', [$company->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $view_driver_btn = '<a href="' . route('admin::company.show', [$company->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="View Details"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    $commissionBtn = '<a href="' . route('admin::commission', [$company->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit Commission"><i class="fas fa-percent font-size-16 align-middle"></i></a>';
                    return $edit_button . ' ' . $view_driver_btn. ' '. $commissionBtn;

                })
                ->rawColumns(['action', 'logo', 'status', 'change_status','drivers_count'])
                ->make(true);
        }
        return view('admin.company.index');
    }

    /**
     * Show the form for creating a new Company.
     *
     * @return Factory|View
     */
    public function create()
    {
        $transportTypes = TransportType::listsTranslations('name')->where('tt_status', 1)->get();
        return view('admin.company.create',['transportTypes' => $transportTypes]);
    }

    public function getCommissionDetailData(Request $request)
    {
        $commission = CompanyCommission::where(['company_id'=> $request->companyId, 'transport_id' => $request->input('fpd_transport_type_id')])->get();
        return view('admin.company.commissionDetail',['commission' => $commission]);
    }

    public function commissionPage($id)
    {
        $transportTypes = TransportType::listsTranslations('name')->where('tt_status', 1)->get();
        return view('admin.company.commisson',['transportTypes' => $transportTypes,'company_id' =>$id]);
    }

    public function storeCommissionData(Request $request)
    {

        $companyId = $request->input('companyId');
        $fpd_transport_type_id = $request->input('fpd_transport_type_id');
        $companyCommissionId = $request->input('companyCommissionId');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (CompanyCommission::where(['company_id' => $companyId, 'transport_id' => $fpd_transport_type_id])
            ->whereTime('company_commission.start_date', '<=', $start_date)
            ->whereTime('company_commission.end_date', '=>', $end_date)
            ->exists()) {
            return response()->json(['success' => false, 'message' => 'Commission Already exits for selected date range, Please select the different range of fare plan']);
        } else {
            $whipp_commission = $request->input('whipp_commission');
            $company_commission = $request->input('company_commission');
            $driver_commission = $request->input('driver_commission');

            $fpd_start_date = $start_date;
            $fpd_end_date = $end_date;

            for ($i = 0; $i < count($whipp_commission); $i++) {
                $commissionDetail = CompanyCommission::where(['company_id' => $companyId, 'transport_id' => $fpd_transport_type_id, 'id' => $whipp_commission[$i]])->get();

                if (isset($commissionDetail) && count($commissionDetail) > 0) {

                    $farePlanHead = CompanyCommission::where(['company_id' => $companyId, 'transport_id' => $fpd_transport_type_id, 'id' => $commissionDetail[$i]])->first();
//                    $farePlanHead->fpd_updated_by = auth()->guard('admin')->user()->id;
                } else {
                    $farePlanHead = new CompanyCommission;
//                    $farePlanHead->fpd_created_by = auth()->guard('admin')->user()->id;
                }

                $TOTAL = $whipp_commission[$i] + $company_commission[$i] + $driver_commission[$i];

                if ($TOTAL > 100 || $TOTAL < 100){
                    return response()->json(['success' => false, 'message' => 'The commission total should be equal to 100%']);
                }
                $farePlanHead->company_id = $companyId;
                $farePlanHead->transport_id = $fpd_transport_type_id;
                $farePlanHead->whipp_commission = $whipp_commission[$i];
                $farePlanHead->company_commission = $company_commission[$i];
                $farePlanHead->driver_commission = $driver_commission[$i];
                $farePlanHead->start_date = $fpd_start_date[$i];
                $farePlanHead->end_date = $fpd_end_date[$i];

                $farePlanHead->save();

            }
            return response()->json(['success' => true, 'message' => 'Commission Detail is successfully Saved', 'companyId' => $companyId, 'fpd_transport_type_id' => $fpd_transport_type_id]);
        }
    }
    /**
     * Store a newly created Company in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $validator_array = [
                'com_name' => 'required',
                'com_contact_number' => 'required',
                'com_logo' => 'mimes:jpeg,jpg,png,gif|required',
                'com_full_contact_number' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            /*Country Code*/
                $data= $request->input('country_code');
                $trimmed = str_replace('+', '', $data);
                $country = Country::where('code', $trimmed)->first();
            /*Country Code*/

            $company = new Company();

            if ($request->hasFile('com_logo')) {
                $mime = $request->com_logo->getMimeType();
                $logo = $request->file('com_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/company/logo/', $logoName);
                $comlogo = 'assets/company/logo/' . $logoName;
                $company->com_logo = $comlogo;
            }
            $company->com_name = $request->input('com_name');
            $company->com_contact_number = $request->input('com_contact_number');
            $company->com_full_contact_number = $request->input('com_full_contact_number');
            $company->com_license_no = $request->input('com_license_no');
            $company->com_service_type = $request->input('com_service_type');
            $company->com_time_zone = $request->input('com_time_zone');
            $company->com_radius = $request->input('com_radius');
            $company->com_lat = $request->input('com_lat');
            $company->com_long = $request->input('com_long');
            $company->com_user_name = $request->input('com_user_name');
            $company->email = $request->input('email');
            $company->password = Hash::make($request->input('password'));
            $company->com_country_code = $request->input('country_code');
            $company->com_country_id = $country->id;
            $company->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_inserted')]);
        } else {
            $company = Company::find($id);
            if ($request->hasFile('com_logo')) {
                $mime = $request->com_logo->getMimeType();
                $logo = $request->file('com_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/company/logo/', $logoName);
                $comlogo = 'assets/company/logo/' . $logoName;
                $company->com_logo = $comlogo;
            }

            /*Country Code*/
            $data= $request->input('country_code');
            $trimmed = str_replace('+', '', $data);
            $country = Country::where('code', $trimmed)->first();
            /*Country Code*/

            $company->com_name = $request->input('com_name');
            $company->com_contact_number = $request->input('com_contact_number');
            $company->com_full_contact_number = $request->input('com_full_contact_number');
            $company->com_license_no = $request->input('com_license_no');
            $company->com_service_type = $request->input('com_service_type');
            $company->com_time_zone = $request->input('com_time_zone');
            $company->com_radius = $request->input('com_radius');
            $company->com_lat = $request->input('com_lat');
            $company->com_long = $request->input('com_long');
            $company->com_user_name = $request->input('com_user_name');
            $company->email = $request->input('email');
            $company->com_country_id = $country->id;
            $company->com_country_code = $request->input('country_code');

            if (!empty($request->password)) {

                $company->password = Hash::make($request->input('password'));
            }
            $company->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_updated')]);
        }
    }

    /**
     * Display the specified Company.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $company = Company::where('id', $id)->first();
        $drivers = Driver::with('DriverProfile')->where('du_com_id', $id)->get();

        return view('admin.company.show', ['company' => $company, 'drivers' => $drivers]);
    }

    /**
     * Show the form for editing the specified Company.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if ($company) {
            return view('admin.company.edit', ['company' => $company]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified Company from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    public function commissionDeletePage($id)
    {
        CompanyCommission::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Company Commission is successfully Deleted']);
    }

    /**Change the status for Company
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        Company::where('id', $id)->update(['com_status' => $status]);

        $company = Company::where('id', $id)->first();
        if ($company->com_status == 5){

            $company_name = $company->com_name;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',1)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => 'en'])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => 'en'])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => 'en'])->first();
            $langtxt = 'en';
            $user_type = "user";

            Mail::to($company->email)->send(new CompanyStatusEmail($company_name,$company->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));
        }


        return response()->json(['success' => true, 'message' => 'Company status is successfully Updated']);
    }

    /** Update Driver Status of Company
     * @param Request $request
     * @param $id
     * @param $status
     * @param $company_id
     * @return JsonResponse
     */
    public function updateDriverStatus(Request $request, $id, $status, $company_id)
    {
        $driver = Driver::find($id);
        if ($status == "driver_status_when_approved") {
            App::setLocale($driver->locale);
            $title =  LanguageString::translated()->where('bls_name_key', 'driver_status_when_approved_admin')->first()->name;
            $body = $driver->du_full_name . " " . LanguageString::translated()->where('bls_name_key', 'driver_status_when_approved_admin_desc')->first()->name;
            App::setLocale('en');
            $driver = Driver::where('id', $id)->first();

                $driver_name = $driver->du_full_name;
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',8)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 8, 'locale' => $driver->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 8, 'locale' => $driver->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 8,'locale' => $driver->locale])->first();
                $langtxt = $driver->locale;
                $user_type = "driver";

                Mail::to($driver->email)->send(new DriverStatusEmail($driver_name,$driver->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));
        }
        if ($status == "driver_status_when_block") {
            App::setLocale($driver->locale);
            $title =  LanguageString::translated()->where('bls_name_key', 'driver_status_when_block_admin')->first()->name;
            $body = $driver->du_full_name . " " . LanguageString::translated()->where('bls_name_key', 'driver_status_when_block_admin_desc')->first()->name;
            App::setLocale('en');
        }
        if ($status == "driver_status_when_pending") {
            App::setLocale($driver->locale);
            $title =  LanguageString::translated()->where('bls_name_key', 'driver_status_when_pending_admin')->first()->name;
            $body = $driver->du_full_name . " " . LanguageString::translated()->where('bls_name_key', 'driver_status_when_pending_admin_desc')->first()->name;
            App::setLocale('en');
        }

        $tokensand = Device::where(['user_id' => $driver->id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
        $tokensios = Device::where(['user_id' => $driver->id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();

        $sound = 'default';
        $action = 'Admin';
        $type = 'pushNotification';
        $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, Auth::guard('admin')->user()->id, null, $drivers = 1);

        $noti_data = [
            'ban_sender_id' => Auth::guard('admin')->user()->id,
            'ban_recipient_id' => $id,
            'ban_sender_type' => 'Admin',
            'ban_recipient_type' => 'Driver',
            'ban_type_of_notification' => $type,
            'ban_title_text' => $title,
            'ban_body_text' => $body,
            'ban_activity' => $action,
            'ban_notifiable_type' => 'App\Admin',
            'ban_notifiable_id' => $id,
            'ban_notification_status' => $notifications,
            'ban_created_at' => now(),
            'ban_updated_at' => now()
        ];
        BaseAppNotification::create($noti_data);
        Driver::where('id', $id)->update(['du_driver_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Driver status is successfully Updated']);
    }

    /** Get Company Status
     * @return JsonResponse
     */
    public function getCompanyStatus()
    {
        $array['globalModalTitle'] = 'Change Status';
        $array['globalModalDetails'] = '<form method="POST" data-parsley-validate="" id="changeStatusForm" role="form">';
        $array['globalModalDetails'] .= '<select class="form-control" id="com_status" name="com_status">';
        $array['globalModalDetails'] .= '<option value="0">Inactive</option>';
        $array['globalModalDetails'] .= '<option value="1">Temporary Inactive</option>';
        $array['globalModalDetails'] .= '<option value="2">Temporary Block</option>';
        $array['globalModalDetails'] .= '<option value="3">Permanenet Block</option>';
        $array['globalModalDetails'] .= '<option value="4">Pending for Approval</option>';
        $array['globalModalDetails'] .= '<option value="5">Verified Approved</option>';
        $array['globalModalDetails'] .= '</select>';
        $array['globalModalDetails'] .= '<button type="submit" class="btn btn-primary">Submit</button>';
        $array['globalModalDetails'] .= '</form>';

        return response()->json(['success' => true, 'data' => $array]);
    }

    public function updateDriverCompany($driver_id , $company_id)
    {
        Driver::where('id', $driver_id)->update(
            [
                'du_com_id' => $company_id,
                'is_company_update' => 1,
            ]);
        return response()->json(['success' => true, 'message' => 'Driver Company is successfully Updated']);
    }

    public function updateStatusCommission($commissionId, $status)
    {
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }

        CompanyCommission::where('id', $commissionId)->update(
            [
                'commission_status' => $status_new,
            ]);
        return response()->json(['success' => true, 'message' => 'Status is successfully Updated']);
    }


    public function changeDriverRegStatus($id, $status)
    {
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        Driver::where('id', $id)->update(['du_is_reg_active' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Registration Status status is successfully Updated']);
    }
}
