<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// user Authentication route
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// send notification to all user
Route::get('sendnotificationall',function (){
   \App\Notification\Notification::sendnotificationall();
});
// send notification topic wise to all drivers
Route::get('sendnotificationtopic',function (){
    $topic = 'all_drivers';
   \App\Notification\Notification::sendnotificationtopic($topic);
});
// transaction route
Route::get('trans',function (){
    $user = \App\User::find(151);
    $var = \App\Utility\Utility::transID($user);
    dd($var);
});


Route::prefix('v1')->name('api.v1.')->namespace('Api\V1')->group(function () {
    // Middleware language check
    Route::post('area',function (Request $request){

        $var = \App\Utility\Utility::restrictedArea($request);
        dd($var);
    });
    Route::group(['middleware' => 'languageCheck'], function () {
        // create a new user using post method
        Route::post('signUp', 'UserController@signUp')->name('signUp');
        // user login route
        Route::post('login', 'UserController@login')->name('login');
        // user refresh token using get method
        Route::get('refreshToken', 'UserController@refreshToken')->name('refreshToken');
        // forget password POST Method
        Route::post('forgotPassword', 'UserController@forgotPassword')->name('forgotPassword');
        //user Reset Password POST Method
        Route::post('resetPassword', 'UserController@resetPassword')->name('resetPassword');
         // Middleware jwt.verify for auth
        Route::group(['middleware' => ['jwt.verify']], function () {
            // user edit profile
            Route::post('editProfile', 'UserController@editProfile')->name('editProfile');
            // user update the language
            Route::get('updateLocale', 'UserController@updateLocale')->name('updateLocale');
            // Verification Email Passenger route
            Route::get('verifyEmailPassenger', 'UserController@verifyEmailPassenger')->name('verifyEmailPassenger');
            // passenger create address using post method
            Route::post('passengerAddress', 'UserController@passengerAddress')->name('passengerAddress');
            // Passenger update address using post method
            Route::put('passengerAddressUpdate', 'UserController@passengerAddressUpdate')->name('passengerAddressUpdate');
            // Passenger get address using get method
            Route::get('getPassengerAddress', 'UserController@getPassengerAddress')->name('getPassengerAddress');
            // get passenger Recent Address using get mehtod
            Route::get('getPassengerAddressRecent', 'UserController@getPassengerAddressRecent')->name('getPassengerAddressRecent');
            // get Passenger Reviews route using get method
            Route::get('getPassengerReviews', 'UserController@getPassengerReviews')->name('getPassengerReviews');
            // create driver rating route using post method
            Route::post('driverRating', 'UserController@driverRating')->name('driverRating');
            // get user contact list to show
            Route::get('getContactList', 'UserController@getContactList')->name('getContactList');
            // passenger address is deleted by passenger id
            Route::delete('passengerAddressDestroy/{id}', 'UserController@passengerAddressDestroy')->name('passengerAddressDestroy');
            // get passenger profile route
            Route::get('myPassengerProfile', 'UserController@myProfile')->name('myProfile');
            // passenger logout route
            Route::get('logoutPassenger', 'UserController@logout')->name('logoutPassenger');
            // create book a ride using get ride route
            Route::post('getRide', 'BookARideController@getRide')->name('getRide');
            // create book a ride using get ride1 route
            Route::post('getRide1', 'BookARideController@getRide1')->name('getRide1');
            // get Cancel Reasoning Passenger route
            Route::get('getCancelReasoningPassenger', 'BookARideController@getCancelReasoningPassenger')->name('getCancelReasoningPassenger');
            // get drivers  using post method
            Route::post('getDrivers', 'BookARideController@getDrivers')->name('getDrivers');
            // create Ride Booking Schedule
            Route::post('bookARide', 'BookARideController@bookARide')->name('bookARide');
           // get driver time and distance using post method
            Route::post('getDriversTimeAndDistance', 'BookARideController@getDriversTimeAndDistance')->name('getDriversTimeAndDistance');
         // get passenger booked rides list
            Route::get('getPassengerBookedRides', 'BookARideController@getPassengerBookedRides')->name('getPassengerBookedRides');
            //passenger booked rides details using Get method
            Route::get('getPassengerBookedRideDetails', 'BookARideController@getPassengerBookedRideDetail')->name('getPassengerBookedRideDetails');
             // get my ride Driver by id
            Route::get('getMyRideDriver/{id}', 'BookARideController@getMyRideDriver')->name('getMyRideDriver');
            // user delete profile picture
            Route::delete('deleteProfilePic', 'UserController@deleteProfilePic')->name('deleteProfilePic');
            // Customer create Credit Card
            Route::post('createCreditCard', 'CustomerCreditCardController@createCreditCard')->name('createCreditCard');
            //get customer credit card list
            Route::get('getCreditCards', 'CustomerCreditCardController@getCreditCards')->name('getCreditCards');
            // delete Credit Card by customer id
            Route::delete('deleteCreditCards/{id}', 'CustomerCreditCardController@deleteCreditCards')->name('deleteCreditCards');
            // Cancel Ride By Passenger using post Method
            Route::post('CancelRideByPassenger/{id}', 'BookARideController@CancelRideByPassenger')->name('CancelRideByPassenger');
             // Cancel Ride By Passenger using post Method
            Route::post('CancelRideByPassengerBeforeRide/{id}', 'BookARideController@CancelRideByPassengerBeforeRide')->name('CancelRideByPassengerBeforeRide');
            // Create Invoice using Cash Payment Route
            Route::post('cashPayment', 'InvoiceController@cashPayment')->name('cashPayment');
            // top Up Wallet using post method
            Route::post('topUpWallet', 'InvoiceController@topUpWallet')->name('topUpWallet');
            //  top Up Wallet Success route
            Route::post('topUpWalletSuccess', 'InvoiceController@topUpWalletSuccess')->name('topUpWalletSuccess');
            // create redeem promo code
            Route::post('redeemPromoCode', 'PromoCodeController@redeemPromoCode')->name('redeemPromoCode');
            // create redeem Voucher code
            Route::post('redeemVoucherCode', 'PromoCodeController@redeemVoucherCode')->name('redeemVoucherCode');
            //get voucher code route using post method
            Route::post('getVoucherCode', 'PromoCodeController@getVoucherCode')->name('getVoucherCode');
            // get push notification list
            Route::get('getNotifications', 'AppNotificationController@getNotifications')->name('getNotifications');
            // get hide Notification list
            Route::get('hideNotification/{id}', 'AppNotificationController@hideNotification')->name('hideNotification');
            // get seen and unseen notification list
            Route::get('SeenUnseenNotification/{id}', 'AppNotificationController@SeenUnseenNotification')->name('SeenUnseenNotification');
            // completed Job By Passenger using id
            Route::get('completedJobByPassenger/{id}', 'BookARideController@completedJobByPassenger')->name('completedJobByPassenger');

              // Upcoming Ride route
            Route::post('bookUpcomingRide', 'UpcomingRideController@bookUpcomingRide')->name('bookUpcomingRide');
            Route::post('bookUpcomingRide1', 'UpcomingRideController@bookUpcomingRide1')->name('bookUpcomingRide1');
            // get Passenger Scheduled Rides list
            Route::get('getPassengerScheduledRides', 'UpcomingRideController@getPassengerScheduledRides')->name('getPassengerScheduledRides');
            // get Cancel Schedule Reasoning Passenger list
            Route::get('getCancelScheduleReasoningPassenger', 'UpcomingRideController@getCancelScheduleReasoningPassenger')->name('getCancelScheduleReasoningPassenger');
            // cancel Scheduled Ride route
            Route::post('cancelScheduledRide/{id}', 'UpcomingRideController@cancelScheduledRide')->name('cancelScheduledRide');


            //driver  --------------------------------------------------------------------------------------------------------
           // driver middleware
            Route::group(['middleware' => ['drivers']], function () {
                // get Driver Menu
                Route::get('getDriverMenu', 'DriverController@getDriverMenu')->name('getDriverMenu');
                // driver edit profile route
                Route::post('driverEditProfile', 'DriverController@driverEditProfile')->name('driverEditProfile');
                // driver update language or locale
                Route::get('updateLocaleDriver', 'DriverController@updateLocaleDriver')->name('updateLocaleDriver');
                // get my driver profile
                Route::get('myDriverProfile', 'DriverController@myProfile')->name('myProfile');
                // get driver reviews list
                Route::get('getDriverReviews', 'DriverController@getDriverReviews')->name('getDriverReviews');
                // create passenger rating
                Route::post('passengerRating', 'DriverController@passengerRating')->name('passengerRating');
                // Create driver Registration Images
                Route::post('driverRegistrationImages', 'DriverController@driverRegistrationImages')->name('driverRegistrationImages');
                // delete Driver Registration Images using id
                Route::delete('deleteDriverRegistrationImages/{id}', 'DriverController@deleteDriverRegistrationImages')->name('deleteDriverRegistrationImages');
                // Logout Driver route
                Route::get('logoutDriver', 'DriverController@logout')->name('logoutDriver');
                // Get Driver Profile
                Route::get('getDriverProfile', 'DriverController@getDriverProfile')->name('getDriverProfile');
                // Delete  Driver Profile Picture
                Route::delete('deleteProfilePicDriver', 'DriverController@deleteProfilePic')->name('deleteProfilePicDriver');
                // Create Driver Registration
                Route::post('driverRegistration', 'DriverController@DriverRegistration')->name('driverRegistration');
                // get Transport Type list
                Route::get('getTransportType', 'CarRegistrationController@getTransportType')->name('getTransportType');
                // get Transport Data list
                Route::get('getTransportData', 'CarRegistrationController@getTransportData')->name('getTransportData');
                // get Transport Fuel list
                Route::get('getTransportFuel', 'CarRegistrationController@getTransportFuel')->name('getTransportFuel');
                // create driver Current Location
                Route::post('driverCurrentLocation', 'DriverController@driverCurrentLocation')->name('driverCurrentLocation');
                // Get Job list
                Route::get('getJob', 'BookARideController@getJob')->name('getJob');
                // Accept Ride Job
                Route::get('acceptJob/{id}', 'BookARideController@acceptJob')->name('acceptJob');
                // Cancel Ride Job
                Route::post('cancelJob/{id}', 'BookARideController@cancelJob')->name('cancelJob');
                // Reject Ride Job
                Route::get('rejectJob/{id}', 'BookARideController@rejectJob')->name('rejectJob');
                // Update PickUp Location Ride Job
                Route::post('updatePickUpLocation', 'BookARideController@updatePickUpLocation')->name('updatePickUpLocation');
                // Update Drop Off Location Ride Job
                Route::post('updateDropOffLocation', 'BookARideController@updateDropOffLocation')->name('updateDropOffLocation');
                // Driver is waiting route
                Route::get('driverIsWaiting/{id}', 'BookARideController@driverIsWaiting')->name('driverIsWaiting');
                // Completed ride Job
                Route::get('completedJob/{id}', 'BookARideController@completedJob')->name('completedJob');
                // Completed Ride Job route
//                Route::get('completedJob1/{id}', 'BookARideController@completedJob1')->name('completedJob1');
                // Start Driving route
                Route::get('startDriving/{id}', 'BookARideController@startDriving')->name('startDriving');
                // Get Cancel Reasoning Driver list
                Route::get('getCancelReasoningDriver', 'BookARideController@getCancelReasoningDriver')->name('getCancelReasoningDriver');
                // pay Bill in invoice
                Route::post('payBill', 'InvoiceController@payBill')->name('payBill');
                // get Driver Booked Rides route
                Route::get('getDriverBookedRides', 'BookARideController@getDriverBookedRides')->name('getDriverBookedRides');
            });
            // get Driver Wallet list
            Route::get('getDriverWallet', 'DriverController@getDriverWallet')->name('getDriverWallet');

        });

         // search route
        Route::post('search', 'VehicleController@index')->name('search');
        // vehicle Details route
        Route::get('vehicleDetails/{id}', 'VehicleController@show')->name('vehicleDetails');

       // User Register Mobile Number
        Route::post('registerMobileNumber', 'UserController@registerMobileNumber')->name('registerMobileNumber');
        // Create  verify Code
        Route::post('verifyCode', 'UserController@verifyCode')->name('verifyCode');
         // Create your Identity
        Route::post('yourIdentity', 'UserController@yourIdentity')->name('yourIdentity');
        // Get Verify Code
        Route::get('getVerifyCode', 'UserController@getVerifyCode')->name('getVerifyCode');
        // Passenger Menu list
        Route::get('getPassengerMenu', 'UserController@getPassengerMenu')->name('getPassengerMenu');


        // dirver route ----------------------------------------------------------------------------------------

        // Create driver Register Mobile Number
        Route::post('driverRegisterMobileNumber', 'DriverController@driverRegisterMobileNumber')->name('driverRegisterMobileNumber');
        // reate driver Verify Code
        Route::post('driverVerifyCode', 'DriverController@driverVerifyCode')->name('driverVerifyCode');
        // Create driver Your Identity
        Route::post('driverYourIdentity', 'DriverController@driverYourIdentity')->name('driverYourIdentity');
        // driver Get Verify Code
        Route::get('driverGetVerifyCode', 'DriverController@driverGetVerifyCode')->name('driverGetVerifyCode');

        //end driver route------------------------------------------------------------------------------------------

        // get languages list
        Route::get('languages', 'LanguageController@index')->name('languages');
        // get language string list by dirver or passenger
        Route::get('languageString/{driver_or_passenger}', 'LanguageStringController@index')->name('languageString');
        // get app default images list
        Route::get('getImages', 'AppDefaultImagesController@getimages')->name('getimages');
        // get app references list
        Route::get('references', 'AppReferenceController@index')->name('references');
        // get pages by app type
        Route::get('pages/{appType}', 'PageController@index')->name('pages');
        // get all pages by app type
        Route::get('allPages/{appType}', 'PageController@settingPages')->name('allPages');
       // get page details
        Route::get('pageDetails', 'PageContentController@index')->name('pageDetails');


        Route::get('webpages/{appType}', 'WebPageController@index')->name('webpages');
        // get page details
        Route::get('webPageDetails', 'WebPageContentController@index')->name('webPageDetails');


        // get app theme list
        Route::get('appThemes', 'AppThemeController@index')->name('appThemes');
       // get app theme design list
        Route::get('appThemeDesign', 'AppThemeDesignController@index')->name('appThemeDesign');
        // get app social links list
        Route::get('appSocialLinks', 'AppSocialLinkController@index')->name('appSocialLinks');
        // get app controls list
        Route::get('appControls', 'AppControlsController@index')->name('appControls');
        // get countries list
        Route::get('countries', 'CountryController@index')->name('countries');
    });
    // get Ride Updated Address by id
    Route::get('getRideUpdatedAddress/{id}', 'BookARideController@getRideUpdatedAddress')->name('getRideUpdatedAddress');
});

//-------------------------------TestFirBaseRoute-------------------------------------
// firbase test route
Route::post('testfirebase', function ($id = 30){

        $database = \Kreait\Laravel\Firebase\Facades\FirebaseDatabase::getReference('tracking/'.$id)->set([
            'rbs_driver_id'=>204,
            'rbs_passenger_id'=>310,
            'rbs_driver_lat'=>31.87,
            'rbs_driver_long'=>3265,
            'rbs_transport_id'=>1,
            'rbs_transport_type'=>"Economy",
            'rbs_source_lat'=>13.56,
            'rbs_source_long'=>73.65,
            'rbs_destination_lat'=>73.125,
            'rbs_destination_long'=>36574,
            'rbs_destination_distance'=>24,
            'rbs_destination_time'=>124,
            'rbs_ride_status'=>'Driving',

        ]);
        $val = $database->getSnapshot()->getValue();
        dd($val);


    });
