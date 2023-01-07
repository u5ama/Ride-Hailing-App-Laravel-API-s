<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard="admin")
    {


        if(!auth()->guard($guard)->check()) {
            return redirect(route('admin::login'));
        }
        if(auth()->guard($guard)->user()->status=='Inactive') {
           return redirect()
                ->route('admin::login')
                ->withErrors(['email' => ['Your account is Inactive']])
                ->withInput();

        }

        return $next($request);
    }
}
