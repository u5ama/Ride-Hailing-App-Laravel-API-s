<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Utility\Utility;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use App\Redirection\Redirection;
use Unicodeveloper\Identify\Identify;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', 'Company\CompanyLoginController@showLoginForm')->name('login')->middleware('prevent-back-history');

Route::get('/testtimezone', function () {
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    $transID = Utility::InvoicetransID($user = []);
    return $transID;
});
Route::get('/driververifiedphp', function () {
    $identity = new Identify();
    $osname = $identity->os()->getName();
    if($osname == "iOS"){
        $url = 'https://apps.apple.com/us/app/travelist/id1498411647';

        return view('applink',['url'=>$url]);
    }
    if($osname == "Android"){
        $url = 'https://play.google.com/store/apps/details?id=com.travelist.hi5';
        return view('applink',['url'=>$url]);
    }

        $url = 'https://app.apis.ridewhipp.com/';
        return view('applink',['url'=>$url]);

});
Route::get('/verifiedphp', function () {
    $identity = new Identify();
    $osname = $identity->os()->getName();
    if($osname == "iOS"){
        $url = 'https://apps.apple.com/us/app/travelist/id1498411647';

        return view('applink',['url'=>$url]);
    }
    if($osname == "Android"){
        $url = 'https://play.google.com/store/apps/details?id=com.travelist.hi5';
        return view('applink',['url'=>$url]);
    }
        $url = 'https://app.apis.ridewhipp.com/';
        return view('applink',['url'=>$url]);

});

// Driver Wallet WebView Route
Route::get('/email', function () {
    return view('driver.DriverWallet');
});

// Passenger View RideDetails with URL Route
Route::get('/map/{id}', 'MapViewController@viewMap')->name('map');

// Passenger View Success Message on Ride End Route
Route::get('/success', function () {
    return view('rideSuccess');
});

// Create New Company account Route
Route::get('register', 'RegisterController@index')->name('index');

Route::get('verifyEmailPassenger/{id}', function ($id){
  $url =  Redirection::verifyEmailPassenger($id);
  return redirect()->away($url);
})->name('verifyEmailPassenger');

Route::get('verifyEmailDriver/{id}', function ($id){
  $url =  Redirection::verifyEmailDriver($id);
  return redirect()->away($url);
})->name('verifyEmailDriver');

// Create New Company account POST Method
Route::post('registerPost', 'RegisterController@registerPost')->name('registerPost');

// Create New Company account Success Method
Route::get('register/success', 'RegisterController@successMessage')->name('register/success');

//Company Reset Password Route
Route::get('reset-password/{token}', 'ResetPasswordController@resetPasswordForm')->name('reset-password');

// forget password

Route::get('forget-password', 'ForgetPasswordController@forgetPasswordForm')->name('forget-password');

// forget password submit post
Route::post('forgotPassword', 'ForgetPasswordController@forgotPassword')->name('forgotPassword');

//Company Reset Password POST Method
Route::post('resetPasswordSubmit', 'ResetPasswordController@resetPasswordSubmit')->name('resetPassword.update');

//Invite Friends Route
Route::get('inviteFriends/{id}', 'Admin\InviteFriendsController@inviteFriends')->name('inviteFriends');

//Invite Drivers Route
Route::get('inviteDriver/{id}', 'Admin\InviteFriendsController@inviteDriver')->name('inviteDriver');

//Clear Records
Route::get('cleartransactions', 'SQLQuriesController@transactionsRemoval')->name('cleartransactions');
Route::get('clearallusers', 'SQLQuriesController@UsersRemoval')->name('clearallusers');
Route::get('clearall', 'SQLQuriesController@AllRemoval')->name('clearall');
Route::get('clearcompany/{id}', 'SQLQuriesController@RemoveCompany')->name('clearcompany');

