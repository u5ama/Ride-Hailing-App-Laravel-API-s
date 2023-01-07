<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard="company")
    {
        if(!auth()->guard($guard)->check()) {
            return redirect(route('company::login'));
        }
        if(auth()->guard($guard)->user()->com_status=='0') {
           return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is Inactive; contact support for further details']])
                ->withInput();

        }
        if(auth()->guard($guard)->user()->com_status=='1') {
           return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is temporary inactive; contact support for further details']])
                ->withInput();

        }
        if(auth()->guard($guard)->user()->com_status=='2') {
           return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is temporary block; contact support for further details']])
                ->withInput();

        }
        if(auth()->guard($guard)->user()->com_status=='3') {
           return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is permanenet block; contact support for further details']])
                ->withInput();

        }
        if(auth()->guard($guard)->user()->com_status=='4') {
           return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is pending for approval; contact support for further details']])
                ->withInput();

        }


        return $next($request);
    }
}
