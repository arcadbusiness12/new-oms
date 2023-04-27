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
    margin-bottom: 15px;
}
.comment-action {
    color:#707476;font-size:12px;
}
.comment-list {
    padding-left: 28px;
}
.comment-reply-list {
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
    padding-left: 12px;
}
.reply-to-reply {
    padding-left: 72px;
}
.comment_reply_form {
    padding-left: 55px;
}
.comment_reply_to_reply_form {
    padding-left: 100px;
}
.Reply-to-reply {
    padding-left: 100px !important;
}
.comment-reply-to-reply-list {
    padding-left: 100px !important;
}
.reply-to-reply-list-ap {
    padding-left: 72px !important;
}
.right-heading {
    background-color: #f6f3f3;
    padding-left: 12px;
}
.text-muted {
    color: none;
}
.video-tag {
    padding-left: 12px;
    padding-top: 12px;
}
.left-padding {
    padding-left: 24px;
}
.carousel-control{
    width: 8% !important;
}
.main-title {
    display: inline-block;
}
.move-history {
    padding-left: 5px;
}
.user-history {
    font-weight: bold;
}
.status-history {
    margin: 0 0 5px !important;
}
</style>
            <div class="sub-setting-loop col-md-12" >
                      <div class="row">
                         <div class="col-sm-12 cover-img" >
                             <img src="{{asset($details['cover'])}}" style="height: {{$details['cover'] ? '160px' : '0px'}}; min-height: {{$details['cover'] ? '160px' : '0px'}}; display: {{$details['cover'] ? 'inline-block' : 'none'}};">
                             
                         </div>
                        </div>
                        <div class="row" style="padding-bottom: 14px;">
                            <div class="col-sm-12">
                                    <h4 class="main-title"><i class="fa fa-credit-card" aria-hidden="true"></i> {{$details['title']}}</h4> <span>({{$details->user->firstname}} {{$details->user->lastname}})</span><br>
                                    <p class="progress-title" style="display: inline-block;">
                                        @if($details['progress'] == 0)
                                            In list To Do 
                                        @elseif($details['progress'] == 1)
                                            In list Doing  
                                        @elseif($details['progress'] == 2)
                                            In list Testing
                                        @else
                                            In list Completed
                                        @endif
                                        <i class="fa fa-eye"></i></p> 

                                        <div class="btn-group btn-toggle"> 
                                        <button class="btn btn-xs lable-status  {{($details->is_close == 0) ? 'active btn-success' : 'btn'}}" style="padding:6px;" id="lable-status0" >Active</button>
                                        <button class="btn btn-xs lable-status {{($details->is_close == 1) ? 'active btn-danger' : 'btn'}}" style="padding:6px;" id="lable-status1">In-Active</button>
                                        </div>
                                    <!-- <div class="form-line">
                                        <textarea name="comment" class="form-control"></textarea>
                                    </div> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8" >
                            <div class="row">
                                <div class="form-line col-sm-12">
                                    <h5><i class="fa fa-file-text" aria-hidden="true"></i> Description 
                                        @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)))
                                        <a href="javascript:;" onclick="addDescription()"><button type="button" class="btn btn-secondary">Edit</button></a>
                                    @endif
                                    </h5>

                                </div>
                                <div class="form-line col-sm-12 content-div">
                                    <div class="descrip-text">
                                        @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)))
                                        <a href="javascript:;" onclick="addDescription()">
                                            <p id="descrip-tex">{{$details['description'] ? $details['description'] : 'Add more details' }}</p>
                                        </a>
                                        @else 
                                        <p id="descrip-tex">{{$details['description'] ? $details['description'] : '' }}</p>
                                        @endif
                                    </div>
                                    <div class="descrip-form" style="display: none;">
                                        <form name="description_form" class="description_form" method="post">
                                            {{csrf_field()}}
                                            <div class="form-group">
                                                <input type="hidden" name="duty_id" value="{{$details['id']}}">
                                                <textarea name="description" class="form-control textarea-box" rows="5" id="textarea-box">{{$details['description'] ? $details['description'] : 'Add more details' }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <button type="button" class="btn btn-primary save-change-btn">Save</button> 
                                                <button type="button" class="btn btn-secondary close-form" onclick="closeDescriptionForm()">X</button>
                                            </div>  
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-line col-sm-12">
                                    <h5><i class="fa fa-paperclip" aria-hidden="true"></i> Attachments</h5>
                                </div>
                            </div>
                            <div class="row append-attachment-row"></div>
                            @foreach($details['attachmentFiles'] as $k => $file)
                            <div class="row attachment-row{{$file->id}}">
                            @if(in_array($file->extension, $extensions))
                                @php $file_col = 'col-sm-3'; $comment_col = 'col-sm-9'; $cover_text = true; $comment_left = ''; @endphp
                            @else
                                @php $file_col = 'col-sm-5'; $comment_col = 'col-sm-7'; $cover_text = false; $comment_left = 'left-padding' @endphp
                            @endif
                                <div class="form-line {{$file_col}}">
                                @if(in_array($file->extension, $extensions))
                                @if(in_array($file->extension, $image_extensions)) 
                                @php $flag = false; @endphp
                                <a href='javascript:;' onclick='popupImgCrousel("{{$file->id}}","{{$details['id']}}")'><img id='img-src{{$k}}{{$file->id}}' style="width:129px; max-height:100px; padding: 10px;position: relative;top: 0;left: 0;max-height: 125px;border-radius: 12%;" src="{{asset($file->file)}}"></a>
                                @else
                                  @php $flag = true; @endphp
                                  <a href="{{asset($file->file)}}" style="padding-left: 14px; color:red;vertical-align: -webkit-baseline-middle;" download><i class="fa fa-download"></i> Dwonload</a>
                                @endif
                                @else
                                <video width="230" controls class="video-tag">
                                <source src="{{asset($file->file)}}" type="video/webm">
                                <source src="mov_bbb.ogg" type="video/ogg">
                                Your browser does not support HTML video.
                                </video>
                                @endif
                                    </span></a>
                                </div>
                            @if(count($file['comments']) > 0)
                                <div class="form-line {{$comment_col}} {{$comment_left}}">
                                <div class="row">
                                <a href='javascript:;' ><h5 style="display:inline-block">{{ucwords($file['comments'][0]->user->username)}} {{ucwords($file['comments'][0]->user->lastname)}}</h5></a> <span class="comment-time">{{date('d M', strtotime($file['comments'][0]->created_at))}} {{date('h:i A', strtotime($file['comments'][0]->created_at))}}</span>
                                </div>
                                        <div class="row" style="margin-right: 0px;">
                                         <span>{{substr($file['comments'][0]->comment, 0, 120)}}..</span>
                                        </div>
                                        <!-- <div class="row">
                                            <a href="javascript:;" onclick="makeCover('{{$file->id}}', '{{$details['id']}}','{{asset($file->file)}}')"><span><i class="fa fa-desktop" aria-hidden="true"></i> Make Cover <span class="cover-img-text{{$file->id}} cover-img-text" >{{($file->cover == 1) ? '(Cover Image)' : ''}}</span></span></a>
                                        </div> -->
                                </div>
                            @endif
                            </div>
                            <div class="row attachment-action-row{{$file->id}}">
                                    <div class="col-sm-3" >
                                        @if($cover_text)
                                        @php $icon = ($file->cover == 1) ? 'green' : 'gray'; @endphp
                                        <a href="javascript:;" onclick="makeCover('{{$file->id}}', '{{$details['id']}}','{{asset($file->file)}}')" class="cover-text "><i class="fa fa-desktop {{$icon}} cover-icon cover-icon{{$file->id}}" aria-hidden="true"></i><span class="cover-img-text{{$file->id}} cover-img-text"> {{($file->cover == 1) ? 'Cover Image' : 'Make Cover'}}</span></a>
                                            <!-- {{($file->cover == 1) ? '(Cover Image)' : ''}} -->
                                        @endif
                                    </div>
                                    <div class="col-sm-8" >
                                        <span>{{date('d M', strtotime($file->created_at))}} {{date('h:i A', strtotime($file->created_at))}}</span> - <a href="javascript:;" onclick="removeAttachment('{{$file->id}}', '{{$file->cover}}')"><span>Delete</span></a>
                                        
                                        - <a href="javascript:;" onclick="attachmentcomments('{{$file->id}}')" data-toggle="modal" data-target="#attachmentCommentmodal"><span>Comments({{count($file['comments'])}})</span></a>
                                       
                                    </div>
                             </div>
                            @endforeach
                            <div class="row">
                                <div class="form-line col-sm-12" style="padding-top: 15px;margin-left: 14px; ">
                                <form name="attachment_form" id="attachment_form" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="duty_idd" id="duty_idd" value="{{$details['id']}}">
                                    <input type="hidden" name="file" value="sadasdasdasdas">
                                 <span type="button" class="btn btn-secondary attachment-input">Add an attachment <i class="fa fa-spinner fa-pulse" id="attachment-loader" style="display: none;"></i></span>
                                 <input type="file" name="attachment" id="attachment-input">
                                 <div id="error-worning"></div>
                                </form>
                                </div>
                            </div>


                            <!-- comment box -->
                            <div class="row comment-section" style="padding-top: {{count($details['attachmentFiles']) == 0 && session('role') == 'ADMIN' ? '80px;' : '55px;'}}">
                                <div class="form-line col-sm-12">
                                    <h5><i class="fa fa-comment" aria-hidden="true"></i> Comments</h5>
                                </div>
                            </div>
                            <!-- comment form  -->
                            <div class="row comment-box">
                                <form name="comment_form" class="comment_form" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                        <div class="row">
                                         <input type="hidden" name="coment_duty_id" id="coment_duty_id" value="{{$details['id']}}">
                                            <div class="form-line col-sm-12" style="padding-top: 15px;margin-left: 14px; padding-right: 32px;">
                                                <!-- <input type="text" class="form-control"> -->
                                                <textarea name="comment" rows="2" class="form-control textarea-box" id="comment-box"></textarea>
                                                
                                            </div>
                                        </div>
                                        <div class="row error-alert" style="text-align: center;display:none;">
                                            <span class="text-danger">Comment field is required..</span>
                                        </div>
                                        <div class="row" style="padding-top: 15px;">
                                            
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                        <input type="file" name="comment_file" id="attachment-input" class="form-control comment-file"
                                                        onchange="$('#upload-file-info').html(
                                                                (this.files.length > 1) ? this.files.length + ' files' : this.files[0].name)">                     
                                                       <span type="button" class="btn btn-secondary attachment-input">Attachment</span>&hellip;
                                                    <span class='labe' id="upload-file-info"></span>
                                            </div>
                                            </div>
                                            <div class="col-sm-2 add-btn">
                                                <button type="button" class="btn btn-success comment-save">Add</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                            <!-- Display posted comments  -->
                            
                            <!-- for current comment  -->
                            <div class="current-comment"></div>
                            
                        @if(count($details['comments']) > 0)
                            @foreach($details['comments'] as $k => $comment)
                            <div class="row attachment-row$comment['id'] comment-list">
                                <div class="row">
                                    <div class="form-line col-sm-6 commented_user{{$comment['id']}}">
                                    <a href='javascript:;' ><h4 style="display:inline-block">{{ucwords($comment->user->username)}} {{ucwords($comment->user->lastname)}}</h4></a> <span class="comment-time">{{date('d M', strtotime($comment->created_at))}} {{date('h:i A', strtotime($comment->created_at))}}</span>
                                    @if($comment->user_id == session('user_id')) 
                                    <!-- - <a href="javascript:;" onclick="openEditCommentForm('{{$comment->id}}{{$k}}')"><span>Edit</span></a> -->
                                    @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-line col-sm-12" style="">
                                        @if($comment['file']['file']) 
                                        <div class="row" style="padding-left: 58px">
                                        <a href='javascript:;' onclick='popupImg("{{$k}}{{$comment['id']}}")'><img id='img-src{{$k}}{{$comment['id']}}' style="width:16%; max-height:100px;" src="{{asset($comment['file']['file'])}}"></a>
                                           
                                        </div>
                                        @endif
                                        <div class="row" style="padding-left: 58px">
                                            <p style="color:black;border-bottom: 1px solid gainsboro;" class="comment-text-section{{$comment->id}}{{$k}} comment-text">
                                                {{$comment->comment}}
                                            </p>

                                             <!-- Edit Comment Form  -->
                                            <!-- <div class="edit-comment-form{{$comment->id}}{{$k}}" style="display: none;">
                                                <form name="edited_comment_form{{$comment->id}}{{$k}}" class="edited_comment_form{{$comment->id}}{{$k}}" method="post">
                                                {{csrf_field()}}
                                                <div class="form-group">
                                                    <input type="hidden" name="duty_id" value="{{$details['id']}}">
                                                    <input type="hidden" name="comment_id" value="{{$comment->id}}">
                                                    <textarea name="edit_comment" class="form-control textarea-box" rows="5" id="textarea-box">{{$comment->comment ? $comment->comment : '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary update-change-btn{{$comment->id}}{{$k}}" onclick="updateComment('{{$comment->id}}{{$k}}')">Save</button> 
                                                    <button type="button" class="btn btn-secondary close-form" onclick="closeEditCommentForm('{{$comment->id}}{{$k}}')">X</button>
                                                </div>  
                                            </form>
                                        </div> -->

                                        </div>

                                   

                                    </div>
                                </div>

                                <!-- Reply section  -->
                                <div class="current-comment-reply{{$comment['id']}}" id="current-comment-reply{{$comment['id']}}"></div>
                                @if(count($comment['replies']) > 0)
                                @foreach($comment['replies'] as $k => $reply)
                                 <div class="row attachment-row{{$reply->id}} comment-reply-list">
                                    <div class="row">
                                        <div class="form-line col-sm-6 commented_user{{$reply->id}}">
                                        <a href='javascript:;' ><h5 style="display:inline-block">{{ucwords($reply->user->username)}} {{ucwords($reply->user->lastname)}}</h5></a> <span class="comment-time">{{date('d M', strtotime($reply->created_at))}} {{date('h:i A', strtotime($reply->created_at))}}</span>
                                        @if($reply->user_id == session('user_id')) 
                                        <!-- - <a href="javascript:;" onclick="openEditCommentReplyForm('{{$reply->id}}')"><span>Edit</span></a>  -->
                                        @endif
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
                                                <p style="color:black;border-bottom: 1px solid gainsboro;" class="comment-text-section{{$reply->id}} reply-text-section{{$reply->id}} comment-text">
                                                    {{$reply->reply_comment}}
                                                </p>
                                                 <!-- Edit Comment Form  -->
                                                <!-- <div class="edit-reply-form{{$reply->id}}" style="display: none;">
                                                    <form name="edited_reply_form{{$reply->id}}" class="edited_reply_form{{$reply->id}}" method="post">
                                                    {{csrf_field()}}
                                                    <div class="form-group">
                                                        <input type="hidden" name="reply_id" value="{{$reply->id}}">
                                                        <textarea name="edit_reply" class="form-control textarea-box" rows="5" id="textarea-box">{{$reply->reply_comment ? $reply->reply_comment : '' }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-primary update-reply-btn{{$reply->id}}" onclick="updateReply('{{$reply->id}}')">Save</button> 
                                                        <button type="button" class="btn btn-secondary close-form" onclick="closeEditCommentReplyForm('{{$reply->id}}')">X</button>
                                                    </div>  
                                                </form>
                                            </div> -->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Display Reply to reply section  -->
                                <div class="current-comment-reply_to_reply{{$reply['id']}} reply-to-reply-list-ap" id="current-comment-reply_to_reply{{$reply['id']}}"></div>
                                        @if(count($reply['childs']) > 0)
                                        @foreach($reply['childs'] as $k => $child_reply)
                                        <div class="row attachment-row{{$child_reply->id}} comment-reply-to-reply-list">
                                            <div class="row">
                                                <div class="form-line col-sm-6">
                                                <a href='javascript:;' ><h5 style="display:inline-block">{{ucwords($child_reply->user->username)}} {{ucwords($child_reply->user->lastname)}}</h5></a> <span class="comment-time">{{date('d M', strtotime($child_reply->created_at))}} {{date('h:i A', strtotime($child_reply->created_at))}}</span>
                                                @if($child_reply->user_id == session('user_id')) 
                                                <!-- - <a href="javascript:;" onclick="openEditCommentReplyForm('{{$child_reply->id}}')"><span>Edit</span></a> -->
                                                @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-line col-sm-12" style="">
                                                    @if(count($child_reply['files']) > 0)
                                                    @foreach($child_reply['files'] as $k => $file)
                                                    @php $image = Storage::url($file['file']) @endphp
                                                    <div class="row" style="padding-left: 58px">
                                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file['id']}}")'><img id='img-src{{$k}}{{$file['id']}}' style="width:16%; max-height:100px;" src="{{asset($image)}}"></a>
                                                    
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                    <div class="row" style="padding-left: 58px">
                                                        <p style="color:black;border-bottom: 1px solid gainsboro;" class="comment-text-section{{$child_reply->id}} reply-text-section{{$child_reply->id}} comment-text">
                                                            {{$child_reply->reply_comment}}
                                                        </p>
                                                        <!-- Edit Comment Form  -->
                                                        <!-- <div class="edit-reply-form{{$child_reply->id}}" style="display: none;">
                                                            <form name="edited_reply_form{{$child_reply->id}}" class="edited_reply_form{{$child_reply->id}}" method="post">
                                                            {{csrf_field()}}
                                                            <div class="form-group">
                                                                <input type="hidden" name="reply_id" value="{{$child_reply->id}}">
                                                                <textarea name="edit_reply" class="form-control textarea-box" rows="5" id="textarea-box">{{$child_reply->reply_comment ? $child_reply->reply_comment : '' }}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-primary update-reply-btn{{$child_reply->id}}" onclick="updateReply('{{$child_reply->id}}')">Save</button> 
                                                                <button type="button" class="btn btn-secondary close-form" onclick="closeEditCommentReplyForm('{{$child_reply->id}}')">X</button>
                                                            </div>  
                                                        </form>
                                                    </div> -->

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                       <!-- Display Reply to reply section end -->                     
                                <!-- Reply to reply form  -->
                                <div class="row reply-row comment-rely-list">
                                    <a href="javascript:;" class="reply-to-reply" onclick="commentReply('{{$reply['id']}}', '{{$k}}')"><span>Reply</span></a>
                                    </div>
                                    <div class="row" id="comment-reply-box{{$reply['id']}}{{$k}}" style="display: none;">
                                        <form name="comment_reply_to_reply_form" class="comment_reply_to_reply_form" method="post" enctype="multipart/form-data">
                                                    {{csrf_field()}}
                                                <div class="row">
                                                <input type="hidden" name="coment_duty_id" id="coment_duty_id{{$reply['id']}}{{$k}}" value="{{$details['id']}}">
                                                <input type="hidden" name="comment_id" id="comment_id{{$reply['id']}}{{$k}}" value="{{$comment['id']}}">
                                                <input type="hidden" name="reply_id" id="reply_id{{$reply['id']}}{{$k}}" value="{{$reply['id']}}">
                                                    <div class="form-line col-sm-12" style="padding-top: 15px;margin-left: 14px; ">
                                                        <!-- <input type="text" class="form-control"> -->
                                                        <textarea name="comment_reply{{$reply['id']}}{{$k}}" rows="2" class="form-control textarea-box comment-reply-box{{$reply['id']}}{{$k}}" id=""></textarea>
                                                        
                                                    </div>
                                                </div>
                                                <div class="row error-alert{{$reply['id']}}{{$k}}" style="text-align: center;display:none;">
                                                    <span class="text-danger">Comment field is required..</span>
                                                </div>
                                                <div class="row" style="padding-top: 15px;">
                                                    
                                                    <div class="col-sm-9">
                                                        <div class="input-group">
                                                                <input type="file" name="comment_reply_file" id="attachment-input" class="form-control comment-reply-file{{$reply['id']}}{{$k}}"
                                                                onchange="$('#upload-file-info{{$reply['id']}}{{$k}}').html(
                                                                        (this.files.length > 1) ? this.files.length + ' files' : this.files[0].name)">                     
                                                            <span type="button" class="btn btn-secondary attachment-input">Attachment</span>&hellip;
                                                            <span class="labe choose_file{{$reply['id']}}{{$k}}" id="upload-file-info{{$reply['id']}}{{$k}}"></span>
                                                    </div>
                                                    </div>
                                                    <div class="col-sm-3 add-btn">
                                                        <button type="button" class="btn btn-success reply-save{{$reply['id']}}{{$k}}" id="reply-save{{$reply['id']}}{{$k}}"  onclick="saveReply('{{$reply['id']}}', '{{$k}}', 1)">Reply</button>
                                                        <button type="button" class="btn btn-secondary close-form" onclick="closecommentReplyForm('{{$reply['id']}}', '{{$k}}')">X</button>

                                                    </div>
                                                </div>
                                        </form>
                                    </div>
                                 <!-- Reply to reply form end -->
                                @endforeach
                            @endif
                            </div>
                            <div class="row reply-row comment-list">
                             <a href="javascript:;" class="reply" onclick="commentReply('{{$comment['id']}}', '{{$k}}')"><span>Reply</span></a>
                            </div>
                            <div class="row" id="comment-reply-box{{$comment['id']}}{{$k}}" style="display: none;">
                                <form name="comment_reply_form" class="comment_reply_form" method="post" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                        <div class="row">
                                         <input type="hidden" name="coment_duty_id" id="coment_duty_id{{$comment['id']}}{{$k}}" value="{{$details['id']}}">
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
                                            
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                        <input type="file" name="comment_reply_file" id="attachment-input" class="form-control comment-reply-file{{$comment['id']}}{{$k}}"
                                                        onchange="$('#upload-file-info{{$comment['id']}}{{$k}}').html(
                                                                (this.files.length > 1) ? this.files.length + ' files' : this.files[0].name)">                     
                                                       <span type="button" class="btn btn-secondary attachment-input">Attachment</span>&hellip;
                                                    <span class="labe choose_file{{$comment['id']}}{{$k}}" id="upload-file-info{{$comment['id']}}{{$k}}"></span>
                                            </div>
                                            </div>
                                            <div class="col-sm-3 add-btn">
                                                <button type="button" class="btn btn-success reply-save{{$comment['id']}}{{$k}}" id="reply-save{{$comment['id']}}{{$k}}"  onclick="saveReply('{{$comment['id']}}', '{{$k}}')">Reply</button>
                                                <button type="button" class="btn btn-secondary close-form" onclick="closecommentReplyForm('{{$comment['id']}}', '{{$k}}')">X</button>

                                            </div>
                                        </div>
                                </form>
                            </div>
                            <!-- <hr> -->
                            @endforeach
                         @endif
                         </div>
                         <div class="col-sm-4" >
                            <div class="status-action">
                                <div class="row right-heading">
                                    <h5>Duty</h5>
                                </div>
                                <div class="row" style="padding: 12px;">
                                        @if($details->duty_list)
                                            <i class="fa fa-arrow-circle-right fa-lg" aria-hidden="true"></i>  <label for="active">{{$details->duty_list->name}}</label>
                                        @endif
                                        @if($details->sub_duty_list)
                                            <i class="fa fa-arrow-circle-right fa-lg" aria-hidden="true"></i> 
                                            <label for="inactive">{{$details->sub_duty_list->name}}</label> 
                                        @endif 
                                </div>
                            </div>

                             <!-- Active inactive box -->
                             @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)))
                            <div class="status-action">
                                <div class="row right-heading">
                                    <h5><i class="fa fa-exchange fa-lg" aria-hidden="true"></i> Active/In-Active</h5>
                                </div>
                                <div class="row" style="">
                                        <input type="radio" id="active" name="budget_type" value="0" onchange="changeActiveStatus('{{$details->id}}','0')" {{($details->is_close == 0) ? 'checked' : ''}}>
                                        <label for="active">Active</label>
                                        <input type="radio" id="inactive" name="budget_type" value="1" onchange="changeActiveStatus('{{$details->id}}','1')" {{($details->is_close == 1) ? 'checked' : ''}}>
                                        <label for="inactive">In-Active</label>  
                                </div>
                            </div>
                            @endif
                             <!-- Date Box -->
                             <div class="row right-heading">
                                <h5><i class="fa fa-clock-o fa-lg" aria-hidden="true"></i> Duration
                            </h5>
                             </div>
                             <div class="row">
                              <div class="col-sm-6" >
                              <h5 class="text-muted"><strong>Start Date</strong></h5>
                              </div>
                              <div class="col-sm-6" >
                                <h5>{{date('d M, Y', strtotime($details->start_date))}}</h5>
                              </div>
                             </div>
                             <div class="row">
                                <div class="col-sm-6" >
                                    <h5 class="text-muted"><strong>Due Date</strong></h5>
                                </div>
                                <div class="col-sm-6" >
                                    <h5>{{date('d M, Y', strtotime($details->end_date))}}</h5>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-sm-6" >
                                    <h5 class="text-muted"><strong>Event Date</strong></h5>
                                </div>
                                <div class="col-sm-6" >
                                    <h5>{{date('d M, Y', strtotime($details->event_date))}}</h5>
                                </div>
                             </div>
                             <div class="row right-heading">
                                <h5><i class="fa fa-arrow-circle-right fa-lg" aria-hidden="true"></i> Action</h5>
                             </div>
                             @php 
                                $m_access = array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true));
                                $d_access = array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true));
                                $designer_access = array_key_exists('employee-performance/designer', json_decode(session('access'),true));
                             @endphp
                             <div class="row" style="padding-top: 12px;">
                             <form name="move_duty_form" id="move_duty_form" action="post">
                                 {{csrf_field()}}
                                <div class="col-sm-8" >
                                    <input type="hidden" name="duty_id" value="{{$details->id}}">
                                    <input type="hidden" name="duty_list_id" value="{{$details->duty_list_id}}">
                                    <input type="hidden" name="detail_move" value="1">
                                    <input type="hidden" name="action_by" value="{{$action}}">
                                    
                                    @if($details->is_close == 0 || session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)) 
                                    || array_key_exists('employee-performance/designer', json_decode(session('access'),true)) || $m_access == 1 || $d_access == 1 || $designer_access == 1)
                                        @php $disable = ''; @endphp
                                    @else
                                        @php $disable = 'disabled'; @endphp
                                    @endif
                                    <select name="status" {{$disable}} class="form-control move-select" onchange="enableMove(this.value,{{$details->duty_list_id}})">
                                        <option value="">Select Stage</option>
                                        <option value="0" <?php  if($details->progress == 0) { echo 'selected';} ?>>To Do</option>
                                        <option value="1" <?php  if($details->progress == 1) { echo 'selected';} ?>>Doing</option>
                                        <option value="2" <?php  if($details->progress == 2) { echo 'selected';}elseif($details->progress == 0) { echo 'disabled';} ?>>Testing</option>
                                        @if(session('role') == 'ADMIN' || array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) || array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)))
                                        <option value="5" <?php  if($details->progress == 5) { echo 'selected';}elseif($details->progress == 0 || $details->progress == 1) { echo 'disabled';} ?>>Completed</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-4" >
                                    {{-- @if(session('role') == 'ADMIN' || $details->end_date >= date('Y-m-d') || $details->progress == 2 || $details->progress == 5) --}}
                                     <button type="submit" class="btn btn-info btn-move" disabled>Move</button>
                                     {{-- @else 
                                     <p style="padding-top: 6px; color:red;">Expired</p>
                                    @endif --}}
                                            
                                </div>
                            </form>
                             </div>

                             <div class="row move-history" style="padding-top: 12px;">
                             @if(count($details->statusHistories) > 0)
                               <ul>
                             @foreach($details->statusHistories as $history)
                                   <li>
                                     <p class="status-history">
                                         {{-- Moved By  --}}
                                         <span class="user-history">{{$history->user->firstname}} {{$history->user->lastname}}</span> Moved to 
                                     @if($history->status == 0)
                                            <span class="user-history">To Do</span> 
                                        @elseif($history->status == 1)
                                            <span class="user-history">Doing</span>  
                                        @elseif($history->status == 2)
                                            <span class="user-history">Testing</span>
                                        @else
                                            <span class="user-history">Completed</span>
                                        @endif
                                        <span style="font-size: 11px;
                                        color: darkgrey;"> - {{date('d M', strtotime($history->created_at))}}</span>
                                    </p>
                                   </li>
                             @endforeach
                               </ul>
                                

                            @endif
                            </div>

                            </div>
                      </div>

                
                    

            </div>
           
              <!-- Image Preview Crousel Modal -->

           <div class="modal fade skuDetailsmodal" id="skuDetailsmodal" tabindex="-1" role="dialog" aria-labelledby="DetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="width: 60%">
              <div class="modal-content" style="top:180px;">
                <div class="modal-header text-center">
                  <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;"> <span id="changed-group" style="color: green;"></span></h5>
                   
                  <button type="button" class="close close-sku-second-modal-btn"  aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
                  <span id="top-title">
                    <p>Please enter all the listed products sku by comma separate like (S-05BL,S-05GR,....)</p>  
                  </span>
                </div>
                <div class="modal-body" >
                  <div class="detail-div">
                      <form name="check_sku_form" id="check_sku_form" action="post">
                          {{csrf_field()}}
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                 <input type="text" name="sku" class="form-control" placeholder="S-05BL,S-05GR,....." style="border: 1px solid rgb(172, 165, 165)">
                                 <span class="alert-error alert-error-sku"></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-float text-right">
                                 <button type="submit" class="btn btn-info check-sku-btn">Submit</button>
                            </div>
                        </div>
                      </form>
                  </div>
                  
                
              </div>
              <div class="modal-footer" style="padding-top: 88px;text-align: left;">
                <div class="alert alert-warning" style="display: none;background-color: #ff5500 !important;">
                   
                </div>
            </div>
            </div>
          </div>
          </div>

          <div class="modal fade pendingChatsModal" id="pendingChatsModal" tabindex="-1" role="dialog" aria-labelledby="pendingChatsModal" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content" style="border: 5px solid grey;top:180px;text-align:center">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;"> <span id="changed-group" style="color: green;"></span></h5>
                     
                    <button type="button" class="close close-pending-chat-second-modal-btn"  aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <p class="stop-p text-center" id="stopb-text">First update today chats of all paid ads then you can perfom next action</p> <br>
                  {{-- <span>In-active duties: </span> <strong><span id="inactive"></span></strong><br>
                  <span>In Testing duties: </span> <strong><span id="testing"></span></strong><br> --}}
                  <a href="{{url('/employee-performance/marketing/save-add-chat')}}" onclick="closeModal()" target="_blank">click here and add chat</a>
                  {{-- <div class="text-center">
                  <i class="fa fa-cutlery" style="font-size:80px;color:green"></i>
                  </div> --}}
                </div>
              </div>
            </div>
          </div>
        </div>

            <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
            
            <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
            
