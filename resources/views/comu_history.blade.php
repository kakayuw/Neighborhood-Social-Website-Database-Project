@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 100%;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid" id="maincontainer">
        <div class="row">
            <div class="col-md-12 "><!-- col-md-offset-1 -->
                @if ( !isset($usedmember))
                    <h4>You need to join one block first, try to find an interesing one</h4>
                    <el-link :underline="false" href="{{ route('community') }}">Come and join one block!</el-link>
                    <p><hr></hr></p>
                @else
                <!-- TIMELINE START -->
                <div class="block mostwidth">
                <el-timeline>
                @foreach ($blocktime as $mb)
                    @if (isset($mb->bmtimestamp))
                    <el-timeline-item
                        key="{{$mb->bmtimestamp}}"
                        icon="el-icon-share"
                        type="success"
                        color="#67C23A"
                        size="large"
                        timestamp="JOIN {{$mb->bmtimestamp}}"
                        placement="top">
                    <el-card>
                        <h4>{{$mb->name}}</h4>
                        <p>{{$mb->description}}</p>
                        <h6><i class="el-icon-location"></i>{{$mb->address}}</h6>
                    </el-card>
                    </el-timeline-item>
                    @endif
                    @if (isset($mb->leavetime))
                        <el-timeline-item
                            key="{{$mb->leavetime}}"
                            icon="el-icon-circle-close"
                            type="danger"
                            color="#F56C6C"
                            size="large"
                            timestamp="LEAVE {{$mb->leavetime}}"
                            placement="top">
                        <el-card>
                            <h4>{{$mb->name}}</h4>
                            <p>{{$mb->description}}</p>
                            <h6><i class="el-icon-location"></i>{{$mb->address}}</h6>
                        </el-card>
                        </el-timeline-item>
                    @endif
                @endforeach
                </el-timeline>
                </div>
                <!-- TIMELINE END -->
                @endif
            </div>
        </div>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection

@push('head')
<!-- <script async defer
src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY'>
// &callback=initMap    
</script> -->
<script>
</script>
<style>
.mostwidth {
    max-width: 60%;
}
</style>
@endpush