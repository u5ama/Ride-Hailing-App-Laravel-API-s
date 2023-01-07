<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Unicodeveloper\Identify\Identify;

class InviteFriendsController extends Controller
{
    public function inviteFriends(Request $request, $id)
    {
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
    }

    public function inviteDriver(Request $request, $id)
    {

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
    }
}
