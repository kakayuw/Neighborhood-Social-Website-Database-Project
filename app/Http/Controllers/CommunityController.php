<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;


class CommunityController extends Controller
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
        $uid = Auth::user()->uid;
        $allblocks = DB::table("locality as l")      
                    ->where('ltype', '=', 'block')  
                    ->join('address as a1', 'l.southwest', '=', 'a1.addrid')
                    ->join('address as a2', 'l.northeast', '=', 'a2.addrid')
                    ->select('l.lid', 'l.ltype', 'l.name', 'l.description', 'a1.lat as swlat', 'a1.lng as swlng', 'a2.lat as nelat', 'a2.lng as nelng', 'a1.address')
                    -> get();
        $allhoods = DB::table("locality")
                    ->where('ltype', '=', 'hood')
                    -> get();
        $ismember = DB::table("block_memb as bm")      
                    ->where([
                        ['uid', '=', $uid],
                        ['active', '=', 'valid'],
                    ])
                    ->join("locality as l", "l.lid", "=", "bm.bid")
                    ->selectRaw("bm.bid, Datediff(now(), bmtimestamp) as ago")
                    ->first();
        $member_lid = $ismember ? $ismember->bid : -1;
        $myblock = DB::table("locality as l")      
                    ->where('ltype', '=', 'block')  
                    ->join('address as a1', 'l.southwest', '=', 'a1.addrid')
                    ->join('address as a2', 'l.northeast', '=', 'a2.addrid')
                    ->where('l.lid', '=', $member_lid)
                    ->select('l.lid', 'l.ltype', 'l.name', 'l.description', 'a1.lat as swlat', 'a1.lng as swlng', 'a2.lat as nelat', 'a2.lng as nelng', 'a1.address')
                    ->get();
        return view('community',  ['blocks' => $allblocks, 'hoods' => $allhoods, 'ismemb' => $ismember, 'myblock' => $myblock]);
    }

    /**
     * Show the request and response page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reqres()
    {
        $uid = Auth::user()->uid;
        $myreq = DB::table("block_req as br")      
                    ->where([
                        ['uid', '=', $uid],
                        ['status', '=', 'processing'],
                    ])
                    ->first();
        $req_lid = $myreq ? $myreq->lid : -1;
        $myreqblock = DB::table("locality as l")      
                    ->where('ltype', '=', 'block')  
                    ->join('address as a1', 'l.southwest', '=', 'a1.addrid')
                    ->join('address as a2', 'l.northeast', '=', 'a2.addrid')
                    ->where('l.lid', '=', $req_lid)
                    ->select('l.lid', 'l.ltype', 'l.name', 'l.description', 'a1.lat as swlat', 'a1.lng as swlng', 'a2.lat as nelat', 'a2.lng as nelng', 'a1.address')
                    ->get();
        $ismember = DB::table("block_memb as bm")      
                    ->where([
                        ['uid', '=', $uid],
                        ['active', '=', 'valid'],
                    ])
                    ->join("locality as l", "l.lid", "=", "bm.bid")
                    ->first();
        $otherreq = DB::table("block_req as mybr")
                    ->where([
                        ['mybr.uid', '=', $uid],
                        ['mybr.status', '=', 'approved'],
                    ])
                    ->select('lid')
                    ->join("block_req as br", 'mybr.lid', '=', 'br.lid')
                    ->where([
                        ['br.uid', '!=', $uid],
                        ['br.status', '=', 'processing'],
                    ])
                    ->whereRaw("br.uid not in (select uid from block_apv where apverid = ?)",$uid)  // who voted cannot voted again
                    ->select("br.uid", "br.lid", "br.brtimestamp", "br.status", "br.numb4apv")
                    ->join("user", "user.uid", "=", "br.uid")
                    ->join("locality as l", 'l.lid', '=', 'br.lid')
                    ->selectRaw("mybr.uid as appver, user.name, user.email, user.uid, l.name as lname, br.lid, br.brtimestamp, Datediff(now(), br.brtimestamp) as ago")
                    ->get();
        return view('comu_join',  ['myreq' => $myreq, 'otherreq' => $otherreq, 'myreqblock' => $myreqblock, 'ismemb' => $ismember]);
    }




    /**
     * One membership approve other's request
     *
     */
    public function aprvother(Request $request)
    {
        $uid = $request->input('uid');
        $lid = $request->input('lid');
        $appver = $request->input('appver');
        $brtime = DB::table("block_req as br")
                    ->where([
                        ['br.uid', '=', $uid],
                        ['br.lid', '=', $lid],
                        ['br.status', '=', 'processing'],
                    ])
                    ->value('brtimestamp');
        echo $uid, $lid, $appver, $brtime;
        DB::table('block_apv')->insert(
            ['uid' => $uid, 'lid' => $lid, 'brtimestamp' => $brtime, 'apverid' => $appver, 'apvtime' => Carbon::now()]
        );
    
    }


    /**
     * One members quit his block
     *
     */
    public function quitblock(Request $request)
    {
        $uid = $request->input('uid');
        $lid = $request->input('lid');
        DB::table('block_memb')
            ->where([
                ['uid', '=', $uid],
                ['bid', '=', $lid],
                ['active', '=', 'valid']
            ])
            ->update(['active' => 'outdated', 'leavetime' => Carbon::now()]);
    }



    /**
     * Show the block move out history dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $uid = Auth::user()->uid;
        $ismember = DB::table("block_memb as bm")      
                    ->where([
                        ['uid', '=', $uid]
                    ])
                    ->join("locality as l", "l.lid", "=", "bm.bid")
                    ->selectRaw("bm.bid, bm.bmtimestamp, Datediff(now(), bmtimestamp) as ago")
                    ->first();
        $blocktime = DB::table("block_memb as bm")
                    ->where('uid', '=', $uid)
                    ->join("locality as l", "l.lid", "=", "bm.bid")
                    ->join('address as a1', 'l.southwest', '=', 'a1.addrid')
                    ->select('l.lid', 'l.ltype', 'l.name', 'l.description', 'a1.address', 'bm.bmtimestamp', 'bm.leavetime')
                    ->get();
        return view('comu_history',  ['usedmember' => $ismember, 'blocktime' => $blocktime]);
    }
}
