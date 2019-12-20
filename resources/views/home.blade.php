@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 500px;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid">
        <search></search>
    </div>
    </el-main>
    </el-container>
</el-container>

@endsection