// Admin Panel Routes
Route::group(['namespace' => 'Admin','middleware' => 'prevent-back-history', 'as' => 'admin::', 'prefix' => 'admin'], function () {

    // Admin Login Route
    Route::get('login', 'LoginController@showLoginForm')->name('login');

    // Admin Login POST Method
    Route::post('postlogin', 'LoginController@login')->name('postlogin');

        // Admin Panel Middleware
        Route::group(['middleware' => ['admin', 'adminLanguage']], function () {

            // Admin Panel Home Route
            Route::get('admin', 'AdminController@index')->name('admin');

            Route::get('getTimeZone', 'AdminController@getTimeZone')->name('getTimeZone');

            // Admin Panel Change Theme Route
            Route::get('changeThemes/{id}', 'AdminController@changeThemes')->name('changeThemes');

            // Admin Panel Change Theme Mode Route
            Route::get('changeThemesMode/{local}', 'AdminController@changeThemesMode')->name('changeThemesMode');

            // Admin Panel Change TimeZone Route
            Route::post('TimeZoneSettings', 'AdminController@TimeZoneSettings')->name('TimeZoneSettings');

            // Admin Panel Company Resource
            Route::resource('company', 'CompanyController');

            Route::post('getCommissionDetailData', 'CompanyController@getCommissionDetailData')->name('getCommissionDetailData');
            Route::get('commission/{id}', 'CompanyController@commissionPage')->name('commission');
            Route::post('storeCommissionData', 'CompanyController@storeCommissionData')->name('storeCommissionData');

            Route::get('deleteCommission/{id}', 'CompanyController@commissionDeletePage')->name('deleteCommission');

            Route::get('updateStatusCommission/{id}/{status}', 'CompanyController@updateStatusCommission')->name('updateStatusCommission');

            // Admin Panel Admins Resource
            Route::resource('admins', 'AdminUsersController');

            // Admin Panel Passengers Resource
            Route::resource('passenger', 'PassengerController');

            // Admin Panel DriverList Resource
            Route::resource('driver_list', 'DriverListController');

            // Admin Panel Display Passenger
            Route::get('showPassenger/{id}', 'PassengerController@showPassenger')->name('showPassenger');

            // Admin Panel Company Status Get Method
            Route::get('getCompanyStatus/{company_id}', 'CompanyController@getCompanyStatus')->name('getCompanyStatus');

            // Admin Panel Company Status Change Method
            Route::get('company/{id}/{status}', 'CompanyController@status')->name('companyStatus');

            // Admin Panel GET Drivers against company
            Route::get('driver/addDriver/{company_id}', 'DriverController@addDriver')->name('driver.addDriver');

            // Admin Panel Edit Driver Route
            Route::get('driver/editDriver/{company_id}/{driver_id}', 'DriverController@edit')->name('driver.editDriver');

            // Admin Panel Update Driver Method
            Route::get('updateDriverStatus/{id}/{status}/{company_id}', 'CompanyController@updateDriverStatus')->name('updateDriverStatus');

            // Admin Panel Update Company Method
            Route::get('updateDriverCompany/{driver_id}/{company_id}', 'CompanyController@updateDriverCompany')->name('updateDriverCompany');

            // admin change driver registtation status alow and disallow in app
             Route::get('changeDriverRegStatus/{id}/{status}', 'CompanyController@changeDriverRegStatus')->name('changeDriverRegStatus');

            // Admin Panel Create New Driver Method
            Route::post('driver/store', 'DriverController@store')->name('driver.store');

            // Admin Panel country resource
            Route::resource('country', 'CountryController');

            // Admin Panel Transport Make resource
            Route::resource('transportMake', 'TransportMakeController');

            // Admin Panel Transport Model resource
            Route::resource('transportModel', 'TransportModelController');

            // Admin Panel Transport Model Colour resource
            Route::resource('transportModelColor', 'TransportModelColorController');

            // Admin Panel Transport Type resource
            Route::resource('transportType', 'TransportTypeController');

            // Admin Panel Transport Model year resource
            Route::resource('transportModelYear', 'TransportModelYearController');

            // Admin Panel Transport Fuel resource
            Route::resource('transportFuel', 'TransportFuelController');

            // Admin Panel Transport GET Method
            Route::post('getModel', 'TransportModelController@getModel')->name('getModel');

            // Admin Panel Transport Make POST Method
            Route::post('getMake', 'TransportMakeController@getMake')->name('getMake');

            // Admin Panel Transport Model POST Method
            Route::post('getModelColor', 'TransportModelColorController@getModelColor')->name('getModelColor');

            // Admin Panel Fare Plan resource
            Route::resource('farePlanHead', 'FarePlanHeadController');

            // Admin Panel GET Fare Plan Details
            Route::get('FarePlanDetailViewModal/{planId}', 'FarePlanHeadController@getDetailedView');

            // Admin Panel Change Fare Plan status Method
            Route::get('farePlanHead/{id}/{status}', 'FarePlanHeadController@status')->name('farePlanHeadStatus');

            // Admin Panel Edit Fare Plan Head Route
            Route::get('getFarePlanHeadByid/{id}', 'FarePlanHeadController@getFarePlanHeadByid')->name('getFarePlanHeadByid');

            // Admin Panel Fare Plan Details resource
            Route::resource('FarePlanDetail', 'FarePlanDetailController');

            // Admin Panel Fare Plan Details POST Method
            Route::get('FarePlanDetail/add/{id}', 'FarePlanDetailController@create')->name('FarePlanDetail.add');

            // Admin Panel Fare Plan Details GET Method
            Route::post('getFareDetailData', 'FarePlanDetailController@getFareDetailData')->name('getFareDetailData');

            // Admin Panel Fare Plan Details Extra Charge POST Method
            Route::post('fareExtraCharge', 'FarePlanDetailController@fareExtraCharge')->name('fareExtraCharge');

            // Admin Panel Fare Plan Details Extra Charge GET Method
            Route::post('getFareExtraModalData', 'FarePlanDetailController@getFareExtraModalData')->name('getFareExtraModalData');

            // Admin Panel Fare Plan Details Extra Charge DELETE Method
            Route::get('deleteExtraFareCharge/{id}', 'FarePlanDetailController@deleteExtraFareCharge')->name('deleteExtraFareCharge');

            // Admin Panel Fare Plan Details Extra Charge GET Method
            Route::get('checkExistExtraCharges/{id}/{head_id}', 'FarePlanDetailController@checkExistExtraCharges')->name('checkExistExtraCharges');

            // Admin Panel App Notifications Resource
            Route::resource('appNotification', 'BaseAppNotificationController');

            // Admin Panel App Notifications Users List
            Route::post('getUserList', 'BaseAppNotificationController@getUserList')->name('getUserList');

            // Admin Panel App SMTP Resource
            Route::resource('SMTPSetting', 'SMTPSettingController');

            // Admin Panel App SMTP Status Change Method
            Route::get('SMTPSetting/{id}/{status}', 'SMTPSettingController@status')->name('SMTPSetting');

            // Admin Panel App FCM Resource
            Route::resource('FCMSetting', 'FCMSettingController');

            // Admin Panel App FCM Status Change Method
            Route::get('FCMSetting/{id}/{status}', 'FCMSettingController@status')->name('FCMSetting');

            // Admin Panel Reference Resource
            Route::resource('referenceModule', 'ReferenceModuleController');

            // Admin Panel App Reference Change Status Method
            Route::get('referenceModule/{id}/{status}', 'ReferenceModuleController@status')->name('referenceModuleStatus');

            // Admin Panel App Reference Type Resource
            Route::resource('referenceType', 'AppReferenceTypeController');

            // Admin Panel App Reference Type Change Status Method
            Route::get('referenceType/{id}/{status}', 'AppReferenceTypeController@status')->name('referenceTypeStatus');

            // Admin Panel App Reference Resource
            Route::resource('appReference', 'AppReferenceController');

            // ledger report
            Route::resource('ledgerReport', 'LedgerReportController');

            // get passenger or driver for ledger report
            Route::post('getPassengerORDriver', 'LedgerReportController@getPassengerORDriver')->name('getPassengerORDriver');

            // get ledger report
            Route::post('getLedgerReport', 'LedgerReportController@getLedgerReport')->name('getLedgerReport');

            // get Reference Type by reference module wise using post method
            Route::post('getReferenceType', 'AppReferenceController@getReferenceType')->name('getReferenceType');

            // get reference order
            Route::post('getReferenceOrder', 'AppReferenceController@getReferenceOrder')->name('getReferenceOrder');

            // Admin Panel App Reference Change Status Method
            Route::get('appReference/{id}/{status}', 'AppReferenceController@status')->name('appReferenceStatus');

            // Admin Panel App Languages Resource
            Route::resource('languages', 'LanguageController');

            // Admin Panel App Languages Status Change Method
            Route::get('languages/{id}/{status}', 'LanguageController@status')->name('languagesStatus');

            // Admin Panel App Languages Language Strings Resource
            Route::resource('languageString', 'LanguageStringController');

            Route::post('getLanguageScreen', 'LanguageStringController@getLanguageScreen')->name('getLanguageScreen');

            // Admin Panel App Languages Strings Status Change Method
            Route::get('languageString/{id}/{status}', 'LanguageStringController@status')->name('languageStringStatus');

            // Admin Panel App Languages Screens Resource
            Route::resource('languageScreen', 'LanguageScreenController');

            // Admin Panel App Languages Screen Status Change Method
            Route::get('languageScreen/{id}/{status}', 'LanguageScreenController@status')->name('languageScreenStatus');

            // Admin Panel App Languages Screens View Method
            Route::get('languageScreenView/{id}', 'LanguageScreenController@screenview')->name('languageScreenView');

            // Admin Panel App Base Default Image Resource
            Route::resource('BaseDefaultImage', 'BaseDefaultImagesController');

            // Admin Panel App Base Default Image Change Status Method
            Route::get('BaseDefaultImage/{id}/{status}', 'BaseDefaultImagesController@status')->name('BaseDefaultImageStatus');

            // Admin Panel App Base Social Links Resource
            Route::resource('BaseAppSocialLink', 'BaseAppSocialLinksController');

            // Admin Panel App Base Social Links Change Status Resource
            Route::get('BaseAppSocialLink/{id}/{status}', 'BaseAppSocialLinksController@status')->name('BaseAppSocialLinkStatus');

            // Admin Panel App Base Theme Resource
            Route::resource('BaseAppTheme', 'BaseAppThemeController');

            // Admin Panel App Base Theme Change Status
            Route::get('BaseAppTheme/{id}/{status}', 'BaseAppThemeController@status')->name('BaseAppThemeStatus');

            // Admin Panel App Base App Control Resource
            Route::resource('BaseAppControl', 'BaseAppControlController');

            // Admin Panel App Base App Control Change Status Method
            Route::get('BaseAppControl/{id}/{status}', 'BaseAppControlController@status')->name('BaseAppControlStatus');

            // Admin Panel App Invoice Plan Resource
            Route::resource('InvoicePlan', 'InvoicePlanController');

            // Admin Panel App Base Invoice Plan Change Status Method
            Route::get('InvoicePlan/{id}/{status}', 'InvoicePlanController@status')->name('InvoicePlanStatus');

            // Admin Panel App Base Theme Design Resource
            Route::resource('BaseAppThemeDesign', 'BaseAppThemeDesignController');

            // Admin Panel App Base Theme Design Change Status Method
            Route::get('BaseAppThemeDesign/{id}/{status}', 'BaseAppThemeDesignController@status')->name('BaseAppThemeDesignStatus');

            // Admin Panel App Page Resource
            Route::resource('page', 'PageController');

            // Admin Panel App Page Change Status Method
            Route::get('page/{id}/{status}', 'PageController@status')->name('pageStatus');

            // Admin Panel App Page View Method
            Route::get('pageView/{id}', 'PageController@pageView')->name('pageView');


            // Admin Panel Web Page Resource
            Route::resource('webpage', 'WebPageController');

            // Admin Panel Web Page Change Status Method
            Route::get('webpage/{id}/{status}', 'WebPageController@status')->name('webPageStatus');

            Route::get('webpageSkip/{id}/{skip}', 'WebPageController@Is_Skip')->name('webpageSkip');

            // Admin Panel Web Page View Method
            Route::get('webpageView/{id}', 'WebPageController@pageView')->name('webpageView');


            // Admin Panel Upcoming Schedule Rides Resource
            Route::resource('upcomingScheduleRides', 'UpcomingScheduledRidesController');

            // Admin Panel InProgress Ride Booking Schedule Resource
            Route::resource('inProgressScheduleRides', 'InProgressScheduleRidesController');

            // Admin Panel Ride Booking Schedule Resource
            Route::resource('rideBookingSchedule', 'RideBookingScheduleController');

            // Admin Panel View Map on Details Method
            Route::post('getViewMapModal', 'RideBookingScheduleController@getViewMapModal')->name('getViewMapModal');

            // Admin Panel Ride Ignored Drivers Resource
            Route::resource('rideIgnoredByDriver', 'RideIngnoredbyController');

            // Admin Panel Ride Details View
            Route::get('getTotalRideViewModal/{ride_id}/{driver_id}', 'RideIngnoredbyController@getTotalRideViewModal')->name('getTotalRideViewModal');

            // Admin Panel Ride Cancel by Passenger Resource
            Route::resource('rideCancelByPassenger', 'RideCancelByPassengerController');

            // Admin Panel Ride Cancel By Driver Resource
            Route::resource('rideCancelByDriver', 'RideCancelByDriverController');

            // Admin Panel Ride Cancel By Driver View Details Method
            Route::get('getRideCancelByPassenger/{ride_id}/{driver_id}', 'RideCancelByPassengerController@getRideCancelByPassenger')->name('getRideCancelByPassenger');

            // Admin Panel Ride GET Passenger Graph Record Method
            Route::get('getGraphRecordPassenger/{filterType}', 'RideCancelByPassengerController@getGraphRecordPassenger')->name('getGraphRecordPassenger');

            // Admin Panel Ride GET Driver Graph Record Method
            Route::get('getGraphRecordDriver/{filterType}', 'RideCancelByDriverController@getGraphRecordDriver')->name('getGraphRecordDriver');

            // Admin Panel Ride GET Driver Ride Cancel Method
            Route::get('getRideCancelByDriver/{driver_id}/{ride_id}', 'RideCancelByDriverController@getRideCancelByDriver')->name('getRideCancelByDriver');

            // Admin Panel Ride GET Passenger Ride Cancel Method
            Route::get('getRideCancelByPassengerDetails/{driver_id}/{ride_id}', 'RideCancelByPassengerController@getRideCancelByPassengerDetails')->name('getRideCancelByPassengerDetails');

            // Admin Panel Passenger Ride Statistics Resource
            Route::resource('rideStatisticsPassenger', 'RideStatisticsPassengerController');

            // Admin Panel Driver Ride Statistics Resource
            Route::resource('rideStatisticsDriver', 'RideStatisticsDriverController');

            // Admin Panel Payment Settings Resource
            Route::resource('paymentSettings', 'PaymentSettingsController');

            // Admin Panel Payment Settings Change Method
            Route::get('paymentSettings/{id}/{status}', 'PaymentSettingsController@status')->name('paymentSettings');

            // Admin Panel Earning Analysis Resource
            Route::resource('earningAnalysis', 'EarningAnalysisController');

            // Admin Panel Invoice Details And change Status Method
            Route::get('invoicesDetails/{invoiceId}', 'EarningAnalysisController@invoicesDetails')->name('invoicesDetails');

            // Admin Panel Date Filter Method
            Route::post('getDateFilter', 'EarningAnalysisController@dateFilter')->name('getDateFilter');

            // Admin Panel Passenger data Filter Method
            Route::post('getDataFilterDashboard', 'AdminController@getDataFilterDashboard')->name('getDataFilterDashboard');

            // Admin Panel Driver Status Resource
            Route::resource('driverStatus', 'DriverStatusController');

            // Admin Panel Driver Status report Resource
            Route::resource('driverStatus', 'DriverStatusController');
             // admin driver status report
            Route::resource('driverStatusReport', 'DriverStatusReportController');
             // get driver status filter data
             Route::post('getDriverStatusFilter', 'DriverStatusReportController@getDriverStatusFilter')->name('getDriversFilter');

            // Admin Panel Driver Filter Resource
            Route::post('getDriversFilter', 'DriverStatusController@getDriversDataByFilter')->name('getDriversFilter');

            // Admin Panel Driver Record By Country Method
            Route::get('getDriversRecord/{country}', 'DriverStatusController@getDriversData')->name('getDriversRecord');

            // Admin Panel Driver Details Method
            Route::get('getDriversDetail/{driverId}', 'DriverStatusController@driverDetails')->name('getDriversDetail');

            // Admin Panel Companies Method
            Route::post('getCompanies', 'DriverStatusController@getCompanies')->name('getCompanies');

            // Admin Panel Companies Drivers Method
            Route::post('getCompanyDrivers', 'DriverStatusController@getCompanyDriversNumbers')->name('getCompanyDrivers');

            // Admin Panel Companies Vehicle Method
            Route::post('getCompanyDriversVeh', 'DriverStatusController@getCompanyDriversVehicles')->name('getCompanyDriversVeh');

            // Admin Panel Update Manual OTP create Method
            Route::post('updateManualOTP', 'DriverController@updateManualOTP')->name('updateManualOTP');

            // Admin Panel Daily Earning Resource
            Route::resource('dailyEarning', 'DailyEarningController');

            // Admin Panel Daily Earning Resource
            Route::resource('creditCards', 'CreditCardController');

            // Admin Panel Promo Code Resource
            Route::resource('promoCode', 'PromoCodeController');

            // Admin Panel Promo Code Status Change Method
            Route::post('promoCodeStatus/{id}/{status}', 'PromoCodeController@promoCodeStatus')->name('promoCodeStatus');

            // Admin Panel Voucher Resource
            Route::resource('voucher', 'VoucherCodeController');

            // Admin Panel Voucher Change Status Method
            Route::post('voucherCodeStatus/{id}/{status}', 'VoucherCodeController@voucherCodeStatus')->name('voucherCodeStatus');

            // Admin Panel Passenger Payments Resource
            Route::resource('passenger_payments', 'PassengerPaymentController');

            // Admin Panel Ride Details in Model of passenger Method
            Route::get('getRideDetailViewModal/{id}/{status}/{count}', 'RideStatisticsPassengerController@getRideDetailViewModal')->name('getRideDetailViewModal');

            // Admin Panel Ride Details in Model of Driver Method
            Route::get('getRideDetailViewModalByDriver/{id}/{status}/{count}', 'RideStatisticsDriverController@getRideDetailViewModalByDriver')->name('getRideDetailViewModalByDriver');

            // Admin Panel Currencies Resource
            Route::resource('currencies', 'CurrenciesController');

            // Admin Panel change currency Status Method
            Route::get('currencies/{id}/{status}', 'CurrenciesController@statuas')->name('currencies');

            // Admin Panel Admin Roles Resource
            Route::resource('roles', 'RolesController');

            // Admin Panel Admin add Roles Method
            Route::post('addRole', 'RolesController@store')->name('addRole');

            // Admin Panel permissions Resource
            Route::resource('permissions', 'PermissionController');

            // Admin Panel GEO Fencing sending resource
            Route::resource('geo_fencing', 'GeoFencingController');

            // Admin Panel Emails sending resource
            Route::resource('EmailSettings', 'EmailSettingsController');

            // Admin Panel Welcome Passenger Verify Status change
            Route::resource('passenger_verify_email_settings', 'PassengerVerifyEmailSettingsController');

            // Admin Panel Email Passenger Verify Forms POST Methods
            Route::post('passenger_verify_email_settings_form_one', 'PassengerVerifyEmailSettingsController@FormOneCreate')->name('passenger_verify_email_settings_form_one');
            Route::post('passenger_verify_email_settings_form_two', 'PassengerVerifyEmailSettingsController@FormTwoCreate')->name('passenger_verify_email_settings_form_two');
            Route::post('passenger_verify_email_settings_three', 'PassengerVerifyEmailSettingsController@FormThreeCreate')->name('passenger_verify_email_settings_three');

            // Admin Panel Welcome Driver Verify Status change
            Route::resource('driver_verify_email_settings', 'DriverVerifyEmailSettingsController');

            // Admin Panel Email Driver Verify Forms POST Methods
            Route::post('driver_verify_email_settings_form_one', 'DriverVerifyEmailSettingsController@FormOneCreate')->name('driver_verify_email_settings_form_one');
            Route::post('driver_verify_email_settings_form_two', 'DriverVerifyEmailSettingsController@FormTwoCreate')->name('driver_verify_email_settings_form_two');
            Route::post('driver_verify_email_settings_three', 'DriverVerifyEmailSettingsController@FormThreeCreate')->name('driver_verify_email_settings_three');

            // Admin Panel Welcome Driver Approval Status change
            Route::resource('driver_approval_email_settings', 'DriverApprovalEmailSettingsController');

            // Admin Panel Email Driver Approval Forms POST Methods
            Route::post('driver_approval_email_settings_form_one', 'DriverApprovalEmailSettingsController@FormOneCreate')->name('driver_approval_email_settings_form_one');
            Route::post('driver_approval_email_settings_form_two', 'DriverApprovalEmailSettingsController@FormTwoCreate')->name('driver_approval_email_settings_form_two');
            Route::post('driver_approval_email_settings_three', 'DriverApprovalEmailSettingsController@FormThreeCreate')->name('driver_approval_email_settings_three');

            // Admin Panel Welcome Company Approval Status change
            Route::resource('company_approval_email_settings', 'CompanyApprovalEmailSettingsController');

            // Admin Panel Email Company Approval Forms POST Methods
            Route::post('company_approval_email_settings_form_one', 'CompanyApprovalEmailSettingsController@FormOneCreate')->name('company_approval_email_settings_form_one');
            Route::post('company_approval_email_settings_form_two', 'CompanyApprovalEmailSettingsController@FormTwoCreate')->name('company_approval_email_settings_form_two');
            Route::post('company_approval_email_settings_three', 'CompanyApprovalEmailSettingsController@FormThreeCreate')->name('company_approval_email_settings_three');

            // Admin Panel Welcome Company Email Status change
            Route::resource('welcome_company_email_settings', 'WelcomeCompanyEmailSettingsController');

            // Admin Panel Email Company Settings Forms POST Methods
            Route::post('welcome_company_email_settings_form_one', 'WelcomeCompanyEmailSettingsController@FormOneCreate')->name('welcome_company_email_settings_form_one');
            Route::post('welcome_company_email_settings_form_two', 'WelcomeCompanyEmailSettingsController@FormTwoCreate')->name('welcome_company_email_settings_form_two');
            Route::post('welcome_company_email_settings_form_three', 'WelcomeCompanyEmailSettingsController@FormThreeCreate')->name('welcome_company_email_settings_form_three');

            // Admin Panel Welcome Driver Email Status change
            Route::resource('welcome_email_driver_settings', 'WelcomeDriverEmailSettingsController');

            // Admin Panel Email Driver Settings Forms POST Methods
            Route::post('welcome_email_driver_settings_form_one', 'WelcomeDriverEmailSettingsController@FormOneCreate')->name('welcome_email_driver_settings_form_one');
            Route::post('welcome_email_driver_settings_form_two', 'WelcomeDriverEmailSettingsController@FormTwoCreate')->name('welcome_email_driver_settings_form_two');
            Route::post('welcome_email_driver_settings_form_three', 'WelcomeDriverEmailSettingsController@FormThreeCreate')->name('welcome_email_driver_settings_form_three');

            // Admin Panel Welcome Email Status change
            Route::resource('welcome_email_settings', 'WelcomeEmailSettingsController');

            // Admin Panel Email Settings Forms POST Methods
            Route::post('welcome_email_settings_form_one', 'WelcomeEmailSettingsController@FormOneCreate')->name('welcome_email_settings_form_one');
            Route::post('welcome_email_settings_form_two', 'WelcomeEmailSettingsController@FormTwoCreate')->name('welcome_email_settings_form_two');
            Route::post('welcome_email_settings_form_three', 'WelcomeEmailSettingsController@FormThreeCreate')->name('welcome_email_settings_form_three');

            // Admin Panel OTP Email Settings Resource
            Route::resource('otp_email_settings', 'OTPEmailSettingsController');

            // Admin Panel Email Settings Forms POST Methods
            Route::post('otp_email_settings_form_one', 'OTPEmailSettingsController@FormOneCreate')->name('otp_email_settings_form_one');
            Route::post('otp_email_settings_form_two', 'OTPEmailSettingsController@FormTwoCreate')->name('otp_email_settings_form_two');
            Route::post('otp_email_settings_form_three', 'OTPEmailSettingsController@FormThreeCreate')->name('otp_email_settings_form_three');

            // Admin Panel Cancel Email Settings Resource
            Route::resource('cancel_email_settings', 'CancelEmailSettingsController');

            // Admin Panel Cancel Settings Forms POST Methods
            Route::post('cancel_email_settings_form_one', 'CancelEmailSettingsController@FormOneCreate')->name('cancel_email_settings_form_one');
            Route::post('cancel_email_settings_form_two', 'CancelEmailSettingsController@FormTwoCreate')->name('cancel_email_settings_form_two');
            Route::post('cancel_email_settings_form_three', 'CancelEmailSettingsController@FormThreeCreate')->name('cancel_email_settings_form_three');

            // Admin Panel Receipt Email Settings Resource
            Route::resource('receipt_email_settings', 'ReceiptEmailSettingsController');

            // Admin Panel Receipt Settings Forms POST Methods
            Route::post('receipt_email_settings_form_one', 'ReceiptEmailSettingsController@FormOneCreate')->name('receipt_email_settings_form_one');
            Route::post('receipt_email_settings_form_two', 'ReceiptEmailSettingsController@FormTwoCreate')->name('receipt_email_settings_form_two');
            Route::post('receipt_email_settings_form_three', 'ReceiptEmailSettingsController@FormThreeCreate')->name('receipt_email_settings_form_three');

            //  // Admin Panel Permission Controller Method
            Route::get('permission/control/{role_id?}', 'PermissionController@index')->name('permission.manage');

            // Admin Panel Permissions Add New POST method
            Route::post('permission/store', 'PermissionController@store')->name('permission.manage');

            //Admin Panel Settings Show Method
            Route::get('setting', 'SettingController@index')->name('setting');

            //Admin Panel Settings Update Method
            Route::post('settingUpdate', 'SettingController@store')->name('settingUpdate');
            // Company Panel Driver Details Method
            Route::get('getDriverDetail/{id}', 'DriverListController@showPassenger')->name('getDriverDetail');
            //Admin Panel LOGOUT Method
            Route::post('logout', 'LoginController@logout')->name('logout');
        });
    });

    // Company Panel Middleware and Routes
    Route::group(['namespace' => 'Company', 'middleware' => 'prevent-back-history', 'as' => 'company::', 'prefix' => 'company'], function () {

        // Company Panel Login Route
        Route::get('login', 'CompanyLoginController@showLoginForm')->name('login');

        // Company Panel Login POST Method
        Route::post('companyPostLogin', 'CompanyLoginController@login')->name('companyPostLogin');

        // Company Panel Middleware and Authentication
            Route::group(['middleware' => ['company','companyLanguage']], function () {
                // Company Panel Dashboard Route
                Route::get('company', 'CompanyController@index')->name('company');

                // Company Panel Profile Route
                Route::get('profile', 'CompanyController@companyProfile')->name('profile');

                // Company Panel Update Profile Method
                Route::post('updateProfile', 'CompanyController@updateProfile')->name('updateProfile');

                // Company Panel Change Theme Route / Method
                Route::get('changeThemes/{id}', 'CompanyController@changeThemes')->name('changeThemes');

                // Company Panel Change TimeZone Route

                 Route::post('TimeZoneSettings', 'CompanyController@TimeZoneSettings')->name('TimeZoneSettings');

                // Company Panel Change theme Mode Route / Method
                Route::get('changeThemesMode/{local}', 'CompanyController@changeThemesMode')->name('changeThemesMode');

                // Company Panel Drivers Resource
                Route::resource('driver', 'DriverController');

                // Company Panel Driver Change Status Method
                Route::get('driver/{id}/{status}', 'DriverController@status')->name('driverStatus');

                Route::get('changeDriverRegStatus/{id}/{status}', 'DriverController@changeDriverRegStatus')->name('changeDriverRegStatus');

                // Admin Panel Date Filter Method
                Route::post('getDateFilter', 'EarningAnalysisController@dateFilter')->name('getDateFilter');

                // Company Panel Driver Registration Method
                Route::get('driverRegistration/{id}', 'DriverController@driverRegistration')->name('driverRegistration');

                // Company Panel Driver Status Resource
                Route::resource('driverStatus', 'DriverStatusController');

                Route::resource('driverStatusReport', 'DriverStatusReportController');
             // get driver status filter data
             Route::post('getDriverStatusFilter', 'DriverStatusReportController@getDriverStatusFilter')->name('getDriversFilter');

                // Company Panel Driver Filter Method
                Route::post('getDriversFilter', 'DriverStatusController@getDriversDataByFilter')->name('getDriversFilter');

                // Company Panel Driver Records Method
                Route::get('getDriversRecord', 'DriverStatusController@getDriversData')->name('getDriversRecord');

                // Company Panel Driver Details Method
                Route::get('getDriversDetail/{driverId}', 'DriverStatusController@driverDetails')->name('getDriversDetail');

                // Company Panel Get Company Method
                Route::post('getCompanies', 'DriverStatusController@getCompanies')->name('getCompanies');

                // Company Panel Drivers Filter With Company Method
                Route::post('getCompanyDrivers', 'DriverStatusController@getCompanyDriversNumbers')->name('getCompanyDrivers');

                // Company Panel Drivers Filter Compare  Method
                Route::post('getCompanyDriversVeh', 'DriverStatusController@getCompanyDriversVehicles')->name('getCompanyDriversVeh');

                // Company Panel Drivers Registration Method
                Route::get('driverRegistration/{id}', 'DriverController@driverRegistration')->name('driverRegistration');

                // Company Panel Drivers Daily Earning Resource
                Route::resource('dailyEarning', 'DailyEarningController');

                // ledger report
            Route::resource('ledgerReport', 'LedgerReportController');
            // get passenger or driver for ledger report
            Route::post('getPassengerORDriver', 'LedgerReportController@getPassengerORDriver')->name('getPassengerORDriver');
            // get ledger report
            Route::post('getLedgerReport', 'LedgerReportController@getLedgerReport')->name('getLedgerReport');

                // Company Panel Drivers Invoices Resource
                Route::resource('earningAnalysis', 'EarningAnalysisController');

                // Company Panel Drivers Invoices Details Method
                Route::get('invoicesDetails/{invoiceId}', 'EarningAnalysisController@invoicesDetails')->name('invoicesDetails');

                // Company Panel Get Model Method
                Route::post('getModel', 'TransportModelController@getModel')->name('getModel');

                // Company Panel Get Make Method
                Route::post('getMake', 'TransportMakeController@getMake')->name('getMake');

                // Company Panel Get Model Color Method
                Route::post('getModelColor', 'TransportModelColorController@getModelColor')->name('getModelColor');

                // Company Panel Get Model Year Method
                Route::post('getModelYear', 'TransportModelYearController@getModelYear')->name('getModelYear');

                // Company Panel Add Driver Registration Method
                Route::post('addEditDriverRegistration', 'DriverController@addEditDriverRegistration')->name('addEditDriverRegistration');

                // Company Panel Edit Driver Registration Method
                Route::get('editDriverRegistration/{id}', 'DriverController@editDriverRegistration')->name('editDriverRegistration');

                // Company Panel Driver Details Method
                Route::get('getDriverDetail/{id}', 'DriverController@getDriverDetail')->name('getDriverDetail');

                // Company Panel Get Company Filtered Data Method
                Route::post('getCompanyDataFilterDashboard', 'CompanyController@getCompanyDataFilterDashboard')->name('getCompanyDataFilterDashboard');
                 // Company Panel Upcoming Schedule Rides Resource
                Route::resource('upcomingScheduleRides', 'UpcomingScheduledRidesController');

               // Company Panel InProgress Ride Booking Schedule Resource
                 Route::resource('inProgressScheduleRides', 'InProgressScheduleRidesController');
                 // Company Panel  Ride Booking Schedule Resource
                  Route::resource('rideBookingSchedule', 'RideBookingScheduleController');

            });

        // Company Panel Logout Method
        Route::post('companyLogout', 'CompanyLoginController@logout')->name('companyLogout');
    });
    // Authentication Routes
