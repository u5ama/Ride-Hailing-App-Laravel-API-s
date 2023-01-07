<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppControl;
use App\BaseAppNotification;
use App\Device;
use App\LanguageString;
use App\Notification\Notification;
use App\User;
use App\Country;
use Auth;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use App\Utility\Utility;

class BaseAppNotificationController extends Controller
{
    /**
     * Display a listing of the BaseAppNotifications.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');

            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $base_app_notifications = BaseAppNotification::query()->whereRaw("date(base_app_notifications.ban_created_at) >= '" . $start_date . "' AND date(base_app_notifications.ban_created_at) <= '" . $end_date . "'")
                    ->leftJoin('admin_users as sender', 'base_app_notifications.ban_sender_id', '=', 'sender.id')
                    ->leftJoin('users as recipient', 'base_app_notifications.ban_recipient_id', '=', 'recipient.id')
                    ->select('base_app_notifications.id as id', 'sender.name as sender_name', 'recipient.name as recipient_name', 'base_app_notifications.ban_sender_id', 'base_app_notifications.ban_recipient_id', 'base_app_notifications.ban_type_of_notification'
                        , 'base_app_notifications.ban_title_text', 'base_app_notifications.ban_body_text', 'base_app_notifications.ban_activity', 'base_app_notifications.ban_is_unread'
                        , 'base_app_notifications.ban_is_hidden', 'base_app_notifications.ban_notifiable_id', 'base_app_notifications.ban_notifiable_type', 'base_app_notifications.ban_notification_status', 'base_app_notifications.ban_created_at')
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                $base_app_notifications = BaseAppNotification::whereDate('base_app_notifications.ban_created_at',date('Y-m-d'))->
                leftJoin('admin_users as sender', 'base_app_notifications.ban_sender_id', '=', 'sender.id')
                    ->leftJoin('users as recipient', 'base_app_notifications.ban_recipient_id', '=', 'recipient.id')
                    ->select('base_app_notifications.id as id', 'sender.name as sender_name', 'recipient.name as recipient_name', 'base_app_notifications.ban_sender_id', 'base_app_notifications.ban_recipient_id', 'base_app_notifications.ban_type_of_notification'
                        , 'base_app_notifications.ban_title_text', 'base_app_notifications.ban_body_text', 'base_app_notifications.ban_activity', 'base_app_notifications.ban_is_unread'
                        , 'base_app_notifications.ban_is_hidden', 'base_app_notifications.ban_notifiable_id', 'base_app_notifications.ban_notifiable_type', 'base_app_notifications.ban_notification_status', 'base_app_notifications.ban_created_at')
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            return Datatables::of($base_app_notifications)
                ->addColumn('notification_status', function ($base_app_notifications) {
                    $status = $base_app_notifications->ban_notification_status;
                    if ($status == 1) {
                        $status = 'Sent';
                    } else {
                        $status = 'Not Sent';
                    }
                    return $status;
                })
                ->addColumn('ban_created_at', function ($base_app_notifications) {
                return Utility:: convertTimeToUSERzone($base_app_notifications->ban_created_at,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));
                })
                ->addColumn('action', function ($base_app_notifications) {
                    $edit_button = '<a href="' . route('admin::appNotification.edit', [$base_app_notifications->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $base_app_notifications->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $delete_button;
                })
                ->rawColumns(['action', 'notification_status'])
                ->make(true);
        }
        return view('admin.AppNotification.index');
    }

    /**
     * Show the form for creating a new BaseAppNotification.
     *
     * @return Factory|View
     */
    public function create()
    {
        $countries = Country::listsTranslations('name')
            ->select('countries.id', 'countries.code', 'countries.country_code')
            ->get();
        return view('admin.AppNotification.create', ['countries' => $countries]);
    }

