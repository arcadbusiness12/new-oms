<div class="col-sm-3 col-sm-3 col-xs-12 duty-block" >
    <div class="card" id="pending-duties-box" style="padding: 10px;overflow: hidden;">
                <label class="form-label">To Do</label>
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
                          <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                          @if(in_array($file->extension, $extensions)) 
                          @php $flag = false; @endphp
                          <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                          <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0">
                          {{-- @else
                          @php $flag = true; @endphp
                          <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                          <i class="fa fa-download"></i>Dwonload --}}
                          @endif
                          
                          {{-- @if($flag)
                          <h5>Download your file </h5>
                          @endif --}}
                          </div>
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
    <div class="card" style="padding: 10px;overflow: hidden;">
                <label class="form-label">Doing</label>
                {{-- <label class="form-label duties-count">({{count($started)}})</label> --}}
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
                              <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                              @if(in_array($file->extension, $extensions)) 
                              @php $flag = false; @endphp
                              <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                              <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0">
                              {{-- @else
                              @php $flag = true; @endphp
                              <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                              <i class="fa fa-download"></i>Dwonload --}}
                              @endif
                              
                              {{-- @if($flag)
                              <h5>Download your file </h5>
                              @endif --}}
                              </div>
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
        
                <label class="form-label">Testing</label>
                {{-- <label class="form-label duties-count">({{count($in_testing)}})</label> --}}
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
                          <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                          @if(in_array($file->extension, $extensions)) 
                          @php $flag = false; @endphp
                          <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                          <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0">
                          {{-- @else
                          @php $flag = true; @endphp
                          <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                          <i class="fa fa-download"></i>Dwonload --}}
                          @endif
                          
                          {{-- @if($flag)
                          <h5>Download your file </h5>
                          @endif --}}
                          </div>
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
    <div class="card" style="padding: 10px;overflow: hidden;">
                <label class="form-label">Completed</label>
                {{-- <label class="form-label duties-count">({{count($completed)}})</label> --}}
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
                          <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                          @if(in_array($file->extension, $extensions)) 
                          @php $flag = false; @endphp
                          <!-- <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '108px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a> -->
                          <img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['attachmentFiles']) > 1) ? '111px' : '100%'}}; height:{{(count($duty['attachmentFiles']) > 1) ? '100px' : 'auto'}}; padding: 5px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0">
                          {{-- @else
                          @php $flag = true; @endphp
                          <!-- <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a> -->
                          <i class="fa fa-download"></i>Dwonload --}}
                          @endif
                          
                          {{-- @if($flag)
                          <h5>Download your file </h5>
                          @endif --}}
                          </div>
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

<script>
    function changeStatus(id, title, status) {
        console.log(id+'=='+ title);
        $('#exampleModalLongTitle').html(title);
        $('#duty_id').val(id);
    }
    $('.delete-user').click(function(){
    if(!confirm('Are you sure to delete duty?')) return false;
});

$(document).ready(function() {
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
</script>