Auth::routes(['register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, ]);
    //Server Clear Cache Route and Method
    Route::get('/clear', function() {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:cache');
        echo "success";
    });

    Route::get('/job', function() {
        echo now();
        Artisan::call('bookRide:cron');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');
        echo "success";
    });Route::get('/jobar2', function() {
        Artisan::call('removeRequest:run');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');
        echo "success";
    });

    Route::get('/job1', function() {
    //    Artisan::call('schedule:run');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');
        $var = Utility::BookADriverBySchedule();
        dd($var);
        echo "success";
    });
    Route::get('/job2', function() {
    //    Artisan::call('schedule:run');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');
        $var = Utility::removeRequestedJobExpired();
        dd($var);
        echo "success";
    });
    Route::get('/noti/{token}', function($token) {
    //    Artisan::call('schedule:run');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');

        $var = \App\Notification\Notification::sendnotificationtome($token);
        dd($var);
        echo "success";
    });

    Route::get('/noti/email/test', function() {
    //    Artisan::call('schedule:run');
    //    Artisan::call('cache:clear');
    //    Artisan::call('route:clear');
    //    Artisan::call('config:cache');
    //    Artisan::call('key:generate');

        $rate_data_Email = [
            'drop_off_distance'=>36,
            'drop_off_time'=>36,
            'fare_rate_drop_off_distance'=>75,
            'fare_rate_drop_off_time'=>25,
            'before_pick_up_total_distance'=>36,
            'free_before_pick_up_total_distance'=>25,
            'before_pick_up_distance_charge'=>36,
            'before_pick_up_total_time'=>35,
            'free_before_pick_up_total_time'=>3,
            'before_pick_up_time_charge'=>1,
            'before_pick_up_total_distance_rate'=>15,
            'before_pick_up_total_time_rate'=>23,
            'wait_after_arrived'=>4,
            'wait_charges'=>5,
            'destination_final_KM_rate'=>65,
            'destination_final_time_rate'=>45,
            'destination_base_charges'=>52,
            'destination_total_with_out_pick_up_and_wait'=>25,
            'destination_total_pick_up'=>30,
            'destination_total_wait'=>20,
            'total_bill'=>10,
            'discount_if_voucher'=>0
        ];
        $var = \Illuminate\Support\Facades\Mail::to("nomowe5770@edmondpt.com")->send(new \App\Mail\RecieptEmailDetail("farhan",1,2,2,3,3,3,4,3,"","","","","","","",$rate_data_Email));
        dd($var);
        echo "success";
    });
