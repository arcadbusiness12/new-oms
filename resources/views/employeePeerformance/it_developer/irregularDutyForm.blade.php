@extends('layouts.app')

@section('content')
<style>
    .file-drop-area {
    position: relative;
    display: flex;
    align-items: center;
    max-width: 100%;
    padding: 25px;
    border: 1px dashed rgba(255, 255, 255, 0.4);
    border-radius: 3px;
    transition: .2s
}

.choose-file-button {
    flex-shrink: 0;
    background-color: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    padding: 8px 15px;
    margin-right: 10px;
    font-size: 12px;
    text-transform: uppercase
}

.file-message {
    font-size: small;
    font-weight: 300;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
}

.file-input {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    widows: 100%;
    cursor: pointer;
    opacity: 0
}
img {
    width: 150px; height:100px; padding: 10px
}
</style>
<section class="content">
    <div class="container-fluid">
        <form action="<?php echo URL::to('/save/assigned/custom/duty') ?>" method="post" enctype="multipart/form-data" name="form-setting">
            {{ csrf_field() }}
            <div class="block-header">
                <div class="pull-left">
                    <h2><?php if(isset($duty_details['id'])) {echo 'Edit Custom Duty';} else {echo 'Assign Custom Duty';} ?></h2>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i></button>
                    <a href="<?php echo URL::to('/designer/custom/duties') ?>"><button type="button" class="btn btn-info"><i class="fa fa-reply"></i></button></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row clearfix">
                <div class="col-sm-12">
                    <?php if(Session::has('message')) { ?>
                    <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo Session::get('message') ?>
                    </div>
                    <?php } ?>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="card" style="padding: 15px;">
                        <div class="row">
                            <input type="hidden" name="action" value="{{$action}}">
                            <input type="hidden" name="irregular" value="1">
                            <div class="col-sm-3">
                                <div class="form-group form-float">
                                        <label class="form-label">User Group</label>    
                                        <div class="form-line">
                                            <select name="user_group" class="form-control show-tick" onchange="getUsers(this.value)">
                                                <option value="" >Select User Group</option>
                                                @foreach($userGroups as $group)
                                                @php
                                                    $u = ($user) ? $user['user_group_id'] : '';
                                                @endphp
                                                <option value="{{@$group['id']}}" >{{@$group['name']}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div> 
                                        @if($errors->has('user_group'))
                                            <span class="invalid-response" role="alert">{{$errors->first('user_group')}}</span>
                                            @endif 
                                </div>
                            </div>
                            <div class="col-sm-3">
                                        <label class="form-label">User</label>  
                                            <span class="user_loading"></span><br> 
                                            <div class="schedule_group col-sm-12">
                                            <select name="user" class="form-control user-select" onchange="getIrregularDuties(this.value)">
                                            <option>Select User </option>
                                            @if(@$users)
                                            @foreach(@$users as $userr)
                                            @php
                                                $u = ($user) ? $user['user_id'] : '';
                                            @endphp
                                                <option value="{{$userr['user_id']}}" <?php if($userr['user_id'] == @$u) {echo 'selected';} ?>>{{$userr['username']}}</option>
                                                @endforeach
                                            @endif
                                            </select>
                                            </div>
                                        @if($errors->has('user'))
                                            <span class="invalid-response" role="alert">{{$errors->first('user')}}</span>
                                            @endif 
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-float">
                                <label class="form-label">Duty</label>   
                                        <span class="duty_loading"></span><br>  
                                        <div class="form-line duty_box">
                                            <select name="duty" class="form-control show-tick" onchange="getSubDuties(this)">
                                                @foreach($irregularDutyLists as $duty)
                                                <option value="{{$duty['customDuty']['id']}}">{{$duty['customDuty']['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                        @if($errors->has('duty'))
                                        <span class="invalid-response" role="alert">{{$errors->first('duty')}}</span>
                                        @endif
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-float">
                                <label class="form-label">Sub Duty</label>   
                                        <span class="sub_duty_loading"></span><br>  
                                        <div class="form-line sub_duty_box">
                                            <select name="duty" class="form-control show-tick">
                                                @foreach($irregularDutyLists as $duty)
                                                <option value="{{$duty['customDuty']['id']}}">{{$duty['customDuty']['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                        @if($errors->has('duty'))
                                        <span class="invalid-response" role="alert">{{$errors->first('duty')}}</span>
                                        @endif
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="hidden" name="id" value="{{@$duty_details['id']}}" class="form-control" autocomplete="off" required />
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" value="<?php echo @$duty_details['title'] ?>" class="form-control" autocomplete="off" required />
                                    </div>
                                    @if($errors->has('title'))
                                        <span class="invalid-response" role="alert">{{$errors->first('title')}}</span>
                                        @endif
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <div class="form-line">
                                        <textarea name="description" class="form-control">{{@$duty_details['description']}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">From Date</label>
                                    <div class="form-line">
                                        <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" placeholder="Date From" value="{{@$duty_details['start_date']}}">
                                    </div>
                                    @if($errors->has('date_from'))
                                        <span class="invalid-response" role="alert">{{$errors->first('date_from')}}</span>
                                        @endif
                                </div>
                            </div>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <div class="form-line">
                                    <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" placeholder="Date To" value="{{@$duty_details['end_date']}}">
                                </div>
                                @if($errors->has('date_to'))
                                        <span class="invalid-response" role="alert">{{$errors->first('date_to')}}</span>
                                        @endif
                            </div>
                            </div>

                            <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">Event Date</label>
                                <div class="form-line">
                                <input type="text" name="date_event" id="event_event" class="datepicker form-control" autocomplete="off" placeholder="Date Event" value="">
                                </div>
                                @if($errors->has('date_event'))
                                        <span class="invalid-response" role="alert">{{$errors->first('date_event')}}</span>
                                        @endif
                            </div>
                            </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Active/In-Active</label>
                                <div class="form-line">
                                <select name="is_close" class="form-control show-tick">
                                               <option value="0" {{(@$duty_details['is_close'] == 0) ? 'selected' : '' }}>Active</option>
                                               <option value="1" {{(@$duty_details['is_close'] == 1) ? 'selected' : '' }}>In-Active</option>
                                               
                                           </select>                                </div>
                                @if($errors->has('is_close'))
                                        <span class="invalid-response" role="alert">{{$errors->first('is_close')}}</span>
                                        @endif
                            </div>
                            </div>
                            <hr>
                            <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Files</label>
                                <div class="hidden-file">

                                </div>
                                <div class="file-drop-area"> <span class="choose-file-button">Choose Files</span> 
                                <span class="file-message">or drag and drop files here</span> 
                                <input type="file" name="file[]" class="file-input" id="files" multiple>
                                <a href="javascript:;" onclick="clearfiles()" class="clear-tag" style="display:none;"> <span style="padding-left: 72px; color:red;">Clear</span></a>
                             </div>
                             <div id="divOldImageMediaPreview">
                                 @if(isset($duty_details) && count($duty_details['files']) > 0)
                                    @foreach($duty_details['files'] as $k => $file)
                                    @php $flag = false; @endphp
                                    <div class="old-file-div{{$k}}{{$file->id}}" style="width: 150px;position: relative; left: 0; top: 0;display:inline-block; float:left;">
                                    @if(in_array($file->extension, $extensions)) 
                                    @php $flag = false; @endphp
                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: 150px; height:100px; padding: 10px;position: relative;top: 0;left: 0;" src="{{asset($file->file)}}" id="image-tag0"></a>
                                    @else
                                    @php $flag = true; @endphp
                                    <a href="{{asset($file->file)}}" download><i class="fa fa-download"></i>Dwonload</a>
                                    @endif
                                      <a href="javascript:;" style="position: absolute;right: 5px; color:red;" onclick="removeOldImage('{{$k}}', '{{$file->id}}')"><i class="fa fa-times-circle-o fa-lg"></i></a>  
                                      @if($flag)
                                      <h5>Download your file </h5>
                                      @endif
                                    </div>
                                    @endforeach
                                 @endif   
                            </div>
                                <div id="divImageMediaPreview"> </div>
                            </div>
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
    </div>
</section>
@endsection
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/css/purchase.css')}}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />

<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

<script>
$(function () {
  $(".datepickerr").datepicker({ 
        autoclose: true, 
        todayHighlight: true,
        format: 'yyyy-mm-dd'
  });
});
function removeOldImage(index, image) {
    if(image) {
        if(confirm('Are sure ?')) {
            $.ajax({
                url: "{{url('/remove/duty/old/file')}}/"+image,
                type: "GET",
                cache: false,
                success: function(respo) {
                    if(respo.status) {
                        $('.old-file-div'+index+image).remove();
                    }
                }
            })
        }
    }
}

$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
        var dvPreview = $("#divImageMediaPreview");
      var files = e.target.files,
      
        filesLength = files.length;
        console.log(e.target);
        var counter = 0;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        console.log(i);
        // $('.hidden-file').append('<input type="file" name="files[]" id="filess" value="'+f+'">');
        // <input type="file" name="file[]" class="file-input" id="files" accept=".jfif,.jpg,.jpeg,.png,.gif" multiple>
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          var img = "<div class='new-file-div"+counter+"' style='width: 150px;position: relative; left: 0; top: 0;display:inline-block; float:left;'>" +
            "<a href='javascript:;' onclick='popupImg("+counter+")'><img id='img-src"+counter+"' class=\"imageThumb\ pop\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "</a> </div> ";
          
            dvPreview.append(img);
          $(".remove").click(function(){
            $(this).parent(".new-file-div").remove();
          });
          
          // Old code here
          /*$("<img></img>", {
            class: "imageThumb",
            src: e.target.result,
            title: file.name + " | Click to remove"
          }).insertAfter("#files").click(function(){$(this).remove();});*/
          counter++;
        });
        fileReader.readAsDataURL(f);
        
      }
      $('.clear-tag').css('display','inline-block');
    });
    
  } else {
    alert("Your browser doesn't support to File API")
  }
});
 function popupImg(index) {
     console.log($('#img-src'+index).attr('src'));
     $('.imagepreview').attr('src', $('#img-src'+index).attr('src'));
			$('#imagemodal').modal('show');   
 }

 function clearfiles() {
     $('.file-input').val('');
     $("#divImageMediaPreview").html('');
    $('.clear-tag').css('display','none');
 }

 function getUsers(group) {
     $('.schedule_group').css('display', 'none');
    $('.user_loading').html('<i class="fa fa-spin fa-2x fa-circle-o-notch"></i>');
    if(group) {
        $.ajax({
            url: "{{url('/get/irregular/group/users')}}/"+group,
            type: "GET",
            cache: false,
            success: function(resp) {
                    console.log(resp);
            $('.user_loading').html('');
                var html = '';
                if(resp.status) {
                    $('.schedule_group').css('display', 'inline-block');
                    html += '<option value="" >Select User</option>';
                    resp.users.forEach(function callback(value, index) {
                    html += '<option value="'+value.user_id+'" >'+value.username+'</option>';
                    });
                }else {
                    html += '<option value="" >Not available.</option>';
                }
                
                // $('.user-select').html(html);
                    // $('.user-select').val(html);
                    // $(".user-select").selectpicker("refresh");
                var s_html = '<select name="user" id="sub_catess" class="form-control" onchange="getIrregularDuties(this.value)">';
                var e_html = '</select>';
                var h = s_html+html+e_html;
                $('.schedule_group').html(h);
                
            }
        });
    }
 }

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
//  $(document).on('change', '.file-input', function() {


