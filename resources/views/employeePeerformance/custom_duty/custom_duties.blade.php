@extends('layouts.app')

@section('content')
<style>
    /* .duty-box {
        margin-bottom: 20px;
    } */
    
    .assign-duty-div {
        position: relative;overflow-x: hidden;
    }
    .duty-section {
        margin-bottom: 5px;
    }
    .action-btn{
        display: inline-block;
        float: left;

    }
    #detail-tag {
        text-align: justify;
    }
    .modal-content-loader {
        margin-left: 265px !important;
    }
    .modal .modal-content .modal-body {
    padding: 2px 0px;
    }
    .container {
    padding-left: 0px;
}
.duty-block {
    padding-right: 4px !important;
    display: inline-block !important;
    float: left !important;
    height: 520px;
    max-height: 520px;
}
.card {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    position: relative;
    margin-bottom: 30px;
    border-radius: 2px;
}
.close-second-btn,.close-second-crousel-btn,.close-attachment-comment-modal,.main-modale-dismiss {
    float: right;
    font-size: 21px;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    filter: alpha(opacity=20);
    opacity: .7;
}
.duty-block {
    height: 520px;max-height:520px;
}
/* #detailModal modal-body{
    background-color: #f4f5f7;
} */
.duty-date {
    font-size: 12px;
    color: gray;
    font-weight: 600;
}
.progress {
    height: 10px !important;
}
.progress-bar {
    float: left;
    width: 0;
    height: 100%;
    font-size: 9px;
    line-height: 11px;
}
a:hover{
    text-decoration: none;
}
.duty-title {
    color: #172b4d;
}
.badge-new {
    background-color: green;
    position: absolute;
    border-radius:35%;
}
.badge-expire {
    background-color: red;
    position: absolute;
    border-radius:35%;
}
.title-text {
    position: relative;
}
.title-less {

    top: -12px;
}
.btn-filter {
    padding-top: 35px;
}
.alerts-border {
    border: 2px #ff0000 solid;
    
    animation: blink 1s;
    animation-iteration-count: 25;
}
.duties-count {
    float: right;
}
.form-label {
    font-weight: 600;
}
.assign-duty-div {
    max-height: 520px;
    margin-bottom: 15px;
    padding: 10px 0;
    overflow-y: auto;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}
