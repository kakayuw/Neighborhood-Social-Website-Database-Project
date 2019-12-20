@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 500px;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid">        
        	<!-- <iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.co.uk/maps?f=q&source=s_q&hl=en&geocode=&q=15+Springfield+Way,+Hythe,+CT21+5SH&aq=t&sll=52.8382,-2.327815&sspn=8.047465,13.666992&ie=UTF8&hq=&hnear=15+Springfield+Way,+Hythe+CT21+5SH,+United+Kingdom&t=m&z=14&ll=51.077429,1.121722&output=embed"></iframe> -->
        <profile></profile>    
    </div>
    </el-main>
    </el-container>
</el-container>

@endsection
@push('tail')
<script src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY&language=en-US'>
</script>
@endpush