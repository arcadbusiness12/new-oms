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
                                        <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#ba-template" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">B.A Setting</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link show" id="w5--tab1" data-toggle="tab" href="#df-template" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">D.F Setting</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session()->has('success'))
              <div role="alert" class="alert alert-success">
              {{ session()->get('success') }}
              </div>
            @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Setting
                          </div>
                          
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
                                              <table class="table" width="100%" style="border: 1px solid #2196f3">
                              
                                              <thead >
                              
                                                <tr style="background-color: #2196f3;color:white">
                              
                                                <th scope="col"><center>Title</center></th>
                                                <th scope="col"><center>Action</center></th>
                              
                                                </tr>
                              
                                              </thead>
                              
                                              <tbody class="setting-body-data-ba">
                                                @if(count($ba_paid_promotion_main_setting) > 0)
                                                @foreach(@$ba_paid_promotion_main_setting as $key=>$setting)
                                                <tr id="row_ba{{$key}}" style="border-top: 1px solid gray">
                              
                                                          <td><center><label>{{ $setting->title }}</label></center></td>
                                                          <td>
                                                          <center><label>
                                                          @if(session('role') == 'ADMIN' || (array_key_exists('promotion/organic/settings/actions', json_decode(session('access'),true))))
                                                          <a href="#"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Add Products" onclick="checkSettings(1, '{{$setting->id}}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
                                                          <a href="#"><i class="fa fa-trash-o fa-2x" onclick="deleteMainSetting('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a>
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
                                
                                <div class="tab-pane fade text-center " id="df-template" role="tabpanel" aria-labelledby="df-template">
                                    <span class="alert alert-success msg_success" style="display: none;"></span>
                                    <div class="row new-setting-btn">
                                        <button type="button" class="btn btn-sm btn-success float-end" onclick="checkSettings(2)" style="display: inline;width:7%;position: absolute;top:0%;margin-left: 92%;margin-top: 4px;" data-toggle="modal" data-target=".setting_view_modal"><i class="icon icon-plus"></i> New</button>
                                    </div>
                                    <div class="table-responsive">
                                        <div id="status_changed_msg" style="display: none"></div>
                                              <table class="table" width="100%" style="border: 1px solid #2196f3">
                        
                                              <thead >
                        
                                                <tr style="background-color: #2196f3;color:white">
                        
                                                <th scope="col"><center>Title</center></th>
                                                <th scope="col"><center>Action</center></th>
                        
                                                </tr>
                        
                                              </thead>
                        
                                              <tbody class="setting-body-data-df">
                                                @if(count($df_promotion_main_setting) > 0)
                                                @foreach(@$df_promotion_main_setting as $key=>$setting)
                                                <tr id="row_df{{$key}}" style="border-top: 1px solid gray">
                        
                                                          <td><center><label>{{ $setting->title }}</label></center></td>
                                                          <td>
                                                          <center><label>
                                                          @if(session('role') == 'ADMIN' || (array_key_exists('promotion/organic/settings/actions', json_decode(session('access'),true))))
                                                          <a href="#"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Add Products" onclick="checkSettings(2, '{{$setting->id}}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
                                                          <a href="#"><i class="fa fa-trash-o fa-2x" onclick="deleteMainSetting('{{ $setting->id }}', '{{$key}}','df')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a>
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

  <!-- product view modal start -->
<div class="modal fade porduct_view_modal" id="promotion_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 70%">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Promotion</h5>
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button> -->
        </div>
        <div class="modal-body" id="porduct_view_content">
        </div>
        <!--<div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div> -->
        <div class="modal-footer">
          <span class="text-right" id="error_text" style="color:red;">  </span>
          <button type="button" form="promotion-form" id="saveForm" class="btn btn-primary">Save</button> 
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- edit invertory modal end -->
  
  <!-- edit inventory modal start -->
  <div class="modal fade add_product_modal" id="add_product_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Add products to group "<span id="group-name"></span>"</h5><br>
          <div class="col-md-6">
             <input id="product_sku" name="product_sku" list="product_skus" value="{{ @$old_input['product_sku'] }}" type="text" placeholder="Search By SKU" class="form-control product_sku" autocomplete="off">
             <datalist id="product_skus"></datalist>
           </div>
  
           <div class="col-md-6">
           <input type="button" name="search_product" class="btn btn-sm btn-primary search_product" value="Add">
           </div>
        </div>
        <hr>
        <div class="modal-body col-md-12" >
          <form id="products-form" name="product_form" action="javascript:void(0)" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="group_id" id="selected_group" value="">
            <div id="add_search_product_content"></div>
            <div id="add_product_content">Loadding..</div>
          </form>
         </div>
        <div class="modal-footer">
        <span class="text-right" id="errorr_text" style="color:red;">  </span>
          <button type="button" form="products-form" id="productsForm" class="btn btn-primary">Save</button> 
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  
  <!--  Store social post modal end -->
  <div class="modal fade store_social_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 60%">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Promotion</h5> / 
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
          <form id="promo_form" name="promo_form" method="post">
            {{csrf_field()}}
          <div id="setting_view_contentt">
          <div class="modal-content-loader"></div>
        </div>
        <div class="modal-footer">
          <span class="text-right" id="error_mesge" style="color:red;">  </span>
          <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
          <button type="submit" form="promo_form" id="savePromoForm" class="btn btn-primary">Save</button> 
          <button type="button" class="btn btn-danger promotion-dismiss" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  </div>
  <!-- 
    Store social post Modal code end-->
  
    <!--  Setting modal end -->
  <div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 60%">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Setting</h5>
          <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
        <div class="modal-content-loader"></div>
          <div id="setting_schedule_view_content">
          
          
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- Setting Modal code end-->
  <!--  update price modal start -->
  <div class="modal fade update_price_modal" id="update_price_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="width: 60%">
      <div class="modal-content" >
        <div class="modal-header">
          {{--  <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Add/Update Prices</h5>  --}}
          <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
          <form method="post" id="frm_update_site_prices" action="">
            {{csrf_field()}}
            <input type="hidden" name="group_name" id="group_name">
            <div class="row" id="update_prices_popup_content">
            </div>
            <div class="row col-sm-12">
              <button type="submit" class="btn btn-success pull-right">SAVE</button>
            </div>
          </form>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection
@push('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<!-- SweetAlert Plugin Js -->


<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script>
    $(document).ready(function() {
      var setting = <?php echo $promotion_orginaic_setting ?>;
      if(setting.length < 1) {
        $('.save-btn').css('display', 'none');
        $('#add-more').text('Add Setting');
      }
    $('.datepicker').datepicker({
         format: 'dd/mm/yyyy',
         //startDate: '-3d'
     }).on('changeDate', function(e){
      $(this).datepicker('hide');
    });
    setTimeout(() => {
        $('.alert').css('display', 'none');
    }, 5000);
    });
    $(document).ready(function(){
      $(".nav-tabs a").click(function(){
        $(this).tab('show');
      });
    });
     
    
    function editgroup(object) {
      var ob = JSON.parse(object);
      $('#group-edit-input').val(ob.id);
      $('#add-edit-input').val(ob.name);
      $('#add-edit-input').focus();
      $('.group-form-btn').val('Update');
    }
    $('#saveForm').click(function(e) {
      e.preventDefault();
      $('#error_text').text('');
      $.ajax({
        url: "{{url('/product/promotion/form')}}",
        type: "POST",
        data: $('#promotion-form').serialize(),
        success: function(result) {
          if(result.status) {
            $('.msge_success').css('display', 'block');
            $('.msge_success').text(result.msge);
            $('#promotion_modal').modal('toggle');
            setTimeout(() =>{
              $('.msge_success').css('display', 'none');
            },5000);
          }
        },error: function(error) {
            $('#error_text').css('display', 'inline-block');
            if(error.responseJSON.product_id) {
              $('#error_text').text('Please select/checked atlest 1 product.');
            }
            if(error.responseJSON.social) {
              $('#error_text').text(error.responseJSON.social[0]);
            }
            if(error.responseJSON.data) {
              $('#error_text').text(error.responseJSON.data[0]);
            }
            if(error.responseJSON.time) {
              $('#error_text').text(error.responseJSON.time[0]);
            }
            
        }
      })
    });
    
    $('#productsForm').on('click', function(e) {
      e.preventDefault();
      $.ajax({
        url: "{{url('/add/products/to/group')}}",
        type: "POST",
        data: $('#products-form').serialize(),
        success: function(response) {
          if(response.status) {
            $('.msgee_success').css('display', 'block');
            $('.msgee_success').text(response.msge);
            $('#add_product_modal').modal('toggle');
            setTimeout(() =>{
              $('.msgee_success').css('display', 'none');
            },5000);
          }
        },error: function(error) {
          if(error.responseJSON.g_product) {
            $('#errorr_text').text('Please select atlest 1 product.');
          }
        }
    
      })
    })
    
      function productDetails(product) {
        $.ajax({
          url: "{{url('/inventory_manage/group/promotion/products')}}/"+product,
          type: 'GET',
          cache: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token]"').attr('content')
          },
          success: function(result) {
            $('#porduct_view_content').html(result);
          } 
        });
      }
      
     $("#promote_report").submit(function(event) { 
      event.preventDefault();
      // action="{{url('product/promote/report')}}"
      $.ajax({
        url: "{{url('product/promote/report')}}",
        method: "POST",
        data: $(this).serialize(),
        beforeSend: function() {
          $('#search_pro_report').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
          $('#search_pro_report').prop('disabled', true);
        },
        complete: function() {
          $('#search_pro_report').html('<i class="fa fa-filter"></i>Search');
          $('#search_pro_report').prop('disabled', false);
        }
      }).done(function(result) {
        $('#report_data').html(result);
      })
     });
    
     $("#search_by_group_type").on('click',function(event) {
      event.preventDefault();
      var type = $('#product_change_status').val();
      $.ajax({
        url: "{{route('get.product.type')}}",
        type: "GET",
        data: $('#by_group_type').serialize(),
        cache: false,
        beforeSend: function() {
          $('#search_by_group_type').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
          $('#search_by_group_type').prop('disabled', true);
        },
        complete: function() {
          $('#search_by_group_type').html('<i class="fa fa-filter"></i>Search');
          $('#search_by_group_type').prop('disabled', false);
        }
      }).then(function(resp) {
        $('#table-data').html(resp);
      })
     });
    
     $('.search_product').on('click', function() {
       loadProducts($("input[name='product_sku']").val(), 1);
     })
    
     $('#by_group_type_organic').submit(function(event) {
       event.preventDefault();
       $.ajax({
        url: "{{route('get.product.type')}}",
        type: "GET",
        data: $(this).serialize(),
        cache: false,
        beforeSend: function() {
          $('#search_by_group_type').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
          $('#search_by_group_type').prop('disabled', true);
        },
        complete: function() {
          $('#search_by_group_type').html('<i class="fa fa-filter"></i>Search');
          $('#search_by_group_type').prop('disabled', false);
        }
      }).then(function(resp) {
        $('#organic-table-data').html(resp);
      })
     })
      $(document).ready(function(){
    
       $("#myInput").on("keyup", function() {
    
         var value = $(this).val().toLowerCase();
    
         $("#myTable tr").filter(function() {
    
           $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    
         });
    
       });
    
     });
    
    $('.dismiss-btn').on('click', function() {
      
      $('#history-tbl').css('display', 'none');
      $('.msge').css('display', 'none');
    })
    
      function deleteMainSetting(id, rowIndex, row) {
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
      function deleteSetting(id) {
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
                    url: "{{url('/destroy/setting')}}/" + id,
                    data: {_token: CSRF_TOKEN},
                    dataType: 'JSON',
                    success: function (results) {
    
                        if (results.success === true) {
                          $('#srow_'+id).remove();
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
      function deleteCateSetting(setting, key) {
        swal({
                title: "Are sure remove the name?",
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
                    url: "{{url('/destroy/cate/setting')}}/",
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, ids:setting},
                    dataType: 'JSON',
                    success: function (results) {
    
                        if (results.status === true) {
                          $('#cate_row_'+key).remove();
                            swal("Done!", results.mesg, "success");
                        } else {
                            swal("Sorry!", "There are some issues.", "error");
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
      
    
      function removeProductFromList(product) {
        $('#product_rom_'+product).remove();
      }
    
      function selectSite(index , site) {
        if(index && site) {
                if(site == 1) {
                  $('#store_'+index).val(site);
                  $('#store_name_'+index).text('ba.com');
                }else {
                  $('#store_'+index).val(site);
                  $('#store_name_'+index).text('df.com');
                }
                $('#head_'+index).css('display', 'table-row-group');
                $('#social_tr_'+index).css('display', 'contents');
                // $('.msge_success').text(response.message);
    
        }else {
          $('#head_'+index).css('display', 'none');
          $('#social_tr_'+index).css('display', 'none');
        }
      }
    
      // function selectedSiteSocialPosts(store) {
      //   console.log(store);
      //   $('.store_social_view_modal').modal('show');
      //   if(store) {
      //     $.ajax({
      //       url: "{{url('/store/social/posts')}}/"+store,
      //       type: "GET",
      //       cache: false,
      //       success: function(resp) {
      //         console.log(resp);
      //         $('.modal-content-loader').css('display', 'none');
      //         $('#porduct_view_contentt').html(resp)
      //       }
      //     })
      //   }
      // }
    
      function getSettingTemplate(store, group, type) {
        $('.modal-content-loader').css('display', 'none');
        if(store) {
          $('#top-title').text(getStoreName(store));
          $('.store_social_view_modal').modal('show');
          $.ajax({
            url: "{{url('/get/setting/template')}}/"+store +'/' +group +'/'+ type,
            type: "GET",
            cache: false,
            success: function(resp) {
              $('#setting_view_contentt').html(resp)
            }
          })
        }
      }
    
      function resetLoading(_this) {
            $(_this + ' .cart-loader').html('');
            $(_this).removeClass('loading');
        }
    
        function startLoading(_this) {
            $(_this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $(_this).addClass('loading');
        }
    
    function changeGroupType(type, product) {
      if(type && product) {
        $.ajax({
        url: "{{url('/change/group/type')}}/"+type + '/' + product,
        type: "GET",
        cache: false,
        success: function(resp) {
          if(resp.status) {
            $('.msges_success'+product).text(resp.message);
            $('.msges_success'+product).css('display', 'block');
            // $('.msges_success').attr('tabindex', -1).focus();
            // $('html, body').animate({ scrollTop: $('#msges_success').offset().top }, 'slow');
            setTimeout(() => {
              $('.msges_success'+product).css('display', 'none');
            },5000);
          }
        }
      })
      }
      
    }
    
    
    
    $('#promo_form').submit(function(event) {
      event.preventDefault();
      $.ajax({
        url: "{{url('/save/promo/posting')}}",
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        beforeSend: function() {
          $('#savePromoForm').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
          $('#savePromoForm').prop('disabled', true);
        },
        complete: function() {
          $('#savePromoForm').html('Save');
          $('#savePromoForm').prop('disabled', false);
        },
        error: function(error) {
          if(error.responseJSON.social) {
    
            $('#error_mesge').text('Please select at least one social..');
          }
          if(error.responseJSON.dates) {
            $('#error_mesge').text('Please select at least one date..');
          }
          setTimeout(() => {
            $('#error_mesge').text('');
          }, 3500)
        }
        
      }).then(function(resp) {
        if(resp.status) {
          $('#m_success').show();
          $('#m_success').text('Posting added successfully.');
          setTimeout(() => {
            $('#promotion_setting_modal').modal('toggle');
            $('#m_success').text('');
            $('#setting_view_contentt').html('');
          }, 1500)
        }else{
          console.log(resp);
        }
      })
    });
    
    function checkSettings(store, id = null) {
      $('.modal-content-loader').show();
      $.ajax({
        url: "{{url('/productgroup/get/setting/template/form')}}/"+ id,
        type: "GET",
        data: {store:store, type:1},
        cache: false,
        success: function(resp) {
          $('.modal-content-loader').css('display', 'none');
          $('#setting_schedule_view_content').html(resp);
        }
      })
    }
    
    function getSchedules(value,type, store, cate) {
      if(value) {
        $.ajax({
          url: "{{url('/get/template/schedules')}}/"+ value +'/' +type +'/' +cate +'/' +store,
          type: "GET",
          cache: false,
          beforeSend: function() {
            $('.tmp-loader').css('display', 'block');
          },
          complete: function() {
            $('.tmp-loader').css('display', 'none');
          }
        }).then(function(resp) {
          $('.schedules-data').html(resp);
        })
      }
    }
    function loadsettings() {
      console.log("Ok Called");
    }
    
    function getStoreName(store) {
      return (store == 1) ? 'BusinessArcade' : 'DressFair';
    }
    $('.promotion-dismiss').on('click', function() {
      $('.schedule_post').prop('selectedIndex',-1);
      $('#setting_view_contentt').html('');
    });
    
    
    
    $(document).ready(function(){
    
    //  $(document).on('click', '.pagination a', function(event){
    //   event.preventDefault(); 
    //   var page = $(this).attr('href').split('page=')[1];
    //   console.log(page);
    //   fetch_data(page);
    //  });
    
    //  function fetch_data(page)
    //  {
    //   $.ajax({
    //    url:"/pagination/fetch_data?page="+page,
    //    success:function(data)
    //    {
    //     $('#table_data').html(data);
    //    }
    //   });
    //  }
     
    });
    // $(document).click('.close-popup', function() {
    //   $('#setting_schedule_view_content').html('');
    // })
  </script>
@endpush