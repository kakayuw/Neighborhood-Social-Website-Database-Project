@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 100%;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid" id="maincontainer">
        <div class="row">
            <div class="col-md-12 "><!-- col-md-offset-1 -->
                @if ( !isset($ismemb))
                    <h1>You have not joined any blocks yet!</h1>
                    <h2>Come and join one!</h2>
                    <!-- <p><hr></hr></p> -->
                    <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>All available blocks</span>
                        <el-button style="float: right; padding: 3px 0" type="text">options</el-button>
                    </div>
                    <ul id="allblockpage">
                    <!-- Loop Entry Body-->
                    <div v-for="block in {{ $blocks}}" :key="block.lid" class="item  panel" data-href='' >   
                    <el-popover
                        placement="right"
                        width="400px"
                        :title="block.name"
                        trigger="click"
                        content="this is content">
                        <!--Map card-->
                        <section class="section">
                        <div class="card">
                        <div class="card-body">
                            <!--Google Map-->
                            <div class="z-depth-1-half map-container-7 col-md-12" style="height: 250px">
                            <!-- <iframe src="https://maps.google.com/maps?q=Miami&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0"
                                style="width:600px; height:250px" allowfullscreen></iframe> -->
                                <div style="height:400px; width:400px;">
                                <!-- <div :id="'map'+block.lid"></div> -->
                                <div :id="'map'+block.lid" style="height:250px;width:400px;"></div>
                                </div>
                                <!-- Replace the value of the key parameter with your own API key. -->                                
                            </div>
                            <div class="row">
                            <!--Grid column-->
                            <div class="col-md-12">
                                <form>
                                <div class="md-form">
                                    <h4>@{{block.description}}</h4>
                                </div>
                                <div class="md-form">
                                <h5><i class="el-icon-location"></i>@{{block.address}}</h5>
                                </div>
                                </form>
                            </div>
                            </div>
                        </div>
                        </div>
                        </section>
                         <!--Map card end-->
                        <el-button slot="reference" type="text" :onclick="'mapTrigger(' + block.lid  + ',' + block.swlat  + ',' + block.swlng  + ',' + block.nelat  + ',' + block.nelng  + ')'">@{{block.name}}</el-button>
                    </el-popover>  
                    </div>
                    </ul>
                    </el-card>
                @else
                <!-- Map here -->
                <div v-for="block in {{ $myblock}}" :key="block.lid">
                    <div style="width:400px;" :onclick="'mapTrigger(' + block.lid  + ',' + block.swlat  + ',' + block.swlng  + ',' + block.nelat  + ',' + block.nelng  + ')'" id="mapcontainer">
                    <div :id="'map'+block.lid" style="height:300px;width:700px;">Click to load</div>
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
                    <el-col :span="8"><label >Time</label></el-col>
                    <el-col :span="16"><p>You have been joined the community for <b><font color="#67C23A">{{$ismemb->ago}}</font></b> days!</p></el-col>
                    <el-col :span="8"><label ></label></el-col>
                    <el-col :span="8">  <el-button type="warning" plain round onclick="confirmquit()">Quit this block</el-button></el-col>
                    <el-col :span="8"><label ></label></el-col>
                    </el-row>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection

@push('head')
<script async defer
src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY'>
// &callback=initMap    
</script>
<script>
$( document ).ready(function() {
    $('#allblockpage').paginate({
        scope: $('div'), // targets all div elements
    });
});

const vuemethod = new Vue({
    el: '#maincontainer',
    methods: {
        opencfm: function() {
            this.$confirm('You will not receive message from this block. Continue?', 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                quitblock();
                this.$message({
                    type: 'success',
                    message: 'You have quited this block!'
                });
            }).catch(() => {
                console.log("cancel quit");   
            });
        }
    }
});

function confirmquit() {
    vuemethod.opencfm();
}

function quitblock() {
    alert("are you ok?");
    $.ajax({
        url: '{{route('quitblock')}}',
        data: {
            _token : "{{ Session::token() }}",
            uid: {{Auth::user()->uid}},
            lid: {{isset($ismemb) ? $ismemb->bid: -1}}
        },
        type: "GET",
        dataType: "json"
    });
}
</script>
@endpush