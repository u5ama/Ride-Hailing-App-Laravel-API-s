<?php

namespace App\Http\Middleware;

use App\LanguageString;
use Closure;
use Illuminate\Support\Facades\App;
use Tymon\JWTAuth\Facades\JWTAuth;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Accept-Language') != '') {

            $token = JWTAuth::getToken();
            if ($token != null) {
                $user = JWTAuth::toUser($token);
                if(isset($user->user_type) && $user->user_type == "user") {
                    $user->locale = $request->header('Accept-Language');
                    $user->save();
                }
                $driver = \Auth::guard('driver')->user();
                if(isset($driver->user_type) && $driver->user_type == "driver") {
                    $driver->locale = $request->header('Accept-Language');
                    $driver->save();
                }


            }
            App::setLocale($request->header('Accept-Language'));
        }else{
            App::setLocale('en');
        }

        return $next($request);
    }
}
