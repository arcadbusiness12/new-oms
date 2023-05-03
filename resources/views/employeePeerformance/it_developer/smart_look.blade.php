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
    </style>
<section class="content">
    <div class="container-fluid">
            {{ csrf_field() }}
            <div class="block-header">
                <div class="pull-left">
                    <h2>Custom Duties</h2>
                </div>
                <div class="pull-right">
                    <a href="<?php echo route('employee-performance.itdeveloper.smart.look.form', [$user,$action]) ?>"><button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button></a>
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
            
         <?php if(count($smart_looks) > 0) { ?>
           <?php foreach ($smart_looks as $look) { ?>
            <div class="card order_list">
                <div class="row top_row">
                    <div class="col-xs-6">
                        <div class="title"><b>
                           <h4> <?php echo $look->title ?></h4>
                        </b>
                    </div>

                        <div class="desc"><?php echo $look->description ?></div>

                        {{-- <div class="desc"><strong>Link: </strong> <br></div> --}}
                        <div class="focused">
                            <input type="text" name="supplier_link" value="{{ $look->link }}}" class="form-control copy_to_clipboard" placeholder="Supplier Link" readonly="">
                        </div>
                     </div>
                    <div class="col-xs-2 text-center">
                            {{-- <b>2021-11-29</b> <br><br>
                            <b>2021-11-30</b> --}}
                            <div class="badge"><?php echo $look->created_at ?></div> <br><br>
                            <div class="badge"><?php echo $look->event_date ?></div>
                       </div>
                    <div class="col-xs-2 text-center">
                            <div class="badge">
                                <?php
                                $progress = '';
                                 if($look->progress && $look->progress == 0) {
                                        $progress = 'In To Do';
                                    }elseif ($look->progress && $look->progress == 1) {
                                        $progress = 'In Doing';
                                    }elseif ($look->progress && $look->progress == 2) {
                                        $progress = 'In Testing';
                                    }elseif ($look->progress && $look->progress == 5) {
                                        $progress = 'In Complete';
                                    }else {
                                        $progress = 'Not connected with duty';
                                    }

                                    echo $progress;
                                    ?>

                            </div>
                    </div>

                    <div class="col-xs-2 text-center">
                        <div class="label label-{{($look->is_emergency == 1) ? 'warning' : 'success'}}">
                            <td><?php if($look->is_emergency == 1) {echo 'Urgent';} else {
                                echo 'Normal';
                              } ?>    
                        </div>
                            <div class="assiged">Assigned To <?php echo $look->user['username'] ?> </div>
                    </div>
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
        {{$smart_looks->links()}}
<!-- <?php echo $smart_looks->appends(@$old_input)->render(); ?> -->
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
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush