<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile');
    }

    // Get my information 
    public function getmyinfo(Request $request) {
        $uid = Auth::user()->uid;
        $type = $request->input('type');
        $myaddr = '';
        $myprofile = '';
        if ( $type == "addr" ) {
            $myaddr = DB::table("profile as p")
                        ->leftjoin("address as a", "p.address", "a.addrid")
                        ->where("p.uid", "=", $uid)
                        ->select("a.name", "a.address", "a.lat", "a.lng")
                        ->first();
        } elseif ( $type == "profile" ) {
            $myprofile = DB::table("profile as p")
                        ->leftjoin("address as a", "p.address", "a.addrid")
                        ->where("p.uid", "=", $uid)
                        ->select("a.name as aname", "a.address", "a.lat", "a.lng", "p.uid as uid","p.name as uname", "p.phone", "p.description")
                        ->first(); 
        }
        return response()->json(['address' => $myaddr, 'profile' => $myprofile]);
    }

    // Update my profile and address
    public function updatemyinfo(Request $request) {
        $uid = Auth::user()->uid;
        $name = $request->input('name');
        $phone = $request->input('phone');
        $desc = $request->input('desc');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $address = $request->input('address');
        $newAddrid = DB::table('address')->insertGetId(
            ['address' => $address, 'lat' => floatval($lat), 'lng' => floatval($lng), 'type' => "chosenFromMap"]
        );
        DB::table('profile')
        ->where([['uid', '=', $uid]])
        ->update(['name' => $name, 'phone' => $phone, 'description' => $desc, 'address' => $newAddrid]);
    }
}