<script>
// $('.btn-toggle').click(function() {
//     $(this).find('.btn').toggleClass('active');  
    
//     if ($(this).find('.btn-success').size()>0) {
//     	$(this).find('.btn').toggleClass('btn-success');
//     }
//     if ($(this).find('.btn-info').size()>0) {
//     	$(this).find('.btn').toggleClass('btn-info');
//     }
    
//     $(this).find('.btn').toggleClass('btn-default');
       
// });

// $('form').submit(function(){
// 	alert($(this["options"]).val());
//     return false;
// });
function changeActiveStatus(duty,status) {
    if(duty && status) {
        $.ajax({
        url: "{{url('/change/duty/active/actin')}}/"+duty +"/"+ status,
        type: "GET",
        cache: false,
        error: function(error) {
             console.log(error);
        }
      }).then(function(resp) {
          if(resp.status) {
              if(status == 1) {
                $('#descrip-tex').html(resp.contents);
                $('.lable-status').removeClass('active');
                $('.lable-status').removeClass('btn-success');
                $('#lable-status1').addClass('active');
                $('#lable-status1').addClass('btn-danger');
              }else {
                $('#descrip-tex').html(resp.contents);
                $('.lable-status').removeClass('active');
                $('.lable-status').removeClass('btn-danger');
                $('#lable-status0').addClass('active');
                $('#lable-status0').addClass('btn-success');
              }
            
          }
            
      })
    }
}
function addDescription() {
    $('.descrip-text').css('display','none');
    $('.descrip-form').css('display','block');
    const textarea = document.getElementById("textarea-box");
    if(textarea.value !== ""){
        textarea.onfocus = function () {
		        textarea.setSelectionRange(0, textarea.value.length);
                textarea.onfocus = undefined;
		    } 
		    textarea.focus(); 
    } 
}

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
function closeDescriptionForm() {
    $('.descrip-text').css('display','block');
    $('.descrip-form').css('display','none');
}
function closeCommentForm(section) {
    $('.comment-text-section'+section).css('display', 'block');
    $('.comment-form'+section).css('display', 'none');
}
function popupImg(index, duty = null) {
     $('.imagepreview').attr('src', $('#img-src'+index).attr('src'));
			$('#imagePreviewmodal').modal('show');  
     
 }

 function popupImgCrousel(file,duty = null) {
    //  $('.imagepreview').attr('src', $('#img-src'+index).attr('src'));
	$('#imagePreviewCrouselmodal').modal('show'); 
     if(duty) {
         $.ajax({
             url: "{{url('/get/duty/files')}}/"+duty +"/"+file,
             type: "GET",
             cache: false,
             beforeSend: function() {
                $('.modal-crousel-loader').css('display', 'block');
             },
             error: function(er) {
                console.log(er);
             }
         }).then(function(resp) {
            $('.modal-crousel-loader').css('display', 'none');
             $('.attachment-crousel-data').html(resp);
         })
     }
     
 }
