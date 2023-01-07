<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('admin.setting.index', ['settings' => $settings]);
    }

    public function store(Request $request)
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            Setting::where('meta_key', $setting->meta_key)->update([
                'meta_value' => $request->input($setting->meta_key) == NULL ? 0 : $request->input($setting->meta_key)
            ]);
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.setting_updated')]);
    }
}
