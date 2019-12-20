@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 100%;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid" id="maincontainer">
        <div class="row">
        <el-row :gutter="24">
        <!-- CURRENT REQUEST START -->
        <el-col :span="11">
            <el-card shadow="hover">
            <div slot="header" class="clearfix">
                <span><b>My Request</b></span>
                <el-button style="float: right; padding: 3px 0" type="text"><font color="#67C23A">{{isset($myreq)? $myreq->status : ''}}</font></el-button>
            </div>
            
            <div class="card-body">
            @if( isset($ismemb))
                <h3> You are a member of</h3>
                <h1><font color="#67C23A">{{$ismemb->name}}</font></b></h1>
                <h3>Help others to join in!</h3>
            @elseif( !empty($myreqblock) and isset($myreq) )
                <!-- Map here -->
                <div v-for="block in {{ $myreqblock}}" :key="block.lid">
                <div style="width:400px;" :onclick="'mapTrigger(' + block.lid  + ',' + block.swlat  + ',' + block.swlng  + ',' + block.nelat  + ',' + block.nelng  + ')'">
                <div :id="'map'+block.lid" style="height:250px;width:400px;">Click to load</div>
                </div>
                <el-row>
                <el-col :span="8"><label >Block</label></el-col>
                <el-col :span="16"><h4 >@{{block.name}}</h4></el-col>
                </el-row>
                <el-row>
                <el-col :span="8"><label >Address</label></el-col>
                <el-col :span="16"><h5 id="labeladdress"><i class="el-icon-location"></i>@{{block.address}}</h5></el-col>
                </el-row>
                <el-row>
                <el-col :span="8"><label >Description</label></el-col>
                <el-col :span="16"><p>@{{block.description}}</p></el-col>
                </el-row>
                <el-row>
                <el-col :span="8"><label >Status</label></el-col>
                <el-col :span="16"><h4 ><el-progress :percentage="{{(3-$myreq->numb4apv)/3*100}}" :text-inside="true" ></el-progress></h4></el-col>
                </el-row>
                <el-row>
                <el-col :span="8"><label >{{isset($myreq)? $myreq->status : 'None'}}</label></el-col>
                <el-col :span="16"><p>Your request still needs <b><font color="#67C23A">{{$myreq->numb4apv}}</font></b> approvement...</p></el-col>
                </el-row>
                </div>
            @else
            <h4>You have not submitted any request yet.</h4>
            <el-link :underline="false" href="{{ route('community') }}">Come and join one block!</el-link>
            @endif
            </div>
            </el-card>
        </el-col>
        <!-- CURRENT REQUEST END -->
        <!-- APPROVE OTHERS START -->
        <el-col :span="11" :offset='1'>
            <el-card shadow="hover">
            <div slot="header" class="clearfix">
                <span><b>Approve Other's Join Request</b></span>
                <el-button style="float: right; padding: 3px 0" type="text">Operation button</el-button>
            </div>
            <div class="card-body">
            @if(!isset($ismemb) )
            <h4>You need to join one block first, try to find an interesing one</h4>
            <el-link :underline="false" href="{{ route('community') }}">Come and join one block!</el-link>
            @else
            <!-- HERE -->
            <ul class="list-group" id="entry-parent">
                <div v-for="oreq in {{$otherreq}}" :key="oreq.uid" :id="'entry'+oreq.uid">   
                    <el-row class="list-group-item"> 
                        <el-col :span="16">
                            <span>@{{oreq.name}}<a>(@{{oreq.email}})</a>&nbsp;want to join&nbsp;<font color="#E6A23C">@{{oreq.ago===0?'today':(oreq.ago + 'days ago')}}&nbsp;</font> </span>
                        </el-col><el-col :span="4">
                            <el-button style="float: right; padding:1px 0;" type="success" plain 
                                :onclick="'appv('+oreq.uid+','+oreq.lid+','+oreq.appver+')'">Approve</el-button>
                        </el-col><el-col :span="4">
                            <el-button style="float: right; padding:1px; 0" type="danger" :onclick="'entry_del('+oreq.uid+')'" plain>Ignore</el-button>
                        </el-col>
                    </el-row>
                </div>
            </ul>
            @endif
            </div>
            </el-card>
        </el-col>
        <!-- APPROVE OTHERS END -->
        </el-row>
        </div>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection

@push('head')
<script src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY'  async defer></script>
<script>
$( document ).ready(function() {
    $('#entry-parent').paginate({
        scope: $('div'), // targets all div elements
    });
});

const vuemethod = new Vue({
    el: '#maincontainer',
    // data: {
    //     name: 'Vue.js'
    // },
    // define methods under the `methods` object
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

function entry_del(entryid) {
    $("#entry" + entryid).remove();
}

function appv(...apvreq) {
    entry_del(apvreq[0]);
    $.ajax({
        url: '{{route('aprvother')}}',
        data: {
            _token : "{{ Session::token() }}",
            uid: apvreq[0],
            lid: apvreq[1],
            appver: apvreq[2]
        },
        type: "GET",
        dataType: "json",
        finally: function(data) {
            vuemethod.greet('You have approved an request!');
        }
    });
}
</script>
<style>
html{
    overflow:scroll;
}
</style>

@endpush