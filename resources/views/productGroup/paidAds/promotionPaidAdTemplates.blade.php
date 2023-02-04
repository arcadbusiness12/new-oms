@extends('layouts.app')

@section('content')
<style>
    .group-title {
        /* width: 75%;
        margin-left: 30px; */
    }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <div class="align-self-end float-start">
                                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#ba-template" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">B.A Paid Ads Template</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link show" id="w5--tab1" data-toggle="tab" href="#df-template" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">B.A Paid Ads Template</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Templates
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          {{-- <div class="table-responsive"> --}}
                           <div id="status_changed_msg" style="display: none"></div>
                           <div class="tab-content">

                                <div class="tab-pane fade  active show " id="ba-template" role="tabpanel" aria-labelledby="ba-template">
                                    
                                    <span class="alert alert-success msg_success" style="display: none;"></span>
                                    <div class="row new-setting-btn text-right">
                                    <!--<h5 style="display: inline;">BusinessArcade Setting</h5> -->
                                    <button type="button" class="btn btn-sm btn-success" onclick="checkSettings(1)" data-toggle="modal" data-target=".setting_view_modal" style="display: inline;width:7%;position: absolute;top:0%;margin-left: 92%;margin-top: 4px;"><i class="icon icon-plus"></i> New</button>
                                    </div>
                                    <div class="table-responsive">
                                        <div id="status_changed_msg" style="display: none"></div>
                                            <table class="table" width="100%" style="border: 1px solid #3f51b5">
                                            <thead >

                                            <tr style="background-color: #3f51b5;color:white">

                                            <th scope="col"><center>Title</center></th>
                                            <th scope="col"><center>User</center></th>
                                            <th scope="col"><center>Socials</center></th>
                                            <th scope="col"><center>Ad Type</center></th>
                                            <th scope="col"><center>Action</center></th>

                                            </tr>

                                            </thead>

                                            <tbody class="setting-body-data-ba">
                                            @if(count($ba_paid_promotion_main_setting) > 0)
                                            @foreach(@$ba_paid_promotion_main_setting as $key=>$setting)
                                            <tr id="row_ba{{$key}}" style="border-top: 1px solid gray">

                                                        <td><center><label>{{ $setting->setting_name }}</label></center></td>
                                                        <td><center><label>{{ @$setting->user->firstname }} {{ @$setting->user->lastname }}</label></center></td>
                                                        <td><center><label>{{ $setting->title }}</label></center></td>
                                                        <td><center><label>{{ $setting->adsType->name }}</label></center></td>
                                                        <td>
                                                        <center><label>
                                                        @if(session('role') == 'ADMIN' || (array_key_exists('promotion/paid/settings/actions', json_decode(session('access'),true))))
                                                        <a href="#"><i class="icon icon-pencil-square-o fa-2x" style="color:green;" aria-hidden="true" title="Add Products" onclick="checkSettings(1, '{{$setting->id}}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
                                                        @if(session('role') == 'ADMIN')
                                                        <a href="#"><i class="icon icon-trash-o fa-2x" style="color:red;" onclick="deleteMainSetting('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a> | 
                                                        @endif
                                                        <a href="#"><i class="icon icon-copy fa-2x" onclick="createCupy('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Create Cupy" data-toggle="tooltip"></i></a>
                                                        @endif
                                                        
                                                        </label></center>
                                                        </td>

                                            </tr>
                                            @endforeach
                                        
                                            @else
                                            <tr id="tr_{{@$group->id}}" style="border-top: 1px solid gray">

                                            <td colspan="2" class="column col-sm-12">
                                                <center><label>No Setting Available..</label></center>
                                            </td>
                                            </tr>
                                            @endif

                                        </tbody>
                                       
                                        </table>
                                        {{$ba_paid_promotion_main_setting->links()}}
                                        </div>
                                </div>
                                
                                <div class="tab-pane fade text-center " id="df-template" role="tabpanel" aria-labelledby="df-template">
                                    <span class="alert alert-success msg_success" style="display: none;"></span>
                                    <div class="row new-setting-btn">
                                        <button type="button" class="btn btn-sm btn-success float-end" onclick="checkSettings(2)" style="display: inline;width:7%;position: absolute;top:0%;margin-left: 92%;margin-top: 4px;" data-toggle="modal" data-target=".setting_view_modal"><i class="icon icon-plus"></i> New</button>
                                    </div>
                                        <div class="table-responsive">
                                        <div id="status_changed_msg" style="display: none"></div>
                                            <table class="table" width="100%" style="border: 1px solid #3f51b5">
                                            <thead >
                            
                                                <tr style="background-color: #3f51b5;color:white">
                            
                                                <th scope="col"><center>Title</center></th>
                                                <th scope="col"><center>User</center></th>
                                                <th scope="col"><center>Socials</center></th>
                                                <th scope="col"><center>Ad Type</center></th>
                                                <th scope="col"><center>Action</center></th>
                            
                                                </tr>
                            
                                            </thead>
                            
                                            <tbody class="setting-body-data-df">
                                                @if(count($df_paid_promotion_main_setting) > 0)
                                                @foreach(@$df_paid_promotion_main_setting as $key=>$setting)
                                                <tr id="row_ba{{$key}}" style="border-top: 1px solid gray">
                            
                                                        <td><center><label>{{ $setting->setting_name }}</label></center></td>
                                                        <td><center><label>{{ @$setting->user->firstname }} {{ @$setting->user->lastname }}</label></center></td>
                                                        <td><center><label>{{ $setting->title }}</label></center></td>
                                                        <td><center><label>{{ $setting->adsType->name }}</label></center></td>
                                                        <td>
                                                        <center><label>
                                                        @if(session('role') == 'ADMIN' || (array_key_exists('promotion/paid/settings/actions', json_decode(session('access'),true))))
                                                        <a href="#"><i class="icon icon-pencil-square-o fa-2x" style="color:green;" aria-hidden="true" title="Add Products" onclick="checkSettings(2, '{{$setting->id}}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
                                                        <a href="#"><i class="icon icon-trash-o fa-2x" style="color:red;" onclick="deleteMainSetting('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a> | 
                                                        <a href="#"><i class="icon icon-copy fa-2x" onclick="createCupy('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Create Cupy" data-toggle="tooltip"></i></a>
                                                        @endif 
                                                        </label></center>
                                                        </td>
                            
                                                </tr>
                                            @endforeach
                                            
                                            @else
                                            <tr id="tr_{{@$group->id}}" style="border-top: 1px solid gray">
                            
                                            <td colspan="2" class="column col-sm-12">
                                                <center><label>No Setting Available..</label></center>
                                            </td>
                                            </tr>
                                            @endif
                            
                                        </tbody>
                                        </table>
                                        </div>
                                </div>
                                
                            </div>
                            
                         {{-- </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
  <!--  Setting modal end -->
  <div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="width: 124%;">
        <div class="modal-header">
          <h5 class="modal-title text-black fw-bold" id="exampleModalCenterTitle" style="display: inline-block;color:white !important;">Paid Ads Setting</h5>
          <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
        <div class="modal-content-loader text-center" id="loader">
            <div class="preloader-wrapper small active">
                <div class="spinner-layer spinner-green-only">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
                </div>
            </div>
        </div>
          <div id="setting_schedule_view_content">
          
          
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- Setting Modal code end-->
@endsection

@push('scripts')
<script>
   
function checkSettings(store, id = null) {
  $('.modal-content-loader').show();
  $.ajax({
    url: "{{url('/productgroup/get/paid/ads/setting/template/form')}}/"+ id,
    type: "GET",
    data: {store:store, type:2},
    cache: false,
    success: function(resp) {
      $('.modal-content-loader').css('display', 'none');
      $('#setting_schedule_view_content').html(resp);
    }
  })
}

function createCupy(id, rowIndex, row) {
    swal({
            title: "Duplicate?",
            text: "Are you sure want to duplicate copy!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, copy it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        },function (e) {
        if (e === true) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'GET',
                url: "{{url('/productgroup/create/main/setting/copy')}}/" + id,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {
                    if (results.success === true) {
                      $('#row_'+row+rowIndex).remove();
                        swal("Done!", results.message, "success");
                    } else {
                        swal("Sorry!", results.message, "error");
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                }
            });

        } else {
            e.dismiss;
        }

        }, function (dismiss) {
        return false;
        })
  }

  function deleteMainSetting(id, rowIndex, row) {
    // console.log(id);
    swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
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
                url: "{{url('/productgroup/destroy/main/setting')}}/" + id,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {

                    if (results.success === true) {
                      $('#row_'+row+rowIndex).remove();
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
        })
  }

</script>
@endpush