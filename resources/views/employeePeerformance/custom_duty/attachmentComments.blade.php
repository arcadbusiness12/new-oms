<style>
  .cover-img {
    text-align: center;
   }
  /*.cover-img img {
    height: 160px;
    min-height: 160px;
  } */
  h4,h5 {
      color: #172b4d;
  }
  .progress-title {
    margin: 0px 27px 12px;
  }
  .content-div {
    margin-left: 14px;
  }
  .textarea-box {
      border: 2px solid #a5e2ff !important;
  }
  .form-group {
    margin-bottom: 5px;
}
.close-form {
    color: red;
}
.comment-section {
    padding-top: 40px;
}
@media (min-width: 768px) {
  .modal-xl {
    width: 100%;
   max-width:1200px;
  }
}
#descrip-tex {
    text-align: justify;
}
.comment-text {
    text-align: justify;
}
a{
    text-decoration: none !important;
}

.sweet-alert {
    width: 305px !important;
}
.attachment-input {
  position: relative;
  overflow: hidden;
}

#attachment-input {
  position: absolute;
  font-size: 50px;
  opacity: 0;
  right: 0;
  top: 0;
}
.comment-time{
    font-size: 12px;
}
.comment-box {
    margin-bottom: 0px;
}
.comment-action {
    color:#707476;font-size:12px;
}
.comment-list {
    padding-left: 28px;
}
.comment-reply-list {
    padding-left: 58px;
}
.green {
    color: green;
}
.gray {
    color: gray;
}
.cover-text {
    padding-left: 12px;
}
.add-btn {
    text-align: right;
}
.reply {
    padding-left: 42px;
}
.comment_reply_form {
    padding-left: 55px;
}
#comment-body {
    width: 98%;
    max-width: 98%;
}
#main-row {
    margin-right: 16px;
    margin-left: 0px;
}