//         var filesCount = $(this)[0].files.length;

//         var textbox = $(this).prev();

//         if (filesCount === 1) {
//         var fileName = $(this).val().split('\\').pop();
//         textbox.text(fileName);
//         } else {
//         textbox.text(filesCount + ' files selected');
//         }



//         if (typeof (FileReader) != "undefined") {
//             console.log("ok");
//         var dvPreview = $("#divImageMediaPreview");
//         dvPreview.html("");
//         var count = 0;
//         $($(this)[0].files).each(function (k) {
//         var file = $(this);
//         extension = file[0].name.split('.').pop();
//         if(extension == 'jpg' || extension == 'jpeg' ||
//           extension == 'JPEG' ||  extension == 'png' ||
//           extension == 'PNG' ||  extension == 'gif') {
//             var reader = new FileReader();
//             reader.onload = function (e) {
                
//             // var atag = "<a href='javascript:;' id='image-tag"+k+"'>";
//             // atag.attr("id", "image-tag"+k);
//             var img = $("<img />");
//             // console.log(img);
//             img.attr("style", "width: 150px; height:100px; padding: 10px");
//             img.attr("src", e.target.result);
//             img.attr("id", "image-tag"+k);
//             // console.log($("#image-tag"+k));
//             // console.log(atag+img+atagcl);
//             dvPreview.append(img);
//             }
//             reader.readAsDataURL(file[0]);
//           }
//          count++;
//         });
//         } else {
//         alert("This browser does not support HTML5 FileReader.");
//         }


// });


</script>
@endpush