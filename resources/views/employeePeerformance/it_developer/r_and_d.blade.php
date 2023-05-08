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
            
            
         <?php if(count($rAndDs) > 0) { ?>
           <?php foreach ($rAndDs as $k => $rd) { ?>
            <div class="card order_list mt-4">
                <div class="row top_row m-4">
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
                    <div class="col-2 col-grid mt-4 text-center">
                            {{-- <b>2021-11-29</b> <br><br>
                            <b>2021-11-30</b> --}}
                            <div class="badge badge-secondary"><?php echo $rd->created_at ?></div> <br><br>
                            <div class="badge badge-secondary"><?php echo $rd->event_date ?></div>
                       </div>
                       

                    <div class="col-2 mt-4 col-grid text-center">
                        <div class="badge badge-{{($rd->is_emergency == 1) ? 'warning' : 'success'}}">
                            <td><?php if($rd->is_emergency == 1) {echo 'Urgent';} else {
                                echo 'Normal';
                              } ?>    
                        </div>
                        <?php if( $rd->assignedUser ) {?>
                            <div class="assiged col-8 col-grid">Assigned To
                                 <?php echo $rd->assignedUser['username'] ?> 
                                </div>
                            <?php } ?>
                            
                    </div>
                </div>
                  
                <div class="row text-right mb-4 mr-5">
                    <?php if($rd->need_to_approve == 1 && session('role') == 'ADMIN') {?>
                        <div class="assiged col-xs-12 btn-action{{$k}}">
                            <button class="btn btn-danger" onclick="acceptRejectRequest('{{$rd->id}}',2, {{$user}}, '{{$action}}', '{{$k}}')" tabindex="1" style="float:right;background-color: rgb(147, 5, 5);, rgba(0, 0, 0, 0.05) 0px 0px 0px 1px inset;margin: -5px 5px 0px;font-size: 12px;">
                                Reject
                             </button>
                              <button class="btn btn-success" onclick="acceptRejectRequest('{{$rd->id}}',1, {{$user}}, '{{$action}}', '{{$k}}')" tabindex="1" style="float:right; background-color: #097d07; , rgba(0, 0, 0, 0.05) 0px 0px 0px 1px inset;margin: -5px 5px 0px;font-size: 12px;">
                              Accept
                              </button>
                        </div>
                        <?php }elseif ($rd->need_to_approve == 1 && session('role') != 'ADMIN') {?>
                            <div class="assiged col-xs-12 approve-st">
                                 <div class="badge badge-warning col-2 ">Pending</div>
                            </div>
                       <?php }elseif ($rd->need_to_approve == 2 && $rd->approved == 1) {?>
                        <div class="assiged col-xs-12 approve-st">
                             <div class="badge badge-success col-2 ">Approved</div>
                        </div>
                        <?php }elseif ($rd->need_to_approve == 2 && $rd->approved == 2) {?>
                            <div class="assiged col-xs-12 approve-st">
                                <div class="badge badge-danger col-2 ">Rejected</div>
                            </div>
                            <?php }else {?>
                           {{$rd->need_to_approve}}
                       <?php } ?>
                       <span class="ap-message{{$k}}" style="color:#097d07; display:none;"></span>
                       <span class="rj-message{{$k}}" style="color:#b6310c;display:none;"></span>
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
    console.log(status);
    $.ajax({
        url: "{{url('/admin/approve/request/response')}}/"+ id +"/"+ status +"/"+ user +"/"+ action,
          type: "GET",
          cache: false,
          success: function(resp) {
            if(resp.status) {
              $('.btn-action'+index).remove();
              if(status == 1) {
                $('.ap-message'+index).css('display', 'block');
                $('.ap-message'+index).text('Request Approved');
              }else {
                $('.rj-message'+index).css('display', 'block');
                $('.rj-message'+index).text('Request Rejected');
              }
            }else {
              
            $('.request-action-btn'+index).remove();
            }
            
            
            
          }
    });
}
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush