<?php

namespace App\Http\Middleware;

use App\LanguageString;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class AdminLanguage
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
        if (auth()->guard('admin')->user()->locale == 'ar') {
            App::setLocale('ar');
        }

        if (Schema::hasTable('base_language_strings')) {
            $response = LanguageString::all();
            foreach ($response as $setting) {
                // dd($setting->name);
                Config::set('languageString.' . $setting->bls_name_key, $setting->name);
            }
        }
        return $next($request);
    }
}
