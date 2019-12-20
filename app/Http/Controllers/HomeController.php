<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uid = Auth::user()->uid;
        $lid = DB::table("block_memb")      
                ->where([
                    ['uid', '=', $uid],
                    ['active', '=', 'valid']
                ])
                ->whereNull('leavetime')
                ->distinct()
                ->value("bid");
        $lid = isset($lid)? $lid : -1;
        $blockstranger = DB::table("block_memb as bm")      
                        ->where([
                            ['bm.uid', '!=', $uid],
                            ['bm.bid', '=', $lid],
                            ['bm.active', '=', 'valid'],
                        ])
                        ->join("locality as l", "l.lid", "=", "bm.bid")
                        ->join("user as u", "bm.uid", '=', "u.uid")
                        ->join("profile as p", "p.uid", "=", "u.uid")
                        ->select('bm.uid', 'l.ltype', 'l.name as lname', 'u.name as uname', 'u.email', "p.phone", "p.description")
                        ->get();
        $friendreq = DB::table("friend as f")
                    ->where([
                        ['uid1', '=', $uid],
                        ['status', '=', 'valid']
                    ])
                    ->join("user as u", "u.uid", "uid2")
                    ->join("profile as p", "p.uid", "=", "u.uid")
                    ->leftjoin("block_memb as bm", "bm.uid", "=", "u.uid")
                    ->leftjoin("locality as l", "l.lid", "=", "bm.bid")
                    ->select('bm.uid', 'l.ltype', 'l.name as lname', 'u.name as uname', 'u.email', "p.phone", "p.description");
        $friendall = DB::table("friend as f")
                    ->where([
                        ['uid2', '=', $uid],
                        ['status', '=', 'valid']
                    ])
                    ->join("user as u", "u.uid", "uid1")
                    ->join("profile as p", "p.uid", "=", "u.uid")
                    ->leftjoin("block_memb as bm", "bm.uid", "=", "u.uid")
                    ->leftjoin("locality as l", "l.lid", "=", "bm.bid")
                    ->select('bm.uid', 'l.ltype', 'l.name as lname', 'u.name as uname', 'u.email', "p.phone", "p.description")
                    ->unionAll($friendreq)
                    ->get();
        $neighbor = DB::table("neighbor as n")
                    ->where([
                        ['uid1', '=', $uid]
                    ])
                    ->join("user as u", "n.uid2", "=", "u.uid")
                    ->join("profile as p", "p.uid", "=", "u.uid")
                    ->select('u.uid', 'u.name as uname', 'u.email', "p.phone", "p.description")
                    ->get();
        $neighborof = DB::table("neighbor as n")
                    ->where([
                        ['uid2', '=', $uid]
                    ])
                    ->join("user as u", "n.uid1", "=", "u.uid")
                    ->join("profile as p", "p.uid", "=", "u.uid")
                    ->select('u.uid', 'u.name as uname', 'u.email', "p.phone", "p.description")
                    ->get();
        $myfriendreq = DB::table("friend as f")
                    ->where([
                        ['uid1', '=', $uid],
                        ['status', '=', 'processing']
                    ])
                    ->join("user as u", "u.uid", '=',"f.uid2")
                    ->select('u.name as uname', 'u.email', "u.uid")
                    ->get();
        $recvreq = DB::table("friend as f")
                    ->where([
                        ['uid2', '=', $uid],
                        ['status', '=', 'processing']
                    ])
                    ->join("user as u", "u.uid", '=',"f.uid1")
                    ->select('u.name as uname', 'u.email', "u.uid")
                    ->get(); 
        return view('home', ['lid' => $lid, 'blockstranger' => $blockstranger, 'friend' => $friendall,
              'neighbor' => $neighbor, 'neighborof' => $neighborof,'myfriendreq' => $myfriendreq, 'recvreq' => $recvreq
            ]);
    }


    public function test(Request $request) {
        // echo bcrypt('pwd1');
        $uid = Auth::user()->uid;

        $friendreq = DB::table("friend as f")
        ->where([
            ['uid1', '=', $uid],
            ['status', '=', 'valid']
        ])
        ->join("user as u", "u.uid", "uid2")
        // ->join("profile as p", "p.uid", "=", "u.uid")
        ->get();
        // ->leftjoin("block_memb as bm", "bm.uid", "=", "u.uid")
        // ->leftjoin("locality as l", "l.lid", "=", "bm.bid")
        // ->select('bm.uid', 'l.ltype', 'l.name as lname', 'u.name as uname', 'u.email', "p.phone", "p.description");
        // $friendall = DB::table("friend as f")
        // ->where([
        //     ['uid2', '=', $uid],
        //     ['status', '=', 'valid']
        // ])
        // // ->join("user as u", "u.uid", "uid1")
        // // ->join("profile as p", "p.uid", "=", "u.uid")
        // // ->leftjoin("block_memb as bm", "bm.uid", "=", "u.uid")
        // // ->leftjoin("locality as l", "l.lid", "=", "bm.bid")
        // // ->select('bm.uid', 'l.ltype', 'l.name as lname', 'u.name as uname', 'u.email', "p.phone", "p.description")
        // ->unionAll($friendreq)
        // ->get();
        echo $friendreq;
    }

}
