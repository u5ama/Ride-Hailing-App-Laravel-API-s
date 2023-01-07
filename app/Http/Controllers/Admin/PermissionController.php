<?php

namespace App\Http\Controllers\Admin;


use App\Company;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Roles;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display a listing of the Permission.
     *
     * @param string $role_id
     * @return Factory|View
     */
    public function index($role_id = "")
    {
        $permission_list = array();
        $role_id = $role_id;
        if ($role_id != "") {
            $permission_list = Permission::where("role_id", $role_id)->pluck('permission')->toArray();
        }

        $notallowed = array(
            'App\Http\Controllers\Auth\LoginController',
            'App\Http\Controllers\Auth\RegisterController',
            'App\Http\Controllers\Auth\ForgotPasswordController',
            'App\Http\Controllers\Auth\ResetPasswordController',
            'App\Http\Controllers\Admin\AdminController',
            'App\Http\Controllers\Api\V1\UserController',
            'App\Http\Controllers\Api\V1\WebPageController',
            'App\Http\Controllers\Api\V1\BookARideController',
            'App\Http\Controllers\Api\V1\CustomerCreditCardController',
            'App\Http\Controllers\Api\V1\InvoiceController',
            'App\Http\Controllers\Api\V1\DriverController',
            'App\Http\Controllers\Api\V1\VehicleController',
            'App\Http\Controllers\Api\V1\LanguageController',
            'App\Http\Controllers\Api\V1\LanguageStringController',
            'App\Http\Controllers\Api\V1\AppReferenceController',
            'App\Http\Controllers\Api\V1\PageController',
            'App\Http\Controllers\Api\V1\PageContentController',
            'App\Http\Controllers\Api\V1\AppThemeController',
            'App\Http\Controllers\Api\V1\AppThemeDesignController',
            'App\Http\Controllers\Api\V1\AppSocialLinkController',
            'App\Http\Controllers\Api\V1\AppControlsController',
            'App\Http\Controllers\Api\V1\CountryController',
            'App\Http\Controllers\Api\V1\PromoCodeController',
            'App\Http\Controllers\Api\V1\AppNotificationController',
            'App\Http\Controllers\Api\V1\UpcomingRideController',
            'App\Http\Controllers\RegisterController',
            'App\Http\Controllers\HomeController',
            'App\Http\Controllers\ResetPasswordController',
            'App\Http\Controllers\ForgetPasswordController',
            'App\Http\Controllers\Admin\LoginController',
            'App\Http\Controllers\Admin\SettingController',
            'App\Http\Controllers\MapViewController',
            'App\Http\Controllers\Admin\InviteFriendsController',
            'App\Http\Controllers\Company\CompanyLoginController',
            'App\Http\Controllers\SQLQuriesController'
        );
        $ignoreRoute = array(
            'events.show',
            'notices.show',
        );

        $app = app();
        $routeCollection = $app->routes->getRoutes();
        $routes = [];
        // loop through the collection of routes
        foreach ($routeCollection as $route) {
            // get the action which is an array of items
            $action = $route->getAction();
            // if the action has the key 'controller'
            if (array_key_exists('controller', $action)) {
                // explode the string with @ creating an array with a count of 2
                $explodedAction = explode('@', $action['controller']);
                //If not needed so ignore
                if (in_array($explodedAction[0], $notallowed)) {
                    continue;
                }

                if (!isset($routes[$explodedAction[0]])) {
                    $routes[$explodedAction[0]] = [];
                }

                $test = new $explodedAction[0]();

                if (method_exists($test, $explodedAction[1])) {
                    $routes[$explodedAction[0]][] = array("method" => $explodedAction[1], "action" => $route->action);
                }
            }
        }
        $permission = array();
        foreach ($routes as $key => $route) {
            foreach ($route as $r) {
                if (strpos($r['method'], 'get') === 0) {
                    continue;
                }
                if (array_key_exists('as', $r['action'])) {
                    $routeName = $r['action']['as'];
                    //If not needed so ignore
                    if (in_array($routeName, $ignoreRoute)) {
                        continue;
                    }
                    $permission[$key][$routeName] = $r['method'];
                }
            }
        }



        foreach ($permission as $key => $val) {
            foreach ($val as $name => $url) {
                if ($url == "store" && in_array("create", $val)) {
                    unset($permission[$key][$name]);
                }
                if ($url == "update" && in_array("edit", $val)) {
                    unset($permission[$key][$name]);
                }
            }
        }
        return view('admin.permissions.index', compact('permission', 'permission_list', 'role_id'));
    }

    /**
     * Add Store for Permission
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required',
            'permissions' => 'required'
        ]);
        $permission = Permission::where("role_id", $request->role_id);
        $permission->delete();
        foreach ($request->permissions as $role) {
            $permission = new Permission();
            $permission->role_id = $request->role_id;
            $permission->permission = $role;
            $permission->save();
        }
        return redirect('admin/permission/control')->with('success', 'Saved Successfully');
    }
}
