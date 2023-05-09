@extends('layouts.app')

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
    .title h4 {
        font-weight: bold;
        color: black;
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
    .badge {
        font-weight: 500;
        font-size: 13px;
    }
    </style>
<section class="content">
    <div class="container-fluid">
            {{ csrf_field() }}
            <div class="block-header">
                <div class="pull-left">
                    <h2>Smart Look</h2>
                </div>
                <div class="pull-right">
                    <a href=""><button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="panel panel-default p-4">
                            <div class="panel-body">
                                <form name="filter_reports" id="filter_reports" method="get" action="">
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
            
            <div class="col-sm-12">
                <?php if(Session::has('message')) { ?>
                <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo Session::get('message') ?>
                </div>
                <?php } ?>
            </div>
            <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr style="background-color: #3f51b5;color:white; text-align:center;">
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Link</th>
                                    <th>Assigned To</th>
                                    <th>Emergency/Normal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($smart_looks)) { ?>
                                <?php foreach ($smart_looks as $look) { ?>
                                    <tr>
                                        <td><?php echo $look->title ?></td>
                                        <td><?php echo $look->description ?></td>
                                        <td ><?php echo $look->link ?></td>
                                        <td><?php echo @$look->user['username'] ?></td>
                                        <td><?php if($look->is_emergency == 1) {echo 'Urgent';} else {
                                          echo 'Normal';
                                        } ?></td>
                                        <td>
                                        <td>
                                            <!--<a href="<?php echo URL::to('duty/edit/' . $look->id) ?>"><button type="button" name="edit-user" class="btn btn-info"><i class="fa fa-pencil"></i></button></a>
                                            <a href="<?php echo URL::to('duty/delete/' . $look->id . '/2') ?>"><button type="button" name="delete-user" value="on" class="btn btn-danger delete-user"><i class="fa fa-trash"></i></button></a> -->
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Records Not Found!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div id="group-paginate">
                            {{$smart_looks->links()}}
                    <!-- <?php echo $smart_looks->appends(@$old_input)->render(); ?> -->
                        </div>
                    </div>
                </div>
            </div>
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