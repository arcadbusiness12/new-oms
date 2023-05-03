@extends('layout.theme')
@section('title', 'Home')
@section('content')
<style>
    .order_progress_bar_new .circle-box .title {
    min-height: 20px !important;
    font-size: 12px;
    text-align: center !important;
    }
    .order_progress_bar_new .circle-box{
    text-align: center;
    }
    .title {
        padding-left: 2px;
    }
    .desc {
        margin-top: 16px;
        padding-left: 15px;
        padding-top: 5px;
        border: 1px solid rgb(221, 221, 221);
    }
    .assiged {
        padding-top: 12px;
    }
    .focused {
        margin-top: 12px;
    }
    .order_list .top_row {
        border-bottom: none !important;
    } 
    .approve-st {
        padding-right: 56px !important;
        font-size: 18px;
    }
    </style>
<section class="content">
    <div class="container-fluid">
            {{ csrf_field() }}
            <div class="block-header">
                <div class="pull-left">
                    <h2>R&D</h2>
                </div>
                <div class="pull-right">
                    <a href="<?php echo route('employee-performance.itdeveloper.rAndD.form', [$user,$action]) ?>"><button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                  <div class="col-xs-12">
                      <div class="card">
                          <div class="panel panel-default">
                              <div class="panel-body">
                                  <form name="filter_reports" id="filter_reports" method="get" action="{{ ($action == 'web') ? route('developerweb.smart.look', [$user, $action]) : route('employee-performance.app.developer.smart.look', [$user, $action]) }}">
                                      {{csrf_field()}}
                                      <div class="row">
                                          <div class="col-sm-4">
                                              <div class="form-group form-float">
                                                  <div class="form-line">
                                                      <label class="form-label" for="status">Title</label><br>
                                                     <input type="text" name="title" class="form-control" value="{{@$old_input['title']}}">
                                                  </div>
                                              </div>
                                          </div> 
                                          <div class="col-sm-4">
                                            <div class="form-group form-float">
                                              <div class="form-line">
                                                  <label class="form-label" for="user_group_id">Progress</label><br>
                                                  <select name="progress" id="progress" class="form-control show-tick" data-live-search="true">
                                                      <option value="">Select Stage</option>
                                                      <option value="0" <?php  if(isset($old_input['progress']) && $old_input['progress'] == 0) { echo 'selected';} ?>>To Do</option>
                                                      <option value="1" <?php  if(@$old_input['progress'] == 1) { echo 'selected';} ?>>Doing</option>
                                                      <option value="2" <?php  if(@$old_input['progress'] == 2) { echo 'selected';} ?>>Testing</option>
                                                      <option value="5" <?php  if(@$old_input['progress'] == 5) { echo 'selected';} ?>>Completed</option>
                                                  </select>
                                              </div>
                                            </div>
                                          </div>
                                         
                                      </div>

                                      <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            
         <?php if(count($rAndDs) > 0) { ?>
           <?php foreach ($rAndDs as $k => $rd) { ?>
            <div class="card order_list">
                <div class="row top_row">
                    <div class="col-xs-6">
                        <div class="title"><b>
                           <h4> <?php echo $rd->title ?></h4>
                        </b>
                    </div>

                        <div class="desc"><?php echo $rd->description ?></div>

                        {{-- <div class="desc"><strong>Link: </strong> <br></div> --}}
                        <div class="focused">
                            <input type="text" name="supplier_link" value="{{ $rd->link }}}" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly="">
                        </div>
                     </div>
                    <div class="col-xs-2 text-center">
                            {{-- <b>2021-11-29</b> <br><br>
                            <b>2021-11-30</b> --}}
                            <div class="badge"><?php echo $rd->created_at ?></div> <br><br>
                            <div class="badge"><?php echo $rd->event_date ?></div>
                       </div>
                     <div class="col-xs-2 text-center">
                            <div class="badge">
                                <?php
                                $progress = '';
                                 if($rd->progress && $rd->progress == 0) {
                                        $progress = 'In To Do';
                                    }elseif ($rd->progress && $rd->progress == 1) {
                                        $progress = 'In Doing';
                                    }elseif ($rd->progress && $rd->progress == 2) {
                                        $progress = 'In Testing';
                                    }elseif ($rd->progress && $rd->progress == 5) {
                                        $progress = 'In Complete';
                                    }else {
                                        $progress = 'Not connected with duty';
                                    }

                                    echo $progress;
                                    ?>

                            </div>
                    </div>

                    <div class="col-xs-2 text-center">
                        <div class="label label-{{($rd->is_emergency == 1) ? 'warning' : 'success'}} col-2">
                            <td><?php if($rd->is_emergency == 1) {echo 'Urgent';} else {
                                echo 'Normal';
                              } ?>    
                        </div>
                        <?php if( $rd->assignedUser ) {?>
                            <div class="assiged col-xs-8">Assigned To
                                 <?php echo $rd->assignedUser['username'] ?> 
                                </div>
                            <?php } ?>
                            
                    </div>
                </div>
                  
                <div class="row text-right">
                    <?php if($rd->need_to_approve == 1 && session('role') == 'ADMIN') {?>
                        <div class="assiged col-xs-12">
                            <button class="btn btn-danger" onclick="acceptRejectRequest('{{$rd->id}}',2, {{$user}}, '{{$action}}', '{{$k}}')" tabindex="1" style="float:right;background-color: rgb(147, 5, 5);, rgba(0, 0, 0, 0.05) 0px 0px 0px 1px inset;margin: -5px 5px 0px;font-size: 12px;">
                                Reject
                             </button>
                              <button class="btn btn-success" onclick="acceptRejectRequest('{{$rd->id}}',1, {{$user}}, '{{$action}}', '{{$k}}')" tabindex="1" style="float:right; background-color: #097d07; , rgba(0, 0, 0, 0.05) 0px 0px 0px 1px inset;margin: -5px 5px 0px;font-size: 12px;">
                              Accept
                              </button>
                        </div>
                        <?php }elseif ($rd->need_to_approve == 1 && session('role') != 'ADMIN') {?>
                            <div class="assiged col-xs-12 approve-st">
                                 <div class="label label-danger col-2 ">Pending</div>
                            </div>
                       <?php }elseif ($rd->need_to_approve == 2 && $rd->need_to_approve == 1) {?>
                        <div class="assiged col-xs-12 approve-st">
                             <div class="label label-danger col-2 ">Approved</div>
                        </div>
                        <?php }elseif ($rd->need_to_approve == 2 && $rd->need_to_approve == 2) {?>
                            <div class="assiged col-xs-12 approve-st">
                                <div class="label label-danger col-2 ">Rejected</div>
                            </div>
                            <?php }else {?>
                           {{$rd->need_to_approve}}
                       <?php } ?>
                </div>
                         
            </div>
      <?php  }
    }else {?>
        <div class="card order_list">
                <div class="row top_row">
                    <div class="col-xs-12">
                        <div class="title text-center">
                           <h4> No Rocord Found</h4>
                    </div>
                     </div>
                </div>
                  
                         
            </div>
   <?php }
    ?>
    <div id="group-paginate">
        {{$rAndDs->links()}}
<!-- <?php echo $rAndDs->appends(@$old_input)->render(); ?> -->
    </div>
    </div>
</section>
@endsection
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{URL::asset('public/assets/css/purchase.css?_=' . time()) }}">
<script type="text/javascript">
$('.delete-user').click(function(){
    if(!confirm('Are you sure to delete duty?')) return false;
});

function acceptRejectRequest(id, status, user, action, index) {
    console.log(id);return;
  if(notification && time) {
    $.ajax({
        url: "{{url('/admin/request/response')}}/"+ id +"/"+ status +"/"+ user +"/"+ action,
          type: "GET",
          cache: false,
          success: function(resp) {
            if(from_action == 1) {
              $('.main-row'+index).remove();
            }else {
              if(action == 1) {
              $('.lable-status'+index).addClass('active btn-success');
              $('.lable-status'+index).text('Approved');
            }else {
              $('.lable-status'+index).addClass('active btn-danger');
              $('.lable-status'+index).text('Rejected');
            }
            $('.request-action-btn'+index).remove();
            }
            
            
            
          }
    })
  }
}
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush