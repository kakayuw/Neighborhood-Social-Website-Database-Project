@extends('layouts.app')

@section('content')
<el-container>
    <el-container >
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid" id="maincontainer">
        <el-tabs type="border-card">
        <el-tab-pane>
            <span slot="label"><i class="el-icon-position"></i> Block Neighbor</span>
            @if( $lid < 0 )
            <h3>You are not a member of any block/hood.</h3>
            <el-link :underline="false" href="{{ route('community') }}">Come and join one block!</el-link>
            @else
            <el-table style="maxHeight:100%"
                :data="{{$blockstranger}}"
                style="width: 100%">
                <el-table-column  label="Name"   width="180">
                <template slot-scope="scope">
                    <el-popover trigger="hover" placement="left">
                    <p>Email:   @{{scope.row.email}}   </p>
                    <p>Phone:   @{{scope.row.phone}}   </p>
                    <p>Desc.:   @{{scope.row.description}}   </p>
                    <div slot="reference" class="name-wrapper">
                        <el-tag size="medium">  <i class="el-icon-user-solid"></i>&nbsp;@{{scope.row.uname}}   </el-tag>
                    </div>
                    </el-popover>
                </template>
                </el-table-column>
                <el-table-column label="block/hood"  width="180">
                    <template slot-scope="scope">
                        <span style="margin-left: 10px">  @{{scope.row.lname}}   </span>
                    </template>
                </el-table-column>
                <el-table-column
                label="Operations">
                <template slot-scope="scope">
                    <el-button plain size="mini" type="warning" :onclick="'addneighbor('+scope.row.uid+')'">Add Neighbor</el-button>
                    <el-button plain size="mini" type="success" :onclick="'addfriend('+scope.row.uid+')'">Add Friend</el-button>
                </template>
                </el-table-column>
            </el-table>
            @endif
        </el-tab-pane>
        <el-tab-pane>
        <span slot="label"><i class="el-icon-house"></i> Neighbor</span>
            <el-tabs tab-position="left" :stretch="true">
                <el-tab-pane>
                <span slot="label">Who is my neighbor</span>
                @if (empty($neighbor))
                    <h3>You have not added any neighbor yer! Back to choose your neighbor!</h3>
                @else
                <el-table style="maxHeight:100%" :data="{{$neighbor}}" style="width: 100%">
                    <el-table-column  label="Name"   width="180">
                    <template slot-scope="scope">
                        <el-popover trigger="hover" placement="left">
                        <p>Phone:   @{{scope.row.phone}}   </p>
                        <p>Desc.:   @{{scope.row.description}}   </p>
                        <div slot="reference" class="name-wrapper">
                            <el-tag size="medium">  <i class="el-icon-user-solid"></i>&nbsp;@{{scope.row.uname}}   </el-tag>
                        </div>
                        </el-popover>
                    </template>
                    </el-table-column>
                    <el-table-column label="Email"  width="180">
                        <template slot-scope="scope">
                            <span style="margin-left: 10px">  @{{scope.row.email}}   </span>
                        </template>
                    </el-table-column>
                    <el-table-column
                    label="Operations">
                    <template slot-scope="scope">
                        <el-button plain size="mini" type="warning">Message</el-button>
                    </template>
                    </el-table-column>
                </el-table>
                @endif
                </el-tab-pane>
                <el-tab-pane>
                <span slot="label">I am whose neighbor</span>
                @if (empty($neighborof))
                    <h3>You are not neighbor of any user! Wair for someone neighboring you!</h3>
                @else
                <el-table style="maxHeight:100%" :data="{{$neighborof}}" style="width: 100%">
                    <el-table-column  label="Name"   width="180">
                    <template slot-scope="scope">
                        <el-popover trigger="hover" placement="left">
                        <p>Phone:   @{{scope.row.phone}}   </p>
                        <p>Desc.:   @{{scope.row.description}}   </p>
                        <div slot="reference" class="name-wrapper">
                            <el-tag size="medium">  <i class="el-icon-user-solid"></i>&nbsp;@{{scope.row.email}}   </el-tag>
                        </div>
                        </el-popover>
                    </template>
                    </el-table-column>
                    <el-table-column label="Email"  width="180">
                        <template slot-scope="scope">
                            <span style="margin-left: 10px">  @{{scope.row.lname}}   </span>
                        </template>
                    </el-table-column>
                    <el-table-column
                    label="Operations">
                    <template slot-scope="scope">
                        <el-button plain size="mini" type="warning">Message</el-button>
                    </template>
                    </el-table-column>
                </el-table>
                @endif
                </el-tab-pane>
            </el-tabs>
            
        </el-tab-pane>
        <el-tab-pane>
            <span slot="label"><i class="el-icon-user"></i> Friend</span>
            
            <el-row>
            @if (empty($friend))
            <h3>You have not added any friend yer! Back to choose your friend!</h3>
            @else
            <el-col :span="14">
            <friendchat :messages="{{$friend}}" 
            :showdrawer="false" 
            :friends="{{$friend}}" 
            :selfid="{{Auth::user()->uid}}"
            routeget="{{route('getFriendChat')}}" 
            routesend="{{route('sendFriendChat')}}"
            routefetch="{{route('quickFetch')}}"></friendchat>

            </el-col>
            @endif
            <el-col :span="10">
            <el-tabs tab-position="top" :stretch="true">
                <el-tab-pane>
                <span slot="label">My Request</span>
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Friend Request</h5>
                    <ul class="list-group list-group-flush row">
                    @foreach( $myfriendreq as $req)                    
                        <li class="list-group-item">
                            <p>Waiting for <font color="#67C23A">{{$req->uname}}</font>'s response...<el-button style="float: right; padding: 3px 0" type="text">processing</el-button></p>
                        </li>
                    @endforeach
                    </ul>
                </div>
                </div>
                </el-tab-pane>
                <el-tab-pane>
                <span slot="label" style="minHeight:510px">My Approvement</span>
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Received Request</h5>
                    <ul class="list-group list-group-flush row">
                    @foreach( $recvreq as $req)                    
                        <li class="list-group-item" id="freq{{$req->uid}}">
                        <el-row>
                            <el-col :span="2"></el-col>
                            <el-col :span="12"><el-badge value="new"><el-button type="text"><p>Friend request from <font color="#67C23A">{{$req->uname}}</font></p></el-button></el-badge></el-col>
                            <el-col :span="5"><el-button size="mini" type="success" plain onclick="apvfriend({{$req->uid}})">Approve</el-button></el-col>
                            <el-col :span="5"><el-button size="mini" type="danger" plain onclick="rejfriend({{$req->uid}})">Reject</el-button></el-col>
                        </el-row>
                        </li>
                    @endforeach
                    </ul>
                </div>
                </div>
                </el-tab-pane>
            </el-tabs>
            </el-col>
            </el-row>
        </el-tab-pane>
        <!-- <el-tab-pane>
            <span slot="label"><i class="el-icon-user"></i> debug</span>
            <p>{{!!$friend!!}}<p>
        </el-tab-pane> -->
        </el-tabs>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection


@push('tail')
<script>
function alllt() {
    alert("wtf!");
    $('#chatbox').attr("showdrawer", "true");
    console.log($('#chatbox'));
}
</script>
@endpush

@push('head')
<!-- <script src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY'  async defer></script> -->

<script>

const vuemethod = new Vue({
    // el: '#maincontainer',
    data: {drawer: true},
    methods: {
        greet: function (event) {
        // `this` inside methods point to the Vue instance
            this.$message({
                showClose: true,
                message: event
            });
        }
    }
});

function addneighbor(neigh) {
    $.ajax({
        url: '{{route("addneighbor")}}',
        data: {
            nid: neigh
        },
        type: "GET",
        dataType: "json",
        finally: function(data) {
            vuemethod.greet('You have added a neighbor! You can check in "Neighbor" tab.');
        }
    });
}

function addfriend(friend) {
    $.ajax({
        url: '{{route("addfriend")}}',
        data: {
            fid: friend
        },
        type: "GET",
        dataType: "json"
    });
    vuemethod.greet('You have send a friend request! You can check your requests by refreshing the page.');
}

function apvfriend(neigh) {
    console.log("apv", neigh)
    $("#freq" + neigh).remove();
    $.ajax({
        url: '{{route("apvfriend")}}',
        data: {
            fid: neigh
        },
        type: "GET",
        dataType: "json",
        finally: function(data) {
            vuemethod.greet('You have approved a friend request!');
        }
    });
}

function rejfriend(friend) {
    console.log("rej", friend)

    $("#freq" + friend).remove();
    $.ajax({
        url: '{{route("rejfriend")}}',
        data: {
            fid: friend
        },
        type: "GET",
        dataType: "json"
    });
    vuemethod.greet('You rejected one friend request...');
}

</script>
<style>
html{
    height:100%;
    /* overflow:scroll; */
}
</style>

@endpush
