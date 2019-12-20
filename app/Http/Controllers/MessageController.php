<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
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
        // 7 day's reccent message
        $RecentGroupMessage = DB::table('thread as t')
                            ->where([['t.coveragetype', '=', 'group']])
                            ->join('message as m', 't.thdid', '=', 'm.thdid')
                            ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
                            ->whereNull('mm.leavetime')
                            ->where("mm.uid", '=', $uid)
                            ->whereRaw('m.mtimestamp >= mm.jointime and datediff(m.mtimestamp, now()) <= 7')
                            ->join("user as u", "u.uid", '=', 'm.author')
                            ->join("address as a", "a.addrid", "=", "m.address")
                            ->select("u.name as uname", "m.mid", "m.title", "m.text", "m.replyto", "m.mtimestamp", 'a.lat', 'a.lng')
                            ->get();
        $blocknotif = DB::table('block_memb as bm')
                            ->join('thread as t', 't.sender', '=', 'bm.bid')
                            ->join('locality as l', 'l.lid', '=', 'bm.bid')
                            ->join('localmsg as m', 'm.thdid', '=', 't.thdid')
                            ->whereRaw('bm.uid = '.$uid.' and t.coveragetype = l.ltype and datediff(m.ltimestamp, now()) <= 7')
                            ->whereNull('bm.leavetime')
                            ->where([['bm.bmtimestamp', '<', 'm.ltimestamp']])
                            ->select("m.mid", "m.title", "m.text", "m.ltimestamp")
                            ->get();
        $blockMessage = DB::table('block_memb as bm')
                            ->join('thread as t', 't.sender', '=', 'bm.bid')
                            ->where([['bm.uid', '=', $uid], ['t.coveragetype', '=', 'broadcast']])
                            ->join('message as m', 'm.thdid', '=', 't.thdid')
                            ->whereNull('bm.leavetime')
                            ->where([['bm.bmtimestamp', '<', 'm.mtimestamp']])
                            ->join("address as a", "a.addrid", '=', 'm.address' )
                            ->join("user as u", "u.uid", '=', 'bm.uid')
                            ->whereRaw('datediff(m.mtimestamp, now()) <= 7')
                            ->select('t.thdid', 'm.mid', 'm.title', 'm.text', 'm.replyto', 'm.mtimestamp', 'a.address', 'a.lat', 'a.lng', "u.uid", "u.name")
                            ->get();
        return view('message', ['recentgroup' => $RecentGroupMessage
        , 'recentblock' => $blockMessage, 'recentnotif' => $blocknotif
        ]);
    }

    // Create a map view for end user
    public function mapview()
    {
        $uid = Auth::user()->uid;
        $RecentGroupMessage = DB::table('thread as t')
        ->where([['t.coveragetype', '=', 'group']])
        ->join('message as m', 't.thdid', '=', 'm.thdid')
        ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
        ->whereNull('mm.leavetime')
        ->where("mm.uid", '=', $uid)
        ->whereRaw('m.mtimestamp >= mm.jointime and datediff(m.mtimestamp, now()) <= 7')
        ->join("user as u", "u.uid", '=', 'm.author')
        ->join("address as a", "a.addrid", '=', 'm.address' )
        ->select("u.name as uname", "m.mid", "m.title", "m.text", "m.replyto", "m.mtimestamp", 'a.lat', 'a.lng')
        ->get();
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
                ->first();
        return view('map', ['recentgroup' => $RecentGroupMessage, 'myblock' => $myblock]);
    }

    /**
     * Show the Threads of categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function threadCenter()
    {
        $uid = Auth::user()->uid;
        $previous = DB::table('block_memb as bm')
        ->join('thread as t', 't.sender', '=', 'bm.bid')
        ->join('locality as l', 'l.lid', '=', 'bm.bid')
        ->whereRaw('bm.uid = '.$uid.' and t.coveragetype = l.ltype')
        ->join('localmsg as m', 'm.thdid', '=', 't.thdid')
        ->whereNotNull('bm.leavetime')
        ->where([['bm.bmtimestamp', '<', 'm.ltimestamp'], ['m.ltimestamp', '<', 'bm.leavetime']]);
        // ->get();
        $current = DB::table('block_memb as bm')
                ->join('thread as t', 't.sender', '=', 'bm.bid')
                ->join('locality as l', 'l.lid', '=', 'bm.bid')
                ->whereRaw('bm.uid = '.$uid.' and t.coveragetype = l.ltype')
                ->join('localmsg as m', 'm.thdid', '=', 't.thdid')
                ->whereNull('bm.leavetime')
                ->where([['bm.bmtimestamp', '<', 'm.ltimestamp']])
                ->union($previous)
                ->get();
        return view('thread', ['messages' => $current ]);
    }


    // Quick fetch message
    public function quickMessageFetch(Request $request) {
        $uid = Auth::user()->uid;
        $fid = $request->input('fid');
        $tid = $request->input('tid');
        $mid = $request->input('mid');
        // limit 100 entries
        $msgs = DB::table('message')
            ->where([
                ['thdid', '=', $tid],
                ['mid', '>', $mid]
            ])
            ->limit(50)
            ->get();
        return response()->json(['messages' => $msgs]);;
    }

    // gET Friend CHAT
    public function getFriendChat(Request $request) {
        $uid = Auth::user()->uid;
        $fid = $request->input('fid');
        // limit 100 entries
        $threadid = DB::table('friend')
                    ->where([
                        ['uid1', '=', $uid],
                        ['uid2', '=', $fid],
                        ['status', '=', 'valid']
                    ])
                    ->orWhere([
                        ['uid2', '=', $uid],
                        ['uid1', '=', $fid],
                        ['status', '=', 'valid']
                    ])
                    ->value("thdid");
        $msgs = DB::table('message')
            ->where([
                ['thdid', '=', $threadid]
            ])
            ->limit(50)
            ->get();
        return response()->json(['thdid' => $threadid, 'messages' => $msgs]);;
    }

    // INSERT Friend CHAT
    public function sendFriendChat(Request $request) {
        $uid = Auth::user()->uid;
        $fid = $request->input('fid');
        $content = $request->input('content');
        $tid = DB::table('friend')
        ->where([
            ['uid1', '=', $uid],
            ['uid2', '=', $fid],
            ['status', '=', 'valid']
        ])
        ->orWhere([
            ['uid2', '=', $uid],
            ['uid1', '=', $fid],
            ['status', '=', 'valid']
        ])
        ->value("thdid");
        $mid = DB::table('message')->insertGetId(
            ['thdid' => $tid, 'author' => $uid, 'mtimestamp' => Carbon::now(), 
             'text' => $content]
        );
        return response()->json(['mid' => $mid]);;
    }

    // Get all threads and their init message
    public function getThreadList(Request $request) {
        $uid = Auth::user()->uid;
        $type = $request->input('type');
        $threads = null;
        if ($type == "group") {
            $threads = DB::table('thread as t')
            ->where('t.coveragetype', '=', 'group')
            ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
            ->whereNull('mm.leavetime')
            ->where('mm.uid', '=', $uid)
            ->join('message as m', 'm.thdid', '=', 't.thdid')
            ->where('m.mid', '=', 1)
            ->selectRaw('t.thdid, Date(t.ttimestamp) as date, m.title, m.text')
            ->get();
        }
        return response()->json(['threads' => $threads]);;
    }
    
    // Initialize a thread of message
    public function initGroupMessage(Request $request) {
        $uid = Auth::user()->uid;
        $topic = $request->input('topic');
        $text = $request->input('text');
        $members = $request->input('members');
        $address = $request->input('address');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $addrid = null;
        DB::transaction(function () use ($uid, $topic, $text, $members, $address, $lat, $lng, $addrid) {
            if ($address != '') {
                $addrid = DB::table('address')->insertGetId(
                    ['address' => $address, 'lat' => floatval($lat), 'lng' => floatval($lng), 'type' => "chosenFromMap"]
                );
            }
            $thdid = DB::table('thread')->insertGetId(
                ['sender' => $uid, 'coveragetype' => 'group', 'ttimestamp' => Carbon::now()]
            );
            DB::table('message')->insert(
                ['mid' => 1, 'thdid' => $thdid, 'author' => $uid,'mtimestamp' => Carbon::now(), 'title' => $topic, 'text' => $text, 'address' => $addrid]
            );
            foreach($members as $mb) {
                DB::table('msg_memb')->insertGetId(
                    ['uid' => $mb, 'thdid' => $thdid, 'jointime' => Carbon::now(), 'lastmsg' => "0"]
                );
            }
            DB::table('msg_memb')->insertGetId(
                ['uid' => $uid, 'thdid' => $thdid, 'jointime' => Carbon::now(), 'lastmsg' => "0"]
            );
        });

    }

    
    // Get thread messages from the threads
    public function getThreadMessages(Request $request) {
        $uid = Auth::user()->uid;
        $thdid = $request->input('thdid');
        $allmembers = DB::table('thread as t')
        ->where([['t.coveragetype', '=', 'group'], ['t.thdid', '=', $thdid]])
        ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
        ->whereNull('mm.leavetime')
        ->join('user as u', 'u.uid', '=', 'mm.uid')
        ->select('u.name as uname', 'u.uid')
        ->get();

        $beforemsgs = DB::table('thread as t')
        ->where([['t.coveragetype', '=', 'group'], ['t.thdid', '=', $thdid]])
        ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
        ->whereNotNull('mm.leavetime')
        ->where("mm.uid", '=', $uid)
        ->join('message as m', 't.thdid', '=', 'm.thdid')
        ->whereRaw('m.mtimestamp between mm.jointime and mm.leavetime')
        ->join("user as u", "u.uid", '=', 'm.author');
        // ->get();
        $currentmsgs = DB::table('thread as t')
        ->where([['t.coveragetype', '=', 'group'], ['t.thdid', '=', $thdid]])
        ->join('message as m', 't.thdid', '=', 'm.thdid')
        ->join('msg_memb as mm', 't.thdid', '=', 'mm.thdid')
        ->whereNull('mm.leavetime')
        ->where("mm.uid", '=', $uid)
        ->whereRaw('m.mtimestamp >= mm.jointime')
        ->join("user as u", "u.uid", '=', 'm.author')
        // ->union($beforemsgs)
        ->select("u.name as uname", "m.mid", "m.title", "m.text", "m.replyto", "m.mtimestamp")
        ->get();
        return response()->json(['members' => $allmembers, 'messages' => $currentmsgs]);;
    }

    // Add message to thread
    public function normalGroupMessage(Request $request) {
        $uid = Auth::user()->uid;
        $topic = $request->input('topic');
        $text = $request->input('text');
        $thdid = $request->input('thread');
        $address = $request->input('address');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $replyto = $request->input('replyto');
        $addrid = null;
        if ($address != '') {
            $addrid = DB::table('address')->insertGetId(
                ['address' => $address, 'lat' => floatval($lat), 'lng' => floatval($lng), 'type' => "chosenFromMap"]
            );
        }
        DB::table('message')->insert(
            ['thdid' => $thdid, 'author' => $uid,'mtimestamp' => Carbon::now(), 'title' => $topic, 'text' => $text, 'address' => $addrid, 'replyto' => $replyto]
        );
    }

    // Get all local broadcast message from joined block
    public function getLocalMessages(Request $request) {
        $uid = Auth::user()->uid;
        $type = $request->input('type');
        if ($type == 'broadcast') {
            $thdid = DB::table('block_memb as bm')
                    ->join('thread as t', 't.sender', '=', 'bm.bid')
                    ->whereNull('bm.leavetime')
                    ->where([['bm.uid', '=', $uid], ['t.coveragetype', '=', 'broadcast']])
                    ->first();
            $currentblock = DB::table('locality as l')
                        ->join('block_memb as bm', 'bm.bid', '=', 'l.lid')
                        ->join("address as a1", "a1.addrid", '=', 'l.southwest')
                        ->join("address as a2", 'a2.addrid', '=', 'l.northeast')
                        ->whereNull('bm.leavetime')
                        ->where([['bm.uid', '=', $uid]])
                        ->select('ltype', 'l.name as lname', 'a1.address', 'a1.lat as swlat', 'a1.lng as swlng', 'a2.lat as nelat', 'a2.lng as nelng')
                        ->get();
            $previous = DB::table('block_memb as bm')
                        ->join('thread as t', 't.sender', '=', 'bm.bid')
                        ->where([['bm.uid', '=', $uid], ['t.coveragetype', '=', 'broadcast']])
                        ->join('message as m', 'm.thdid', '=', 't.thdid')
                        ->whereNotNull('bm.leavetime')
                        ->where([['bm.bmtimestamp', '<', 'm.mtimestamp'], ['m.mtimestamp', '<', 'bm.leavetime']])
                        ->join("address as a", "a.addrid", '=', 'm.address' )
                        ->join("user as u", "u.uid", '=', 'bm.uid')
                        ->select('t.thdid', 'm.mid', 'm.title', 'm.text', 'm.replyto', 'm.mtimestamp', 'a.address', 'a.lat', 'a.lng', "u.uid", "u.name");
                        // ->get();
            $current = DB::table('block_memb as bm')
                        ->join('thread as t', 't.sender', '=', 'bm.bid')
                        ->where([['bm.uid', '=', $uid], ['t.coveragetype', '=', 'broadcast']])
                        ->join('message as m', 'm.thdid', '=', 't.thdid')
                        ->whereNull('bm.leavetime')
                        ->where([['bm.bmtimestamp', '<', 'm.mtimestamp']])
                        ->join("address as a", "a.addrid", '=', 'm.address' )
                        ->join("user as u", "u.uid", '=', 'bm.uid')
                        ->select('t.thdid', 'm.mid', 'm.title', 'm.text', 'm.replyto', 'm.mtimestamp', 'a.address', 'a.lat', 'a.lng', "u.uid", "u.name")
                        ->union($previous)
                        ->get();
            return response()->json(['blockinfo' => $currentblock, 'blockmessages' => $current, 'thread' => $thdid]);;
        }
    }

    // Create a local block broadcast message
    public function broadcastMessage(Request $request) {
        $uid = Auth::user()->uid;
        $topic = $request->input('topic');
        $text = $request->input('text');
        $thdid = $request->input('thread');
        $address = $request->input('address');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $replyto = $request->input('replyto');
        $addrid = null;
        if ($address != '') {
            $addrid = DB::table('address')->insertGetId(
                ['address' => $address, 'lat' => floatval($lat), 'lng' => floatval($lng), 'type' => "chosenFromMap"]
            );
        }
        DB::table('message')->insert(
            ['thdid' => $thdid, 'author' => $uid,'mtimestamp' => Carbon::now(), 'title' => $topic, 'text' => $text, 'address' => $addrid, 'replyto' => $replyto]
        );
    }

    // HOMEPAGE SEARCH
    public function search(Request $request) {
        $uid = Auth::user()->uid;
        $type = $request->input('type');
        $text = $request->input('text');
        if ( $type == "message") {
            $results = DB::select( 
                DB::raw("(select m.text, m.title, m.ltimestamp as mtimestamp, a.address, bm.uid
                from block_memb bm join thread t on bm.bid = t.sender join locality l on l.lid = bm.bid join localmsg m on m.thdid = t.thdid join address a on l.southwest = a.addrid
                where bm.uid = ".$uid." and t.coveragetype = l.ltype and ((m.ltimestamp between bm.bmtimestamp and bm.leavetime) or m.ltimestamp >= bm.bmtimestamp) and (title like '%".$text."%' or text like '%".$text."%'))
                
                union all
                
                (select m.text, m.title, m.mtimestamp, a.address, bm.uid
                from block_memb bm join msg_memb as mm on bm.uid = mm.uid join thread t on mm.thdid = t.thdid join message m on m.thdid = t.thdid join address a on a.addrid = m.address
                where bm.uid = ".$uid." and t.coveragetype = 'group' and ((m.mtimestamp between bm.bmtimestamp and bm.leavetime) or m.mtimestamp >= bm.bmtimestamp) and (title like '%".$text."%' or text like '%".$text."%'))
                
                union all
                
                (select m.text, m.title, m.mtimestamp, a.address, bm.uid
                from block_memb bm join thread t on bm.bid = t.sender join locality l on l.lid = bm.bid join message m on m.thdid = t.thdid join address a on l.southwest = a.addrid
                where bm.uid = ".$uid." and t.coveragetype = 'broadcast' and ((m.mtimestamp between bm.bmtimestamp and bm.leavetime) or m.mtimestamp >= bm.bmtimestamp) and (title like '%".$text."%' or text like '%".$text."%'))
                
                union all
                
                (select m.text, concat(u1.name, '--chat--', u2.name) as title, mtimestamp, '' as address, u1.uid
                from friend f join user u1 on u1.uid = f.uid1 join user u2 on u2.uid = f.uid2 join thread t on t.thdid = t.thdid join message m on m.thdid = t.thdid
                where f.status = 'valid' and (u1.uid = ".$uid." or u2.uid = ".$uid.") and ( text like '%".$text."%'))"
            ) );
            return response()->json(['result' => $results]);
        } else if ($type == 'user') {
            $results = DB::select( 
                DB::raw("select u2.name, u2.email, 'my neighbor' as rela, uid2 as id
                from neighbor n join user u1 on u1.uid = n.uid1 join user u2 on u2.uid = n.uid2
                where n.uid1 = ".$uid." 
                union
                select u1.name, u1.email, 'me as neighbor' as rela, uid1 as id
                from neighbor n join user u1 on u1.uid = n.uid1 join user u2 on u2.uid = n.uid2
                where n.uid2 = ".$uid."  
                union
                select u2.name, u2.email, 'my friend' as rela, uid2 as id
                from friend n join user u1 on u1.uid = n.uid1 join user u2 on u2.uid = n.uid2
                where n.uid1 = ".$uid."  and n.status='valid'
                union
                select u1.name, u1.email, 'me friend' as rela, uid1 as id
                from friend n join user u1 on u1.uid = n.uid1 join user u2 on u2.uid = n.uid2
                where n.uid2 = ".$uid."  and n.status='valid'"
            ) );
            return response()->json(['result' => $results]);
        }
    }
}