// function attachmentcommentModal() {
//     console.log("Okkkkkkkkkkk");
//     $('#attachmentCommentmodal').modal('show');   
// }
$('.save-change-btn').on('click', function() {
    // event.preventDefault();
    $.ajax({
        url: "{{url('/save/duty/description/content')}}/",
        type: "POST",
        data: $('.description_form').serialize(),
        cache: false,
        beforeSend: function() {
            $('.save-change-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.save-change-btn').prop('disabled', true);
        },
        complete: function() {
            $('.save-change-btn').html('Save');
            $('.save-change-btn').prop('disabled', false);
        },
        error: function(error) {
             console.log(error);
        }
    }).then(function(resp) {
        $('#descrip-tex').html(resp.contents);
        $('.descrip-text').css('display','block');
        $('.descrip-form').css('display','none');
    })
});

function makeCover(id, duty, src) {
    // console.log("id ="+String(id)+ "duty="+String(duty)+ "src="+ String(src));
    if(id) {
        $.ajax({
        url: "{{url('/make/cover/photo')}}/"+id +"/"+ duty,
        type: "GET",
        cache: false,
        success: function(resp) {
            if(resp.status) {
                $('.cover-icon').css('color', 'gray');
                $('.cover-icon'+id).css('color', 'green');
                $('.cover-img-text').html('Make Cover')
                $('.cover-img-text'+id).html('Cover Image')
                var c_img = $('.cover-img img');
                c_img.css('display', 'inline-block');
                c_img.css('height', '160px');
                c_img.css('min-height', '160px');
                $('.cover-img img').attr('src', src);
            }
        }
     });
    }
}

function removeAttachment(file, cover) {
     swal({
            title: "Delete attachment?",
            text: "Deleting an attachment is permanent. There is no undo.",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        },function (e) {
        if (e === true) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'GET',
                url: "{{url('/remove/duty/attachment')}}/" + file,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {
                    if (results.status === true) {
                      $('.attachment-row'+file).remove();
                      $('.attachment-action-row'+file).remove();
                      if(cover == 1) {
                        var c_img = $('.cover-img img');
                        c_img.css('display', 'none');
                        c_img.css('height', '0px');
                        c_img.css('min-height', '0px');
                      }
                        swal("Done!", results.message, "success");
                    } else {
                        swal("Sorry!", results.message, "error");
                    }
                }
            });

        } else {
            e.dismiss;
        }

        }, function (dismiss) {
        return false;
        });
}

$('#attachment-input').on('change', function() {
    $('#attachment-loader').css('display','inline-block');
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    var fileSizebyte = $('#attachment-input')[0].files[0].size;
    var mb = (Math.round(+fileSizebyte/1024)/1000).toFixed(2);
    // console.log((Math.round(+fileSizebyte/1024)/1000).toFixed(2));
    if(mb <= 25) {
        console.log("less");
        formData.append('attachment', $('#attachment-input')[0].files[0]);
        formData.append('duty_id', $('#duty_idd').val());
        formData.append('_token', CSRF_TOKEN);
        $.ajax({
            type: 'POST',
                url: "{{url('/add/attachment/to/duty')}}/",
                data: formData,
                processData: false,
                contentType: false,
                success: function (results) {
                    if (results.status === true) {
                        var col = 'col-sm-3';
                        var comment_col = 'col-sm-9';
                        var file = results.attachment_data.attachment.file.split('/');
                        let date = new Date(results.attachment_data.attachment.created_at); // 2020-06-21
                        let month = date.toLocaleString('en-us', { month: 'short' }); 
                        var hour    = date.getHours();
                        var minute  = date.getMinutes();
                        var second  = date.getSeconds(); 
                        var time = hour+":"+minute+":"+second;
                        var file_html = checkAttachmentType(results.attachment_data,results.extensions, results.image_extensions);
                        if(results.extensions.includes(results.attachment_data.attachment.extension)) {
                            col = 'col-sm-5';
                            comment_col = 'col-sm-7';
                        }
                    var html = '<div class="row attachment-row'+results.attachment_data.attachment.id+'" style="padding-left: 17px;"><div class="form-line '+col+'">'+file_html+'</div>'+'\n'+
                                    '<div class="form-line '+comment_col+'" style="padding-top: 15px;">'+'\n'+
                                    '<div class="row">'+'\n'+
                                    '<p>'+'\n'+
                                    '<a href="javascript:;" onclick="popupImg('+results.attachment_data.attachment.id+')"></a>'+'\n'+ 
                                    '</p>'+'\n'+
                                    '</div>'+'\n'+
                                    '</div></div>'+'\n'+
                                    '<div class="row attachment-action-row'+results.attachment_data.attachment.id+'" style="padding-left: 17px;">'+'\n'+
                                        '<div class="col-sm-3" >'+'\n'+
                                        //    '<a href="javascript:;" onclick="makeCover('+results.attachment_data.attachment.id+','+results.attachment_data.attachment.custom_duty_id+','+results.attachment_data.base+results.attachment_data.attachment.file+')" class="cover-text "><i class="fa fa-desktop cover-icon cover-icon'+results.attachment_data.attachment.id+'" aria-hidden="true"></i><span class="cover-img-text'+results.attachment_data.attachment.id+' cover-img-text"> Make Cover</span></a>'+'\n'+
                                        '</div>'+'\n'+
                                        '<div class="col-sm-8" >'+'\n'+
                                            '<span>'+month+' '+hour+":"+minute+":"+second+'</span> - <a href="javascript:;" onclick="removeAttachment('+results.attachment_data.attachment.id+', '+results.attachment_data.attachment.cover+')"><span>Delete</span></a>'+'\n'+          
                                            '- <a href="javascript:;" onclick="attachmentcomments('+results.attachment_data.attachment.id+')" data-toggle="modal" data-target="#attachmentCommentmodal"><span>Comments(0)</span></a>'+'\n'+
                                        '</div>'+'\n'+
                                '</div>';
                        // var current_comment = currentComment(results.attachment_data.comment, results.attachment_data.user, results.attachment_data.base+results.attachment_data.attachment.file, month, time);
                        $('.append-attachment-row').prepend(html);
                        $('#attachment-loader').css('display','none');
                        // $('.current-comment').prepend(current_comment);
                    } else {
                            swal("Sorry!", results.message, "error");
                        $('#attachment-loader').css('display','none');
                    }
                }
            });  
    }else {
        $('#attachment-loader').css('display','none');
        $('#error-worning').addClass('alert alert-danger');
        $('#error-worning').html('Your file is greater than 25 mb');
        setTimeout(() => {
            $('#error-worning').removeClass('alert alert-danger');
            $('#error-worning').html('');
        }, 2500);
        return false;
    }
    
    
});