</style>
            <div class="sub-setting-loop col-md-12" >
                      <div class="row">
                         <div class="col-sm-12 cover-img" >
                            @if(in_array($attachment['extension'], $extensions))
                                <img src="{{asset($attachment['file'])}}" style="height:160px; min-height:160px;">                               
                                @else
                                <video width="400" controls class="video-tag">
                                <source src="{{asset($attachment['file'])}}" type="video/{{$attachment['extension']}}">
                                <source src="mov_bbb.ogg" type="video/ogg">
                                Your browser does not support HTML video.
                                </video>
                                @endif
                             
                         </div>
                        </div>
                        <div class="row" id="main-row">
                            <div class="col-sm-12" id="comment-body">
                            <!-- comment box -->
                            <div class="row comment-section">
                                <div class="form-line col-sm-12">
                                    <h5><i class="fa fa-comment" aria-hidden="true"></i> Comment</h5>
                                </div>
                            </div>
                            <!-- comment form  -->
                            <div class="row comment-box">
                                <form name="comment_form" class="comment_form" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                        <div class="row">
                                         <input type="hidden" name="coment_duty_id" id="coment_duty_id" value="{{$attachment['custom_duty_id']}}">
                                         <input type="hidden" name="file_id" id="file_id" value="{{$attachment['id']}}">
                                            <div class="form-line col-sm-12" style="padding-top: 15px;margin-left: 14px; ">
                                                <!-- <input type="text" class="form-control"> -->
                                                <textarea name="comment" rows="2" class="form-control textarea-box" id="comment_box"></textarea>
                                                
                                            </div>
                                        </div>
                                        <div class="row error-alert" style="text-align: center;display:none;">
                                            <span class="text-danger">Comment field is required..</span>
                                        </div>
                                        <div class="row" style="padding-top: 15px;">
                                            
                                            <!-- <div class="col-sm-10">
                                                <div class="input-group">
                                                        <input type="file" name="comment_file" id="attachment-input" class="form-control comment-file"
                                                        onchange="$('#upload-file-info').html(
                                                                (this.files.length > 1) ? this.files.length + ' files' : this.files[0].name)">                     
                                                       <span type="button" class="btn btn-secondary attachment-input">Attachment</span>&hellip;
                                                    <span class='labe' id="upload-file-info"></span>
                                            </div>
                                            </div> -->
                                            <div class="col-sm-12 add-btn">
                                                <button type="button" class="btn btn-success attachment-comment-save">Add</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                            <!-- Display posted comments  -->
                            
                            <!-- for current comment  -->
                            <div class="attachment-current-comment"></div>
                            
                        @if(count($attachment['comments']) > 0)
                            @foreach($attachment['comments'] as $k => $comment)
                            <div class="row attachment-row{{$comment['id']}} comment-list">
                                <div class="row">
                                    <div class="form-line col-sm-6">
                                    <a href='javascript:;' ><h4 style="display:inline-block">{{ucwords($comment->user->username)}} {{ucwords($comment->user->lastname)}}</h4></a> <span class="comment-time">{{date('d M', strtotime($comment->created_at))}} {{date('h:i A', strtotime($comment->created_at))}}</span>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-line col-sm-12" style="">
                                        @if($comment['file']['file']) 
                                        @php $file = Storage::url($comment['file']['file']); @endphp
                                        <div class="row" style="padding-left: 58px">
                                        <a href='javascript:;' onclick='popupImg("{{$k}}{{$comment['id']}}")'><img id='img-src{{$k}}{{$comment['id']}}' style="width:16%; max-height:100px;" src="{{asset($file)}}"></a>
                                           
                                        </div>
                                        @endif
                                        <div class="row" style="padding-left: 58px">
                                            <p style="color:black;border-bottom: 1px solid gainsboro;" class="comment-text-section{{$comment->id}}{{$k}} comment-text">
                                                {{$comment->comment}}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reply section  -->
                                <div class="current-comment-reply{{$comment['id']}}" id="current-comment-reply{{$comment['id']}}"></div>
                                @if(count($comment['replies']) > 0)
                                @foreach($comment['replies'] as $k => $reply)
                                 <div class="row attachment-row{{$reply->id}} comment-reply-list">
                                    <div class="row">
                                        <div class="form-line col-sm-6">
                                        <a href='javascript:;' ><h5 style="display:inline-block">{{ucwords($reply->user->username)}} {{ucwords($reply->user->lastname)}}</h5></a> <span class="comment-time">{{date('d M', strtotime($reply->created_at))}} {{date('h:i A', strtotime($reply->created_at))}}</span>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-line col-sm-12" style="">
                                            @if(count($reply['files']) > 0)
                                             @foreach($reply['files'] as $k => $file)
                                             @php $image = Storage::url($file['file']) @endphp
                                            <div class="row" style="padding-left: 58px">
                                            <a href='javascript:;' onclick='popupImg("{{$k}}{{$file['id']}}")'><img id='img-src{{$k}}{{$file['id']}}' style="width:16%; max-height:100px;" src="{{asset($image)}}"></a>
                                            
                                            </div>
                                            @endforeach
                                            @endif
                                            <div class="row" style="padding-left: 58px">
                                                <p style="color:black;border-bottom: 1px solid gainsboro;" class="comment-text-section{{$reply->id}}{{$k}} comment-text">
                                                    {{$reply->reply_comment}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            @endif
                            </div>
                            <div class="row reply-row comment-list">
                             <a href="javascript:;" class="reply" onclick="commentReply('{{$comment['id']}}','{{$k}}')"><span>Reply</span></a>
                            </div>
                            <div class="row" id="comment-reply-box{{$comment['id']}}{{$k}}" style="display: none;">
                                <form name="comment_reply_form" class="comment_reply_form" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                        <div class="row">
                                         <input type="hidden" name="coment_duty_id" id="coment_duty_id{{$comment['id']}}{{$k}}" value="{{$attachment['id']}}">
                                         <input type="hidden" name="comment_id" id="comment_id{{$comment['id']}}{{$k}}" value="{{$comment['id']}}">
                                            <div class="form-line col-sm-12" style="padding-top: 15px;margin-left: 14px; ">
                                                <!-- <input type="text" class="form-control"> -->
                                                <textarea name="comment_reply{{$comment['id']}}{{$k}}" rows="2" class="form-control textarea-box comment-reply-box{{$comment['id']}}{{$k}}" id=""></textarea>
                                                
                                            </div>
                                        </div>
                                        <div class="row error-alert{{$comment['id']}}{{$k}}" style="text-align: center;display:none;">
                                            <span class="text-danger">Comment field is required..</span>
                                        </div>
                                        <div class="row" style="padding-top: 15px;">
                                            
                                            <div class="col-sm-1">
                                                <div class="input-group">
                                                        <input type="file" name="comment_reply_file" id="attachment-input" readonly class="form-control comment-reply-file{{$comment['id']}}{{$k}}">                     
                                                       <!-- <span type="button" class="btn btn-secondary attachment-input">Attachment</span>&hellip; -->
                                                    <!-- <span class="labe choose_file{{$k}}{{$comment['id']}}" id="upload-file-info{{$k}}{{$comment['id']}}"></span> -->
                                            </div>
                                            </div>
                                            <div class="col-sm-11 add-btn">
                                                <button type="button" class="btn btn-success reply-save{{$comment['id']}}{{$k}}" id="reply-save{{$comment['id']}}{{$k}}"  onclick="saveReply('{{$comment['id']}}','{{$k}}')">Reply</button>
                                                <button type="button" class="btn btn-secondary close-form" onclick="closecommentReplyForm('{{$comment['id']}}','{{$k}}')">X</button>

                                            </div>
                                        </div>
                                </form>
                            </div>
                            @endforeach
                         @endif
                         </div>
                         
                      </div>

                
                    

            </div>
           
           
            <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
            
            <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
            
<script>

function generalComment(data, per_class) {
     console.log("Called");
     console.log(data);
     var div = '';
     let date = new Date(data.comment.created_at); // 2020-06-21
                    let month = date.toLocaleString('en-us', { month: 'short' }); 
                    var hour    = date.getHours();
                    var minute  = date.getMinutes();
                    var second  = date.getSeconds(); 
                    var time = hour+":"+minute+":"+second;
     
    var html = '<div class="row attachment-row'+data.comment.id+' comment-list '+per_class+'">'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-6">'+'\n'+
                                    '<a href="javascript:;" ><h4 style="display:inline-block">'+data.user+'</h4></a> <span class="comment-time">'+month+' '+time+'</span>'+'\n'+

                                    '</div>'+'\n'+
                                '</div>'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-12" style=""><div class="row" style="padding-left: 58px">'+'\n'+
                                            '<p style="color:black" class="comment-text-section comment-text">'+data.comment.comment+'</p>'+'\n'+
                                        '</div>'+'\n'+
                                    '</div>'+'\n'+
                                '</div>'+'\n'+
                            '</div>';

    return html;
 }


$('.attachment-comment-save').on('click', function(event) {
    console.log($('#comment_box').val());
    console.log("yes");
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var comment = $('#comment_box').val();
    // var file = $('.comment-file')[0].files[0];
    if(comment) {
            $('.comment-save').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.comment-save').prop('disabled', true);
        var formData = new FormData();
        formData.append('duty_id', $('#coment_duty_id').val());
        formData.append('file_id', $('#file_id').val());
        formData.append('comment', comment);
        // formData.append('comment_file', file);
        formData.append('_token', CSRF_TOKEN);
        $.ajax({
            url: "{{url('save/attachment/comment')}}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $('.comment-save').html('Add');
                $('.comment-save').prop('disabled', true);
                // console.log(resp);return;
                var current_comment = generalComment(resp, 'a');
                $('.attachment-current-comment').prepend(current_comment);
                $('#comment_box').val('')
                $('.comment-file').val(null);
                $('.labe').html('');
            }
        })
    }else {
       $('.error-alert').css('display','block');
       setTimeout(() => {
        $('.error-alert').css('display','none');
       },3500);
        return;
    }
});

function editComment(section) {
    console.log($('#comment-textarea'+section).val().length);
    if($('#comment-textarea'+section).val().length > 170) {
        $('#comment-textarea'+section).attr('rows', 5);
    }else {
        $('#comment-textarea'+section).attr('rows', 2);
    }
    $('.comment-text-section'+section).css('display', 'none');
    $('.comment-form'+section).css('display', 'block');
}

</script>