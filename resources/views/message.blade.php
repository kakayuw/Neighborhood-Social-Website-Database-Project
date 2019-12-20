@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 100%;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid" id="maincontainer">
    <h3>Messages in recent 7 days</h3>
    <el-row :gutter="24">
    <el-col :span="8"><div class="grid-content bg-purple">
    <!-- LEFT TOP CONTAINER START -->
    <el-card  class="fixoverflow">
    <div slot="header" class="clearfix fixhead">
        <span>Group Message</span>
        <el-button style="float: right; padding: 3px 0" type="text">All as read</el-button>
    </div>
    @foreach ($recentgroup as $m)
    <el-card >
        <h4>{{$m->title}}</h4>
        <p>{{$m->text}}</p>
        <p><font color="#606266">{{$m->uname}}&nbsp;@&nbsp;{{$m->mtimestamp}}</font></p>
    </el-card>
    @endforeach
    </el-card>
    <!-- LEFT TOP CONTAINER END -->
    </div></el-col>
    <el-col :span="8"><div class="grid-content bg-purple">
    <!-- RIGHT TOP CONTAINER START -->
    <el-card class="fixoverflow">
    <div slot="header" class="clearfix fixhead">
        <span>Locality Message</span>
        <el-button style="float: right; padding: 3px 0" type="text">All as read</el-button>
    </div>
    @foreach ($recentblock as $m)
    <el-card>
        <h4>{{$m->title}}</h4>
        <p>{{$m->text}}</p>
        <p><font color="#606266">{{$m->name}}&nbsp;@&nbsp;{{$m->mtimestamp}}</font></p>
    </el-card>
    @endforeach
    </el-card>
    <!-- RIGHT TOP CONTAINER END -->
    </div></el-col>
    <el-col :span="8"><div class="grid-content bg-purple">
    <!-- RIGHT TOP CONTAINER START -->
    <el-card  class="fixoverflow">
    <div slot="header" class="clearfix fixhead">
        <span>Locality Notification</span>
        <el-button style="float: right; padding: 3px 0" type="text">All as read</el-button>
    </div>
    @foreach ($recentnotif as $m)
    <el-card>
        <h4>{{$m->title}}</h4>
        <p>{{$m->text}}</p>
        <p><font color="#606266">@&nbsp;{{$m->ltimestamp}}</font></p>
    </el-card>
    @endforeach
    </el-card>
    <!-- RIGHT TOP CONTAINER END -->
    </div></el-col>
    </el-row>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection

@push('head')
<script>
$( document ).ready(function() {
    $('#allblockpage').paginate({
        scope: $('div'), // targets all div elements
    });

});

const vuemethod = new Vue({
    el: '#maincontainer',
    data: {
        drawer: false
    }, 
    methods: {
        greet: function() {
            this.$message({
                showClose: true,
                message: event
            });
        }
    }
});

</script>

<style>
.fixoverflow{
    display:block;
    width:100%;
    height:450px;
    background-color:#EBEEF5;
    overflow:scroll;
}

</style>
@endpush