.assign-duty-div {
    position: relative;
    overflow-x: hidden;
}
.duty-section {
    margin-bottom: 5px;
}
.duty-title {
    color: #172b4d;
}
.badge {
    display: inline-block;
    font-size: 47%;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
}
@keyframes blink { 50% { border-color:#fff ; }  }
</style>
<section class="content">
    <div class="container-fluid">
        @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || (array_key_exists('employee-performance/designer/custom/duties',  json_decode(session('access'),true))) 
    || (array_key_exists('employee-performance/web/developer/smart/look',  json_decode(session('access'),true))) || (array_key_exists('employee-performance/app/developer/smart/look',  json_decode(session('access'),true))))
    <div class="row">
                  <div class="col-xs-12">
                      
                    <div class="row mb-4">
                        <div class="col-md-12 col-sm-12">
        
                            <div class="card no-b form-box">
                                @if(session()->has('success'))
                                    <div class="alert alert-success">
                                        {{ session()->get('success') }}
                                    </div>
                                @endif
        
                                <div class="card-header white">
                                @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || (array_key_exists('employee-performance/designer/custom/duties',  json_decode(session('access'),true))) 
                                || (array_key_exists('employee-performance/web/developer/smart/look',  json_decode(session('access'),true))))
                                <div class="row">
                                  <div class="col-xs-12">

                                  <form name="filter_reports" id="filter_reports" method="get" action="{{ ($argc && $argc == 'marketer') ? route('update.assign.custom.duties', 'marketer') : route('assigned.custom.duties.'.$argc, $argc) }}">
                                      {{csrf_field()}}
                                      <div class="row">
                                          <div class="col-sm-3">
                                            <div class="form-group form-float">
                                              <!-- <div class="form-line"> -->
                                                  <label class="form-label" for="user_group_id">User</label><br>
                                                  <select name="user" id="user" class="form-control show-tick" data-live-search="true" onchange="getIrregularDuties(this.value)">
                                                      <option value="">Select User</option>
                                                      @foreach($users as $user)
                                                      <option value="{{$user->user_id}}" <?php  if(isset($old_input['user']) && $old_input['user'] == $user->user_id) { echo 'selected';} ?>>{{$user->firstname}} {{$user->lastname}}</option>
                                                      @endforeach
                                                  </select>
                                              <!-- </div> -->
                                            </div>
                                          </div>
                                          <div class="col-sm-3">
                                              <div class="form-group form-float">
                                                  <div class="form-line">
                                                      <label class="form-label" for="status">Duty</label><br>  
                                                      <div class="form-line duty_box">
                                                        <select name="duty" id="is_close" class="form-control show-tick" onchange="getSubDuties(this.value)" data-live-search="true">
                                                            <option value="">Select Duty</option>
                                                            @foreach($duty_lists as $duty_list)
                                                            <option value="{{$duty_list->id}}" <?php  if(isset($old_input['duty']) && $old_input['duty'] == $duty_list->id) { echo 'selected';} ?>>{{$duty_list->name}}</option>
                                                            @endforeach
                                                        </select>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label class="form-label" for="status">Sub Duty</label><br>
                                                    <div class="form-line sub_duty_box">
                                                        <select name="sub_duty" id="is_close" class="form-control show-tick" data-live-search="true">
                                                            <option value="">Select Sub Duty</option>
                                                            @foreach($sub_duty_lists as $sub_duty_list)
                                                            <option value="{{$sub_duty_list->id}}" <?php  if(isset($old_input['sub_duty']) && $old_input['sub_duty'] == $sub_duty_list->id) { echo 'selected';} ?>>{{$sub_duty_list->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          <div class="col-sm-2">
                                              <div class="form-group form-float btn-filter">
                                              <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                              </div>
                                          </div> 
                                          
                                    
                                      </div>
                                      
                                  </form>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-xs-12">
                                  <form name="filter_reports" id="filter_reports" method="get" action="{{ route('add.irregular.duty.form', [$argc, isset($old_input['user']) ? $old_input['user'] : '']) }}">
                                    
                                              <div class="form-group form-float btn-filter">
                                              <button type="submit" id="search_filter" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Add Duty</button>
                                              </div>
                                  </form>
                                  </div>
                                </div>
                                  @else 
                                  <div class="col-xs-4">
                                    <form name="filter_reports" id="filter_reports" method="get" action="{{ route('add.irregular.duty.form', [$argc, isset($old_input['user']) ? $old_input['user'] : '']) }}">
                                      
                                                <div class="form-group form-float btn-filter">
                                                <button type="submit" id="search_filter" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Add Duty</button>
                                                </div>
                                    </form>
                                    </div>
                                    @endif
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              @endif

        {{-- tab code start=============== --}}
        <div class="container" style="">
       

              <!-- Duties Tab Start  -->
                      {{ csrf_field() }}
                      <div class="col-sm-12">
                              <?php if(Session::has('message')) { ?>
                              <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                  <?php echo Session::get('message') ?>
                              </div>
                              <?php } ?>
                          </div>
                      <div class="row ">
                        <div class="col-12" id="custom_list">
                          <div class="col-sm-3 col-sm-3 col-xs-12 duty-block" >
                              <div class="card" id="pending-duties-box" style="padding: 10px;overflow: hidden;">
                                          <label class="form-label" style="border-bottom: 1px solid gainsboro;">To Do</label>
                                          {{-- <label class="form-label duties-count">({{count($not_started)}})</label> --}}
                                          <div class="assign-duty-div {{count($not_started) > 0 ? 'box-extend': '' }}">
                                          @forelse($not_started as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                            <a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal"><a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal">
                                                <div class="col-sm-12" style="position: relative;">
                                                  <h4 id="main" class="title-text">
                                                      {{$duty->title}} 
                                                      @if($duty->is_view == 0 && date('Y-m-d', strtotime($duty->end_date)) >= date('Y-m-d'))
                                                      <span class="badge badge-new {{(strlen($duty->title) <= 19) ? 'title-less' : ''}}">New</span>
                                                      @endif

                                                      @if(date('Y-m-d') > date('Y-m-d', strtotime($duty->end_date)))
                                                      <span class="badge badge-expire {{(strlen($duty->title) <= 19) ? 'title-less' : ''}}">Expire</span>
                                                      @endif
                                                    </h4>
                                                </div>
                                                <!-- <div class="col-sm-6">
                                                    <label id="main">Start:</label> {{$duty->start_date}}
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">End:</label> {{$duty->end_date}}
                                                </div> -->
                                                <div class="col-sm-12">
                                                    <span class="duty-date">{{date('d M, Y', strtotime($duty->start_date))}}</span> <label id="main"> To </label> <span class="duty-date">{{date('d M, Y', strtotime($duty->end_date))}}</span>
                                                </div>
                                                
                                                @foreach($duty['attachmentFiles'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    {{-- <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;"> --}}
                                                    @if(in_array($file->extension, $extensions)) 
                                                    @php $flag = false; @endphp
                                                    <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->

                                                    {{-- <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"> --}}
                                                    
                                                    {{-- @else
                                                    @php $flag = true; @endphp
                                                    <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                                                    <i class="fa fa-download"></i>Dwonload --}}
                                                    @endif
                                                    
                                                    {{-- @if($flag)
                                                    <h5>Download your file </h5>
                                                    @endif --}}
                                                    {{-- </div> --}}
                                                    @endforeach
                                                 </a>
                                            </div>
                                            <div class="col-sm-12" style="border: 1px solid #8080802b;background-color: #80808017">
                                                <div class="col-sm-8" style="margin-top: 5px;padding-left: 8px;display: inline-block;">
                                                    <span class="repeat-text">Repeated <span style="color: red;font-weight: 700;">{{$duty->repeated}}</span> times</span>
                                                </div>
                                                @if(session('role') == 'ADMIN')
                                                <!-- <div class="col-sm-6">
                                                <button type="button" class="btn btn-info btn-block action-btn" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 0)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button type="button" class="btn btn-success btn-block action-btn" onclick="dutyDetails('{{$duty->id}}')" data-toggle="modal" data-target="#detailModal">Details</button>
                                                </div> -->
                                                
                                                <div class="col-sm-4" style="margin-top: 2px;text-align:right;float: right;">
                                                    <a href="<?php echo URL::to('duty/delete/' . $duty->id . '/1/'.$argc) ?>" type="button" class="delete-user" >
                                                        <i class="fa fa-trash-o" style="font-size:22px; color:red;"></i></a>
                                                </div>
                                                @endif
                                                </div>
                                          </div> 
                                            
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>
                              </div>
                          </div>

                          
                          <div class="col-sm-3 col-sm-3 col-xs-12 duty-block">
                              <div class="card" style="padding: 10px;overflow: hidden;">
                                          <label class="form-label" style="border-bottom: 1px solid gainsboro;">Doing</label>

                                          <div class="assign-duty-div {{count($started) > 0 ? 'box-extend' : '' }}">
                                          @forelse($started as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                             <a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal"><a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal">
                                             <div class="col-sm-12" style="position: relative;">
                                                  <h4 id="main" class="title-text">
                                                      {{$duty->title}} 
                                                      @if(date('Y-m-d') > date('Y-m-d', strtotime($duty->end_date)))
                                                      <span class="badge badge-expire {{(strlen($duty->title) <= 19) ? 'title-less' : ''}}">Expire</span>
                                                      @endif
                                                    </h4>
                                                </div>
                                                <div class="col-sm-12">
                                                    <span class="duty-date">{{date('d M, Y', strtotime($duty->start_date))}}</span> <label id="main"> To </label> <span class="duty-date">{{date('d M, Y', strtotime($duty->end_date))}}</span>
                                                </div>
                                                <div class="col-sm-12">
                                                <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
                                                aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:10%">
                                                    10% Complete (success)
                                                </div>
                                                </div>
                                                </div>
                                                    @foreach($duty['attachmentFiles'] as $k => $file)
                                                    @php $flag = false; @endphp
                                                        {{-- <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;"> --}}
                                                            @if(in_array($file->extension, $extensions)) 
                                                            @php $flag = false; @endphp
                                                            <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->

                                                            {{-- <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"> --}}
                                                            
                                                            {{-- @else
                                                            @php $flag = true; @endphp
                                                            <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                                                            <i class="fa fa-download"></i>Dwonload --}}
                                                            @endif
                                                            
                                                            {{-- @if($flag)
                                                            <h5>Download your file </h5>
                                                            @endif --}}
                                                        {{-- </div> --}}
                                                        @endforeach
                                              </a>
                                            </div>
                                            <div class="col-sm-12" style="border: 1px solid #8080802b;background-color: #80808017">
                                            <div class="col-sm-8" style="margin-top: 5px;padding-left: 8px;display: inline-block;">
                                                <span class="repeat-text">Repeated <span style="color: red;font-weight: 700;">{{$duty->repeated}}</span> times</span>
                                            </div>
                                            @if(session('role') == 'ADMIN')
                                            <!-- <div class="col-sm-6">
                                            <button type="button" class="btn btn-info btn-block action-btn" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 0)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-success btn-block action-btn" onclick="dutyDetails('{{$duty->id}}')" data-toggle="modal" data-target="#detailModal">Details</button>
                                            </div> -->
                                            
                                            <div class="col-sm-4" style="margin-top: 2px;text-align:right;float: right;">
                                                <a href="<?php echo URL::to('duty/delete/' . $duty->id . '/1/'.$argc) ?>" type="button" class="delete-user" ><i class="fa fa-trash-o" style="font-size:22px; color:red;"></i></a>
                                            </div>
                                            @endif
                                            </div>
                                        </div>
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse

                                      </div>
                           </div>
                          </div>
                          
                          <div class="col-sm-3 col-sm-3 col-xs-12 duty-block">
                              <div class="card" id="testing-duties-box" style="padding: 10px;overflow: hidden;">
                                  
                                          <label class="form-label" style="border-bottom: 1px solid gainsboro;">Testing</label>
                                          <div class="assign-duty-div {{count($in_testing) > 0 ? 'box-extend' : '' }}">
                                          @forelse($in_testing as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                            <a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal"><a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal">
                                                <div class="col-sm-12">
                                                   <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-12">
                                                    <span class="duty-date">{{date('d M, Y', strtotime($duty->start_date))}}</span> <label id="main"> To </label> <span class="duty-date">{{date('d M, Y', strtotime($duty->end_date))}}</span>
                                                </div>
                                                <div class="col-sm-12">
                                                <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
                                                aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                                                    75% Complete
                                                </div>
                                                </div>
                                                </div>
                                                @foreach($duty['attachmentFiles'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    {{-- <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;"> --}}
                                                        @if(in_array($file->extension, $extensions)) 
                                                        @php $flag = false; @endphp
                                                        <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                                                        
                                                        {{-- <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"> --}}
                                                       
                                                        {{-- @else
                                                        @php $flag = true; @endphp
                                                        <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                                                        <i class="fa fa-download"></i>Dwonload --}}
                                                        @endif
                                                        
                                                        {{-- @if($flag)
                                                        <h5>Download your file </h5>
                                                        @endif --}}
                                                    {{-- </div> --}}
                                                    @endforeach
                                                </a>
                                               
                                                
                                            </div>
                                            <div class="col-sm-12" style="border: 1px solid #8080802b;background-color: #80808017">
                                                <div class="col-sm-8" style="margin-top: 5px;padding-left: 8px;display: inline-block;">
                                                    <span class="repeat-text">Repeated <span style="color: red;font-weight: 700;">{{$duty->repeated}}</span> times</span>
                                                </div>
                                                @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)) )
                                                <!-- <div class="col-sm-6">
                                                <button type="button" class="btn btn-info btn-block action-btn" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 0)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button type="button" class="btn btn-success btn-block action-btn" onclick="dutyDetails('{{$duty->id}}')" data-toggle="modal" data-target="#detailModal">Details</button>
                                                </div> -->
                                                
                                                <div class="col-sm-4" style="margin-top: 2px;text-align:right;float: right;">
                                                    <a href="<?php echo URL::to('duty/delete/' . $duty->id . '/1/'.$argc) ?>" type="button" class="delete-user" ><i class="fa fa-trash-o" style="font-size:22px; color:red;"></i></a>
                                                </div>
                                                @endif
                                                </div>
                                            </div>
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>
                                </div>
                          </div>

                          <div class="col-sm-3 col-sm-3 col-xs-12 duty-block">
                              <div class="card" style="padding: 10px;overflow: hidden;">
                                          <label class="form-label" style="border-bottom: 1px solid gainsboro;">Completed</label>
                                          <div class="assign-duty-div {{count($completed) > 0 ? 'box-extend' : '' }}">
                                          @forelse($completed as $k => $duty)
                                          <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                            <a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal"><a href="javascript:;" onclick="dutyDetails('{{$duty->id}}', '{{$argc}}')" class="duty-title" data-toggle="modal" data-target="#detailModal">
                                                <div class="col-sm-12">
                                                 <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-12">
                                                    <span class="duty-date">{{date('d M, Y', strtotime($duty->start_date))}}</span> <label id="main"> To </label> <span class="duty-date">{{date('d M, Y', strtotime($duty->end_date))}}</span>
                                                </div>
                                                <div class="col-sm-12">
                                                <div class="progress">
                                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
                                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                                                    100% Complete
                                                </div>
                                                </div>
                                                </div>
                                                @foreach($duty['attachmentFiles'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    {{-- <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;"> --}}
                                                        @if(in_array($file->extension, $extensions)) 
                                                        @php $flag = false; @endphp
                                                        <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                                                        
                                                        {{-- <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"> --}}
                                                        
                                                        {{-- @else
                                                        @php $flag = true; @endphp
                                                        <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                                                        <i class="fa fa-download"></i>Dwonload --}}
                                                        @endif
                                                        
                                                        {{-- @if($flag)
                                                        <h5>Download your file </h5>
                                                        @endif --}}
                                                    {{-- </div> --}}
                                                    @endforeach
                                                
                                                </a>
                                                
                                            </div>
                                            <div class="col-sm-12" style="border: 1px solid #8080802b;background-color: #80808017">
                                                <div class="col-sm-8" style="margin-top: 5px;padding-left: 8px;display: inline-block;">
                                                    <span class="repeat-text">Repeated <span style="color: red;font-weight: 700;">{{$duty->repeated}}</span> times</span>
                                                </div>
                                                @if(session('role') == 'ADMIN')
                                                <!-- <div class="col-sm-6">
                                                <button type="button" class="btn btn-info btn-block action-btn" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 0)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button type="button" class="btn btn-success btn-block action-btn" onclick="dutyDetails('{{$duty->id}}')" data-toggle="modal" data-target="#detailModal">Details</button>
                                                </div> -->
                                                
                                                <div class="col-sm-4" style="margin-top: 2px;text-align:right;float: right;">
                                                    <a href="<?php echo URL::to('duty/delete/' . $duty->id . '/1/'.$argc) ?>" type="button" class="delete-user" ><i class="fa fa-trash-o" style="font-size:22px; color:red;"></i></a>
                                                </div>
                                                @endif
                                                </div>
                                            </div>
                                                <hr style="width:85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>
                                </div>
                          </div>
                      </div>
                </div>

              <!-- Duties Tab End  -->
              <div id="tab3" class="tab-pane fade">
                <h3>Tab 3</h3>
                <p>Content for tab 3.</p>
              </div>
            </div>
            <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">              
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <img src="" class="imagepreview" style="width: 100%;height: 100%;" >
                    </div>
                    </div>
                </div>
           </div>

           <!-- Change status Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Progress Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="duty_status_form">
                            {{csrf_field()}}
                         <div class="col-sm-12">
                             <input type="hidden" name="duty_id" id="duty_id">
                                <div class="form-group form-float">
                                <label class="form-label">Status</label><span style="color: red;">*</span>    
                                        <div class="form-line">
                                            <select name="status" class="form-control show-tick">
                                               
                                                <option value="" >Select status</option>
                                                <option value="0" >Not Started</option>
                                                <option value="1" >Started</option>
                                                <option value="2" >Testing</option>
                                                @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)))
                                                <option value="5" >Completed</option>
                                                @endif
                                                
                                            </select>
                                        </div> 
                                        @if($errors->has('status'))
                                        <span class="invalid-response" role="alert">{{$errors->first('status')}}</span>
                                        @endif
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="form-line">
                                        <textarea name="comment" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                    <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary change-btn">Save changes</button>
                    </div>
                    
                    </form>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Details Modal -->
             @push('scripts')    
             
           @endpush
                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-body">
                    {{-- <button type="button" class="main-modale-dismiss" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> --}}

                        <div class="modal-content-loader"></div>
                        <div class="duty-details"></div>
                       
                    <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!--<button type="button" class="btn btn-primary change-btn">Save changes</button> -->  
                    </div>
                    </div>
                    </div>
                </div>
                </div>

                 <!--Attachment Comment Details Modal -->
                 <div class="modal fade" id="attachmentCommentmodal" tabindex="-1" role="dialog" aria-labelledby="attachmentModalLabel" aria-hidden="true">
                 <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-body">
                    <button type="button" class="close-attachment-comment-modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                       
                        <div class="modal-content-loader"></div>
                        <div class="attachment-details"></div>
                       
                    <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                        <!--<button type="button" class="btn btn-primary change-btn">Save changes</button> --> 
                        <button type="button" class="btn btn-secondary close-attachment-comment-modal" data-dismiss="modal">Close</button>
                         
                    </div>
                    </div>
                    </div>
                </div>
           </div>
                 <!-- Image Preview Modal -->

            <div class="modal fade" id="imagePreviewmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">              
                    <div class="modal-body">
                        <button type="button" class="close-second-btn"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <img src="" class="imagepreview" style="width: 100%;height: 100%;" >
                    </div>
                    </div>
                </div>
           </div>

               <!-- Image Preview Modal -->

               <div class="modal fade" id="imagePreviewCrouselmodal" tabindex="-1" role="dialog" aria-labelledby="crouselModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">              
                    <div class="modal-body">
                        <button type="button" class="close-second-crousel-btn"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <br><br>
                        <div class="modal-crousel-loader"></div>
                        
                        <div class="attachment-crousel-data"></div>
                    </div>
                    </div>
                </div>
           </div>
        </div>
        {{-- tab code end=============== --}}
    </div>
</section>
@endsection
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/css/purchase.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script>
    $('.delete-user').click(function(){
    if(!confirm('Are you sure to delete duty?')) return false;
});


     function popupImg(index) {
     console.log($('#img-src'+index).attr('src'));
     $('.imagepreview').attr('src', $('#img-src'+index).attr('src'));
			$('#imagemodal').modal('show');   
 }
    function calculatePoints(index) {
        var wattage = $('.point-box-'+index).val();
        var quantity = $('.quantity-box-'+index).val();
        var points = wattage/quantity;
        $('.calculated-point-box-'+index).val(points.toFixed(2));
        console.log(points);
    }
    $(document).ready(function(e) {
        setTimeout(() => {
            $('.alert').css('display', 'none');
        },5000);

        if(window.location.pathname == '/oms/marketer/custom/duties/marketer') {
            $.ajax({
            url: "{{url('/check/pending/custom/duties')}}/",
            type: "GET",
            cache: false,
            success: function(resp) {
                console.log(resp);
                if(resp.status) {
                  if(resp.activity == 1) {
                    if(resp.pending_activities > 0) {
                        $('#pending-duties-box').addClass('alerts-border');
                    }
                    if(resp.testing_activities > 0) {
                        $('#testing-duties-box').addClass('alerts-border');
                    }
                  }
                  
                }else {
                    $('#pending-duties-box').removeClass('alerts-border');
                    
                    $('#pending-duties-box').removeClass('alerts-border');
                }
            }
          });
        }
    });
    function addPoints(index) {
        console.log($('select.duration-box-'+index).val());
        // var checkBox = document.getElementById("myCheck");
        if($('.duty-box-'+index).prop('checked')) {
            $('.point-box-'+index).val(0);
            $('.quantity-box-'+index).val(0);
            $('.btn-group').removeAttr('disabled');
            $('.btn-default').removeAttr('disabled');
            $('.duration-box-'+index).prop('disabled', false);
            $('.quantity-box-'+index).prop('disabled', false);
            var duration = $('select.duration-box-'+index).val();
            if(!duration){
                // var selector = 'select.duration-box-'+index;
                // $(selector+' option[value="0"]').prop('selected', true);
                $('select.duration-box-'+index).val(0).find("option[value='0']").attr('selected', true);
            }
        }else {
            $('.point-box-'+index).val('');
            $('.quantity-box-'+index).val('');
            $('.duration-box-'+index).prop('disabled', true);
            $('.quantity-box-'+index).prop('disabled', true);
            // $('op-'+index+' option[value="0"]').prop('selected', false);
            // $('select.duration-box-'+index+" option:selected").prop("selected", false);
            $("select.duration-box-"+index+ " option:selected").each(function () {
               $(this).removeAttr('selected'); 
               });
        }
        
    }

    function changeStatus(id, title, status) {
        console.log(id+'=='+ title);
        $('#exampleModalLongTitle').html(title);
        $('#duty_id').val(id);
    }

    function dutyDetails(duty, action) {
        // var contents = JSON.parse(duty_details);
        // $('#detailModalTitle').html(contents.title);
        // $('#detail-tag').html(contents.description);
        // $('#dutyId').val(contents.id);
        if(duty) {
            $.ajax({
                url: "{{url('/get/custom/duty/details')}}/"+duty +"/" +action,
                type: "GET",
                cache: false,
                beforeSend: function() {
                    $('.modal-content-loader').css('display', 'block');
                },
                complete: function() {
                    $('.modal-content-loader').css('display', 'none');
                },
                error: function(error) {
                    console.log(error);
                }
            }).then(function(resp) {
                console.log(resp);
                
                $('.modal-content-loader').css('display', 'none');
             $('.duty-details').html(resp);
            });
        }
    }
    
$('.change-btn').click(function(event) {
  event.preventDefault();
  $.ajax({
    url: "{{url('/changes/duty/status')}}",
    type: "POST",
    data: $('#duty_status_form').serialize(),
    cache: false,
    beforeSend: function() {
      $('#savePromoForm').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('#savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('#savePromoForm').html('Save changes');
      $('#savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      if(error.responseJSON.status) {
        $('#error_mesge').text('Please select status.');
      }
      setTimeout(() => {
        $('#error_mesge').text('');
      }, 3500)
    }
    
  }).then(function(resp) {
    // if(resp.status) {
      $('#m_success').show();
      $('#m_success').text('Duty status changed successfully.');
      setTimeout(() => {
        $('.exist_activities').html('');
        $('#duty_name').val('');
        
        $('#custom_list').html(resp);
        $('#m_success').hide();
        $('#exampleModalCenter').modal('toggle');
        // location.reload();
      }, 500)
    // }else{
    //   console.log(resp);
    // }
  })
});

$('.close-second-btn').on('click', function() {
    $('#imagePreviewmodal').modal('toggle');
    $(document).find('#imagePreviewmodal').on('hidden.bs.modal', function () {
    console.log('hiding child modal');
    $('body').addClass('modal-open');
});
});
$('.close-second-crousel-btn').on('click', function() {
    $('#imagePreviewCrouselmodal').modal('toggle');
    $(document).find('#imagePreviewCrouselmodal').on('hidden.bs.modal', function () {
    console.log('hiding child modal');
    $('body').addClass('modal-open');
});
});

$('.close-attachment-comment-modal').on('click', function() {
    console.log("Ok");
    $('#attachmentCommentmodal').modal('toggle');
    $(document).find('#attachmentCommentmodal').on('hidden.bs.modal', function() {
        $('body').addClass('modal-open');
    })
});

function getIrregularDuties(user) {
     if(user) {
        $('.duty_box').css('display', 'none');
        $('.duty_loading').html('<i class="fa fa-spin fa-2x fa-circle-o-notch"></i>');
         $.ajax({
             url: "{{url('/get/user/irregular/duties')}}/"+user,
             type: "GET",
             cache: false,
             success: function(resp) {
                 
                $('.duty_box').css('display', 'inline-block');
                $('.duty_loading').html('');
                var html = '';
                 if(resp.status) {
                    html += '<option value="" >Select Duty</option>';
                    resp.duties.forEach(function callback(value, index) {
                        console.log(value);
                        if(value.custom_duty) {
                            html += '<option value="'+value.custom_duty.id+'" >'+value.custom_duty.name+'</option>';
                        }
                        
                    })
                     console.log(resp);
                 }else {
                    html += '<option value="" >No duty available.</option>';
                }

                var s_html = '<select name="duty" class="form-control show-tick" onchange="getSubDuties(this.value)">';
                var e_html = '</select>';
                var h = s_html+html+e_html;
                $('.duty_box').html(h);
             }
         })
     }else {
        var s_html = '<select name="sub_duty" class="form-control show-tick" ><option value="" >No duty available.</option></select>';
        $('.duty_box').html(s_html);
     }
 }

 function getSubDuties(duty) {
     
     if(duty) {
        $('.sub_duty_box').css('display', 'none');
        $('.sub_duty_loading').html('<i class="fa fa-spin fa-2x fa-circle-o-notch"></i>');
         $.ajax({
             url: "{{url('/get/user/irregular/sub/duties')}}/"+duty,
             type: "GET",
             cache: false,
             success: function(resp) {
                 console.log(resp.duties);
                $('.sub_duty_box').css('display', 'inline-block');
                $('.sub_duty_loading').html('');
                var html = '';
                 if(resp.status) {
                    html += '<option value="" >Select Sub Duty</option>';
                    resp.duties.forEach(function callback(value, index) {
                        console.log(value);
                            html += '<option value="'+value.id+'" >'+value.name+'</option>';
                        
                        
                    })
                     console.log(resp);
                 }else {
                    html += '<option value="" >No duty available.</option>';
                }

                var s_html = '<select name="sub_duty" class="form-control show-tick" >';
                var e_html = '</select>';
                var h = s_html+html+e_html;
                $('.sub_duty_box').html(h);
             }
         })
     }else {
        var s_html = '<select name="sub_duty" class="form-control show-tick" ><option value="" >No duty available.</option></select>';
        $('.sub_duty_box').html(s_html);
     }
 }

 $('main-modale-dismiss').on('cick', function() {
    console.log("OK");
    var myModalEl = document.getElementById('detailModal');
    var modal = bootstrap.Modal.getInstance(myModalEl)
    modal.hide();
 })

</script>

@endpush