<?php

namespace App\Http\Controllers\Dealer;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('dealer.admin.index');
    }

    public function changeThemes($id)
    {
        User::where('id', Auth::user()->id)->update(['panel_mode' => $id]);
        return redirect()->route('admin::admin');
    }

    public function changeThemesMode($local)
    {
        User::where('id', Auth::user()->id)->update(['locale' => $local]);
        return redirect()->route('admin::admin');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('admin/login');
    }
}