function checkAttachmentType(data, extensions, image_extensions) {
    // console.log(data);
    // console.log(extensions);
    var html = '';
    if(extensions.includes(data.attachment.extension)) {
        if(image_extensions.includes(data.attachment.extension)) {
            html = '<a href="javascript:;" onclick="popupImgCrousel('+data.attachment.id+', '+data.attachment.custom_duty_id+')"><img id="img-src'+data.attachment.id+'" style="width:129px; max-height:100px; padding: 10px;position: relative;top: 0;left: 0;max-height: 400px;border-radius: 12%;" src="'+data.base+data.attachment.file+'" id="image-tag0"></a>'

        }else {
            html = '<a href="'+data.base+data.attachment.file+'" style="padding-left: 14px; color:red;vertical-align: -webkit-baseline-middle;" download><i class="fa fa-download"></i> Dwonload</a>';
        }
    }else {
        html = '<video width="230" controls class="video-tag">'+'\n'+
                                '<source src="'+data.base+data.attachment.file+'" type="video/'+data.attachment.extension+'">Your browser does not support HTML video.'+'\n'+
                                '<source src="mov_bbb.ogg" type="video/ogg">'+'\n'+
                                '</video>';
    }

    return html;
}

function currentComment(comment, user, file, date, time) {
    var html = '<div class="row attachment-row comment-list">'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-6">'+'\n'+
                                    '<a href="javascript:;" ><h4 style="display:inline-block">'+user+'</h4></a> <span class="comment-time">'+date+' '+time+'</span>'+'\n'+
                                    '</div>'+'\n'+
                                '</div>'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-12" style="">'+'\n'+
                                        '<div class="row" style="padding-left: 58px">'+'\n'+
                                            '<img src="'+file+'" style="width:100%">'+'\n'+
                                       '</div>'+'\n'+
                                    '</div>'+'\n'+
                                '</div>'+'\n'+
                            '</div>';
        return html;
}
 function generalComment(data, per_class, action = null) {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
     var div = '';
     var form = '';
     var link = '';
     var h_tag = '';
     let date = new Date(data.comment.created_at); // 2020-06-21
                    let month = date.toLocaleString('en-us', { month: 'short' }); 
                    var hour    = date.getHours();
                    var minute  = date.getMinutes();
                    var second  = date.getSeconds(); 
                    var time = hour+":"+minute+":"+second;
     if(data.file) {
         div =  '<div class="row" style="padding-left: 58px">'+'\n'+
                                        '<a href="javascript:;" onclick="popupImg('+data.comment.id+data.comment.user_id+')"><img id="img-src'+data.comment.id+data.comment.user_id+'" style="width:16%; max-height:100px;" src="'+data.base+data.file.file+'"></a>'+'\n'+
                                        '</div>';
     }
     if(!action) {
         p_tag = 'comment-text-section';
         h_tag = '4';
         link = '- <a href="javascript:;" onclick="openEditCommentForm('+data.comment.id+data.comment.user_id+')"><span>Edit</span></a></span>';
         form = '<div class="edit-comment-form'+data.comment.id+data.comment.user_id+'" style="display: none;">'+'\n'+
                                                '<form name="edited_comment_form'+data.comment.id+data.comment.user_id+'" class="edited_comment_form'+data.comment.id+data.comment.user_id+'" method="post">'+'\n'+
                                                '<input type="hidden" name="_token" value="'+CSRF_TOKEN+'">'+'\n'+
                                                '<div class="form-group">'+'\n'+
                                                    '<input type="hidden" name="duty_id" value="'+data.comment.employee_custom_duty_id+'">'+'\n'+
                                                    '<input type="hidden" name="comment_id" value="'+data.comment.id+'">'+'\n'+
                                                    '<textarea name="edit_comment" class="form-control textarea-box" rows="5" id="textarea-box">'+data.comment.comment+'</textarea>'+'\n'+
                                                '</div>'+'\n'+
                                                '<div class="form-group">'+'\n'+
                                                    '<button type="button" class="btn btn-primary update-change-btn'+data.comment.id+data.comment.user_id+'" onclick="updateComment('+data.comment.id+data.comment.user_id+')">Save</button>'+'\n'+ 
                                                    '<button type="button" class="btn btn-secondary close-form" onclick="closeEditCommentForm('+data.comment.id+data.comment.user_id+')">X</button>'+'\n'+
                                                '</div>'+'\n'+
                                            '</form>'+'\n'+
                                        '</div>';
     }else {
        p_tag = 'reply-text-section';
         h_tag = '5';
        link = ' - <a href="javascript:;" onclick="openEditCommentReplyForm('+data.comment.id+data.comment.user_id+')"><span>Edit</span></a></span>';
        form = '<div class="edit-reply-form'+data.comment.id+data.comment.user_id+'" style="display: none;">'+'\n'+
                                                '<form name="edited_reply_form'+data.comment.id+data.comment.user_id+'" class="edited_reply_form'+data.comment.id+data.comment.user_id+'" method="post">'+'\n'+
                                                '<input type="hidden" name="_token" value="'+CSRF_TOKEN+'">'+'\n'+
                                                '<div class="form-group">'+'\n'+
                                                    '<input type="hidden" name="reply_id" value="'+data.comment.id+'">'+'\n'+
                                                    '<textarea name="edit_reply" class="form-control textarea-box" rows="5" id="textarea-box">'+data.comment.comment+'</textarea>'+'\n'+
                                                '</div>'+'\n'+
                                                '<div class="form-group">'+'\n'+
                                                    '<button type="button" class="btn btn-primary update-reply-btn'+data.comment.id+data.comment.user_id+'" onclick="updateReply('+data.comment.id+data.comment.user_id+')">Save</button>'+'\n'+ 
                                                    '<button type="button" class="btn btn-secondary close-form" onclick="closeEditCommentReplyForm('+data.comment.id+data.comment.user_id+')">X</button>'+'\n'+
                                                '</div>'+'\n'+
                                            '</form>'+'\n'+
                                        '</div>';
     }
    var html = '<div class="row attachment-row'+data.comment.id+' comment-list '+per_class+'">'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-6">'+'\n'+
                                    // '<a href="javascript:;" ><h'+h_tag+' style="display:inline-block">'+data.user+'</h'+h_tag+'></a> <span class="comment-time">'+month+' '+time+ link+'</div>'+'\n'+
                                    '<a href="javascript:;" ><h'+h_tag+' style="display:inline-block">'+data.user+'</h'+h_tag+'></a> <span class="comment-time">'+month+' '+time+'</div>'+'\n'+
                                '</div>'+'\n'+
                                '<div class="row">'+'\n'+
                                    '<div class="form-line col-sm-12" style="">'+div+'<div class="row" style="padding-left: 58px">'+'\n'+
                                            '<p style="color:black" class="'+p_tag+data.comment.id+data.comment.user_id+' comment-text">'+data.comment.comment+'</p>'+form+'</div>'+'\n'+
                                    '</div>'+'\n'+
                                '</div>'+'\n'+
                            '</div>';
    return html;
 }


