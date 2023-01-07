<?php

namespace App\Providers;

use App\AppSmtpSetting;
use App\Http\Resources\AppControlsResource;
use App\Http\Resources\AppDefaultImagesResource;
use App\Http\Resources\AppSocialLinkResource;
use App\Http\Resources\AppThemeDesignResource;
use App\Http\Resources\AppThemeResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\LangaugeResource;
use App\Http\Resources\LanguageStringResource;
use App\Http\Resources\PageContenctResource;
use App\Http\Resources\PageResource;
use App\LanguageString;
use App\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        LanguageStringResource::withoutWrapping();
        AppControlsResource::withoutWrapping();
        AppSocialLinkResource::withoutWrapping();
        AppThemeDesignResource::withoutWrapping();
        AppThemeResource::withoutWrapping();
        CountryResource::withoutWrapping();
        LangaugeResource::withoutWrapping();
        PageContenctResource::withoutWrapping();
        PageResource::withoutWrapping();
        AppDefaultImagesResource::withoutWrapping();

//        if (Schema::hasTable('app_smtp_settings')) {
//            $SMTP = AppSmtpSetting::where('smtp_status', 1)->first();
//            if ($SMTP) {
//                Config::set('mail.driver', $SMTP->MAIL_DRIVER);
//                Config::set('mail.host', $SMTP->MAIL_HOST);
//                Config::set('mail.port', $SMTP->MAIL_PORT);
//                Config::set('mail.username', $SMTP->MAIL_USERNAME);
//                Config::set('mail.password', $SMTP->MAIL_PASSWORD);
//                Config::set('mail.encryption', $SMTP->MAIL_ENCRYPTION);
//                Config::set('mail.from.address', $SMTP->MAIL_FROM_ADDRESS);
//            }
//        }
//        if (Schema::hasTable('language_strings')) {
//            $response = LanguageString::listsTranslations('name')
//                ->select('language_strings.name_key', 'language_string_translations.name')
//                ->get();
//            foreach ($response as $setting) {
//                //dd($setting->name_key);
//                Config::set('languageString.' . $setting->name_key, $setting->name);
//            }
//        }
//
        if (Schema::hasTable('base_language_strings')) {
            $response = LanguageString::all();
            foreach ($response as $setting) {
               // dd($setting->name);
                Config::set('languageString.' . $setting->bls_name_key, $setting->name);
            }
        }
    }
}
