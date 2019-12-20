@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 500px;">
    <sidebar></sidebar>
    <el-main>
    <template>
      <el-tabs>
        <el-tab-pane label="Group Message">
          <group-threads></group-threads>
        </el-tab-pane>
        <el-tab-pane label="Local Message">
          <block-thread></block-thread>
        </el-tab-pane>
        <el-tab-pane label="Local Notification">
          <div class="block">
            <el-timeline>
              @foreach ($messages as $m)
              <el-timeline-item timestamp="{{$m->ltimestamp}}" placement="top">
                <el-card>
                  <h4>{{$m->title}}</h4>
                  <p>{{$m->text}}</p>
                </el-card>
              </el-timeline-item>
              @endforeach
            </el-timeline>
          </div>
        </el-tab-pane>
      </el-tabs>
    </template>
    </el-main>
    </el-container>
</el-container>

@endsection

@push('tail')
<script src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY&language=en-US'>
</script>
@endpush