$('.comment-save').on('click', function(event) {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var comment = $('#comment-box').val();
    var file = $('.comment-file')[0].files[0];
    if(comment) {
            $('.comment-save').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.comment-save').prop('disabled', true);
        var formData = new FormData();
        formData.append('duty_id', $('#coment_duty_id').val());
        formData.append('comment', comment);
        formData.append('comment_file', file);
        formData.append('_token', CSRF_TOKEN);
        $.ajax({
            url: "{{url('save/duty/comment')}}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $('.comment-save').html('Add');
                $('.comment-save').prop('disabled', false);
                // console.log(resp);return;
                var current_comment = generalComment(resp, 'a');
                $('.current-comment').prepend(current_comment, file);
                $('#comment-box').val('')
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

function saveReply(v,i, action = null) {
    console.log(v);
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var comment = $('.comment-reply-box'+v+i).val();
    var file = $('.comment-reply-file'+v+i)[0].files[0];
    var append = '';
    if(comment) {
            $('.reply-save'+v+i).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.reply-save'+v+i).prop('disabled', true);
        var formData = new FormData();
        if(action) {
            append = '#current-comment-reply_to_reply'+v;
            per_class = '';
            formData.append('reply_id', $('#reply_id'+v+i).val());
        }else {
            append = '#current-comment-reply'+v;
            per_class = 'comment-reply-lis';
        }
        formData.append('comment_id', $('#comment_id'+v+i).val());
        formData.append('duty_id', $('#coment_duty_id'+v+i).val());
        formData.append('comment', comment);
        formData.append('comment_file', file);
        formData.append('_token', CSRF_TOKEN);
        $.ajax({
            url: "{{url('save/comment/reply')}}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $('.reply-save'+v+i).html('Reply');
                $('.reply-save'+v+i).prop('disabled', false);
                var current_comment = generalComment(resp, per_class, 1);
                console.log(append);
                $(append).prepend(current_comment);
                $('.comment-reply-box'+v+i).val('')
                $('.comment-reply-file'+v+i).val(null);
                $('#upload-file-info'+v+i).html('');
                $('.choose_file'+v+i).val('');
            }
        })
    }else {
       $('.error-alert'+v+i).css('display','block');
       setTimeout(() => {
        $('.error-alert'+v+i).css('display','none');
       },3500);
        return;
    }
}

function commentReply(k,index) {
    console.log(k+index);
    $('#comment-reply-box'+k+index).css('display', 'block');
}

function closecommentReplyForm(k,index) {
    $('#comment-reply-box'+k+index).css('display', 'none');
}

function attachmentcomments(file) {
    console.log(file);
     $.ajax({
                url: "{{url('/duty/attachment/comments')}}/" + file,
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
                $('.attachment-details').html(resp);
            });
}

$('#move_duty_form').submit(function(event) {
    event.preventDefault();
    console.log($(this).serialize());
    var status = $('.move-select').val();
    $.ajax({
        url: "{{url('/changes/duty/status')}}",
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        beforeSend: function() {
            $('.btn-move').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.btn-move').prop('disabled', true);
        },
        complete: function() {
            $('.btn-move').html('Move');
            $('.btn-move').prop('disabled', true);
        }
    }).then(function(resp) {
        var title = '';
        if(resp.status == 5) {
            console.log(resp);
            $('#pendingChatsModal').modal('toggle');
            $(document).find('#pendingChatsModal').on('hidden.bs.modal', function () {
                $('body').addClass('modal-open');
            });
            // $("#pendingChatModal").modal("show");
        }else {
            var icon = '<i class="fa fa-eye"></i>';
            if(status == 0) {
             title = 'In list To Do';
            }else if(status == 1) {
                title = 'In list Doing';
            }else if(status == 2) {
                title = 'In list Testing';
            }else {
                title = 'In list Completed';
            }
            $('.progress-title').html(title+' '+icon);
            $('#custom_list').html(resp);
        }
            
        
    });
});

$('#check_sku_form').submit(function(event) {
    event.preventDefault();
    console.log($(this).serialize());
    $.ajax({
        url: "{{url('/check/listed/products/sku')}}",
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        beforeSend: function() {
            $('.check-sku-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.check-sku-btn').prop('disabled', true);
        },
        complete: function() {
            $('.check-sku-btn').html('Submit');
            $('.check-sku-btn').prop('disabled', false);
        },
        error: function(error) {
            $('.check-sku-btn').prop('disabled', false);
            if(error.responseText.indexOf('sku') !== -1) {
            $('.check-sku-btn').prop('disabled', false);
            $('.alert-error-sku').text('The SKU field is required.');
            setTimeout(() => {
            $('.alert-error').text('');
            },5000);
          }
        },
    }).then(function(resp) {
        $('.check-sku-btn').prop('disabled', false);
        var title = '';
        if(resp.status) {
            $('.btn-move').attr('disabled', false);
            $('#skuDetailsmodal').modal('toggle');
            $(document).find('#skuDetailsmodal').on('hidden.bs.modal', function () {
                $('body').addClass('modal-open');
            });
        }else {
            var w_megs = '';
            if(!resp.ba_flag || !resp.df_flag) {
                ba = '';
                df = '';
                // $('.alert-warning').text('Somethings went wrong please try again.');
                if(!resp.ba_flag && !resp.df_flag) {
                var mesge = 'In Business Arcade <strong style="color:#151615;">'+resp.bamissing_products.join()+'</strong> and in Dressfair <strong style="color:#151615;">'+resp.dfmissing_products.join()+'</strong>';
                }
                if(!resp.ba_flag && resp.df_flag) {
                var mesge = 'In Business Arcade <strong style="color:#151615;">'+resp.bamissing_products.join()+'</strong>';
                }
                if(!resp.df_flag && resp.ba_flag) {
                var mesge = 'In Dressfair <strong style="color:#151615;">'+resp.dfmissing_products.join()+'</strong>';
                }
                var is_are = (!resp.ba_flag && !resp.df_flag || (resp.bamissing_products.length > 1 || resp.dfmissing_products.length > 1)) ? 'are' : 'is';
            
                w_megs = mesge +' '+is_are+' not found, please make sure these products are listed or the sku '+is_are+' connected.';
            }else {
                var r_url = window.location.origin+'/oms/employee-performance/marketer/product/listing';
                mesge = 'Please first list the products <strong style="color:#151615;">'+resp.oms_product_list.join()+'</strong> then complete duty, click on the link <br> <a href="'+r_url+'" style="background: white;">Click Here</a>';
                w_megs = mesge;
            }
            
            $('.alert-warning').css('display', 'block');
            console.log(w_megs);
            $('.alert-warning').html(w_megs);
            if(!resp.ba_flag || !resp.df_flag) {
                setTimeout(() => {
                $('.alert-warning').css('display', 'none');
                $('.alert-warning').text('');
                }, 10000)
            }
        }
        
    });
});
$('.close-sku-second-modal-btn').on('click', function() {
    $('#skuDetailsmodal').modal('toggle');
    $(document).find('#skuDetailsmodal').on('hidden.bs.modal', function () {
        console.log('hiding child modal');
        $('body').addClass('modal-open');
    });
});

$('.close-pending-chat-second-modal-btn').on('click', function() {
    $('#pendingChatsModal').modal('toggle');
    $(document).find('#pendingChatsModal').on('hidden.bs.modal', function () {
        console.log('hiding child modal');
        $('body').addClass('modal-open');
    });
});

function updateComment(event) {
    console.log($('.edited_comment_form'+event).serialize());
    $.ajax({
        url: "{{url('/update/comment')}}",
        type: "POST",
        data: $('.edited_comment_form'+event).serialize(),
        cache: false,
        beforeSend: function() {
            $('.update-change-btn'+event).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.update-change-btn'+event).prop('disabled', true);
        },
        complete: function() {
            $('.update-change-btn'+event).html('Save');
            $('.update-change-btn'+event).prop('disabled', true);
        }
    }).then(function(resp) {
        if(resp.status) {
            $('.comment-text-section'+event).html(resp.comment);
            $('.comment-text-section'+event).css('display', 'block');
            $('.edit-comment-form'+event).css('display', 'none');
        }
    })
}

function updateReply(event) {
    console.log($('.edited_reply_form'+event).serialize());
    // return;
    $.ajax({
        url: "{{url('/update/comment/reply')}}",
        type: "POST",
        data: $('.edited_reply_form'+event).serialize(),
        cache: false,
        beforeSend: function() {
            $('.update-change-btn'+event).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $('.update-change-btn'+event).prop('disabled', true);
        },
        complete: function() {
            $('.update-change-btn'+event).html('Save');
            $('.update-change-btn'+event).prop('disabled', true);
        }
    }).then(function(resp) {
        if(resp.status) {
            $('.reply-text-section'+event).html(resp.reply);
            $('.reply-text-section'+event).css('display', 'block');
            $('.edit-reply-form'+event).css('display', 'none');
        }
    })
}

function openEditCommentForm(v) {
    $('.comment-text-section'+v).css('display', 'none');
    $('.edit-comment-form'+v).css('display', 'block');
    $('.update-change-btn'+v).prop('disabled', false);
}
function closeEditCommentForm(v) {
    $('.comment-text-section'+v).css('display', 'block');
    $('.edit-comment-form'+v).css('display', 'none');
}

function openEditCommentReplyForm(v) {
    $('.reply-text-section'+v).css('display', 'none');
    $('.edit-reply-form'+v).css('display', 'block');
    $('.update-reply-btn'+v).prop('disabled', false);
}
function closeEditCommentReplyForm(v) {
    $('.reply-text-section'+v).css('display', 'block');
    $('.edit-reply-form'+v).css('display', 'none');
}
function enableMove(option, duty) {
    if(duty == 49 && option == 5 ) {
        $('.btn-move').attr('disabled', true);
        $('#skuDetailsmodal').modal({backdrop: "static"});
    }else {
        if(option) {
            $('.btn-move').attr('disabled', false);
        }else {
            $('.btn-move').attr('disabled', true);
        }
    }
    
}
</script>