    /**
     * Store a newly created BaseAppNotification in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $target_type = $request->target_type;
        $title = $request->title;
        $message_body = $request->description;
        $body = $message_body;
        $sound = 'sound';
        $data = 'Notification From Admin';
        $action = 'Admin';
        $type = 'silent';

        if ($target_type == 'all_whipp') {
            $topic_name = $target_type;
            $list_user = Device::pluck('user_id')->toArray();
            $list_type = Device::pluck('app_type')->toArray();
            $status = Notification::sendnotificationsByAdminByTopic($topic_name, $title, $body, $sound);
        } elseif ($target_type == 'app') {
            $topic_name = $request->app_type;
            $apptype = $request->app_type;
            if ($apptype == 'all_drivers') {
                $apptype = 'Driver';
            } else {
                $apptype = 'Passenger';
            }
            $list_user = Device::pluck('user_id')->where(['app_type' => $apptype])->toArray();
            $list_type = Device::pluck('app_type')->where(['app_type' => $apptype])->toArray();
            $status = Notification::sendnotificationsByAdminByTopic($topic_name, $title, $body, $sound);
        } elseif ($target_type == 'device') {
            $topic_name = $request->device_type;
            $device_type = $request->device_type;
            if ($device_type == 'all_androids') {
                $device_type = 'Android';
            } else {
                $device_type = 'iOS';
            }
            $list_user = Device::pluck('user_id')->where(['device_type' => $device_type])->toArray();
            $list_type = Device::pluck('app_type')->where(['device_type' => $device_type])->toArray();
            $status = Notification::sendnotificationsByAdminByTopic($topic_name, $title, $body, $sound);
        } elseif ($target_type == 'select_country') {
            $app_type = $request->app_type;

            if ($app_type == 'all_drivers') {
                $app_type = 'Driver';
            } else {
                $app_type = 'Passenger';
            }
            $users = User::all();

            if (isset($request->country_id) && !empty($request->country_id)) {
                $country_code = '+' . $request->country_id;
                $users = $users->where('country_code', $country_code);
            }
            $usersArray = $users->toArray();
            $list_user = array_column($usersArray, 'id');

            $tokensand = Device::whereIn('user_id', $list_user)->where(['device_type' => "Android"])->pluck('device_token')->toArray();

            $tokensios = Device::whereIn('user_id', $list_user)->where(['device_type' => "iOS", 'app_type' => $app_type])->pluck('device_token')->toArray();

            $status = Notification::sendnotificationsByAdmin($tokensios, $tokensand, $title, $body, $sound, $action, $type);

            if ($status == true) {
                $status = 1;
            }

            $save_list_data = [
                'ban_sender_id' => \Auth::guard('admin')->user()->id,
                'ban_sender_type' => 'Admin',
                'ban_type_of_notification' => $sound,
                'ban_title_text' => $title,
                'ban_body_text' => $body,
                'ban_activity' => 'Admin_to_user',
                'ban_notifiable_id' => \Auth::guard('admin')->user()->id,
                'ban_notifiable_type' => 'App/AdminUser',
                'ban_notification_status' => $status
            ];

            foreach ($list_user as $key => $value) {
                $save_list_data['ban_recipient_id'] = $value;
                $save_list_data['ban_recipient_type'] = $app_type;
                BaseAppNotification::create($save_list_data);
            }
            return response()->json(['success' => true, 'message' => 'Notification Send Successfully!']);
        } else {
            $app_type = $request->app_type;
            if ($app_type == 'all_drivers') {
                $app_type = 'Driver';
            } else {
                $app_type = 'Passenger';
            }
            $list_user = $request->user;
            $tokensand = Device::whereIn('user_id', $list_user)->where(['device_type' => "Android", 'app_type' => $app_type])->pluck('device_token')->toArray();
            $tokensios = Device::whereIn('user_id', $list_user)->where(['device_type' => "iOS", 'app_type' => $app_type])->pluck('device_token')->toArray();

            $status = Notification::sendnotificationsByAdmin($tokensios, $tokensand, $title, $body, $sound, $action, $type);
        }

        if ($status == true) {
            $status = 1;
        }

        $save_list_data = [
            'ban_sender_id' => \Auth::guard('admin')->user()->id,
            'ban_sender_type' => 'Admin',
            'ban_type_of_notification' => $sound,
            'ban_title_text' => $title,
            'ban_body_text' => $body,
            'ban_activity' => 'Admin_to_user',
            'ban_notifiable_id' => \Auth::guard('admin')->user()->id,
            'ban_notifiable_type' => 'App/AdminUser',
            'ban_notification_status' => $status
        ];
        foreach ($list_user as $key => $value) {
            $save_list_data['ban_recipient_id'] = $value;
            $save_list_data['ban_recipient_type'] = $list_type[$key];
            BaseAppNotification::create($save_list_data);
        }
        return response()->json(['success' => true, 'message' => 'Notification Send Successfully!']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
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
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        BaseAppNotification::where('id',$id)->delete();
        return response()->json(['success' => true, 'message' => 'Notification Deleted Successfully!']);
    }

    /**Method For Requested Users List
     * @param Request $request
     * @return Factory|View
     */
    public function getUserList(Request $request)
    {
        $target_type = $request->target_type;
        if ($target_type == 'select_customer') {
            $users = User::
            leftJoin('devices as d', 'users.id', '=', 'd.user_id')
                ->select('users.*', 'users.id as id', 'd.device_type');

            if (isset($request->country_id) && !empty($request->country_id)) {
                $country_code = '+' . $request->country_id;
                $users = $users->where('country_code', $country_code);
            }
            $users = $users->groupBy('users.id');
            $users = $users->get();

            if ($users) {
                return view('admin.AppNotification.userlist', [
                    'users' => $users,
                    'target_type' => $target_type
                ]);
            } else {
                return view('admin.AppNotification.userlist');
            }
        }

        if ($target_type == 'select_country') {
            $countries = Country::listsTranslations('name')
                ->select('countries.id', 'countries.code', 'countries.country_code')
                ->get();
            if ($countries) {
                return view('admin.AppNotification.countrylist', [
                    'countries' => $countries,
                    'target_type' => $target_type
                ]);
            } else {
                return view('admin.AppNotification.countrylist');
            }
        }
    }
}
