@extends('layouts.app')

@section('content')
<style>
   .area_type_option{
    width: 110px !important;
  }
  .alert-success {
    position: sticky;
    /* Define at what point you would like it to stay fixed */
    top: 0; 
    z-index: 999;
  }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">

                    <div class="card no-b form-box">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        <div class="card-header white">
                            <div class="panel-body">
                                <form name="filter_orders" id="filter_orders" method="GET" action="{{ route('shipping.providers') }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="by_status" class="form-control">
                                                        <option value="1" {{ (@$old_input['by_status']==1 || @$old_input['by_status'] == "" ) ? "selected" : "" }}>Active</option>
                                                        <option value="0" {{ @$old_input['by_status']==0 ? "selected" : "" }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                          <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <form action="<?php echo route('shipping.providers') ?>" method="post" name="form-setting">
                            {{ csrf_field() }}
                          <div class="table-responsive">
                            
                            <table class="table table-hover">
                                <thead>
                                    <tr style="background-color: #3f51b5;color:white">
                                        <th>Provider ID</th>
                                        <th>Shipping Courier</th>
                                        @if( session('user_group_id') == 1 )
                                        <th>Charges</th>
                                        <th>Payment Type</th>
                                        @endif
                                        <th>Pickup Start</th>
                                        <th>Pickup End</th>
                                        <th>Pickup Print</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($shipping_providers) { 
                                            $shipment_print = "";
                                            ?>
                                    <?php foreach ($shipping_providers as $shipping_provider) {
                                          $shipment_print = "-";
                                          if( @$shipping_provider['shipment_print'] == 1 ){
                                            $shipment_print = "Short";
                                          }else if( @$shipping_provider['shipment_print'] == 2 ){
                                            $shipment_print = "Long";
                                          }
                                      ?>
                                        <tr>
                                            <td><?php echo $shipping_provider['shipping_provider_id'] ?></td>
                                            <td><?php echo $shipping_provider['name'] ?></td>
                                            @if( session('user_group_id') == 1 )
                                            <td><?php echo $shipping_provider['shipping_charges'] ?></td>	
                                            <td><?php echo ($shipping_provider['payment_type'] == 1) ? "From COD" : "Invoice" ?></td>	
                                            @endif
                                            <td><?php echo date("h:i A",strtotime($shipping_provider['pickup_start_time'])) ?></td>
                                            <td><?php echo date("h:i A",strtotime($shipping_provider['pickup_end_time'])) ?></td>
                                            <td><?php echo $shipment_print; ?></td>
                                            <td>
                                                <?php if($shipping_provider['is_active']) { ?>
                                                    <div class="label label-success">Active</div>
                                                <?php } else { ?>
                                                    <div class="label label-danger">Deactive</div>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="javascript:void()"><span class="fa fa-pencil shipment_edit" data-toggle="modal" shipping_row="{{ json_encode($shipping_provider) }}" data-target="#editShipmentModal" title="Edit"></span></a>
                                                @if( session('user_group_id') == 1 )
                                                  <?php if($shipping_provider['is_active']) { ?>
                                                      <button type="submit" name="deactive_shipping_provider" value="<?php echo $shipping_provider['shipping_provider_id'] ?>" class="btn btn-danger">Deactive</button>
                                                  <?php } else { ?>
                                                      <button type="submit" name="active_shipping_provider" value="<?php echo $shipping_provider['shipping_provider_id'] ?>" class="btn btn-success">Active</button>
                                                  <?php } ?>
                                                @endif
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Shipping Providers Not Found!</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            
                        </div>
                        </form>
            </div>
           

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection
<div class="modal fade" id="editShipmentModal" tabindex="-1" role="dialog" aria-labelledby="editShipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editShipmentModalLabel"></h5>
            <button type="button" class="close danger" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" >
          <div class="alert alert-success hidden"></div>
          <div class="row">
            <div class="col-sm-12">
              <span><h3 id="shipping_name"></h3></span>
            </div>
          </div>
        <form method="post" id="frm_edit_shipment">
            <input type="hidden" name="shipping_id" id="shipping_id" class="form-control">
            {{ csrf_field() }}
            @if( session('user_group_id') == 1 )
            <div class="row">
                <div class="col-sm-6">
                  <div class="col-sm-2">
                    Charges
                  </div>
                  <div class="col-sm-6">
                    <input type="text" name="charges" id="charges" class="">
                  </div>
                </div>
            </div>
            <br>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-line">
                      <select class="form-control" name="payment_type" id="payment_type">
                          <option value="">-Select Payment Type-</option>
                          <option value="1">Deduction From COD</option>
                          <option value="2">Invoice</option>
                      </select>
                    </div>
                </div>
              </div>
              <div class="col-sm-6">
                 <div class="form-group">
                    <div class="form-line">
                    <input type="number" class="form-control mobile_number" name="contact" placeholder="Contact No.">
                    </div>
                </div>
              </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                 <div class="form-group">
                    <div class="form-line">
                  <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <span class="invalid-response" role="alert"></span>
                 </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                       <div class="form-line">
                     <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                       </div>
                    </div>
                   </div>
              </div>
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                   <div class="form-line">
                      <div class="form-line">
                        <label>Pickup Start Time</label>
                        <select class="form-control" id="pickup_start_time" name="pickup_start_time">
                            <option value="">-Pickup Start Time-</option>
                            <option value="15:00:00">3 PM</option>
                            <option value="15:30:00">3:30 PM</option>
                            <option value="16:00:00">4 PM</option>
                            <option value="16:30:00">4:30 PM</option>
                            <option value="17:00:00">5 PM</option>
                            <option value="17:30:00">5:30 PM</option>
                            <option value="18:00:00">6 PM</option>
                            <option value="18:30:00">6:30 PM</option>
                            <option value="19:00:00">7 PM</option>
                        </select>
                      </div>
                   </div>
                </div>
               </div>
               <div class="col-sm-4">
                <div class="form-group">
                  <div class="form-line">
                    <label>Pickup End Time</label>
                    <select class="form-control" id="pickup_end_time" name="pickup_end_time">
                        <option value="">-Pickup End Time-</option>
                        <option value="19:00:00">7 PM</option>
                        <option value="20:00:00">8 PM</option>
                    </select>
                  </div>
                </div>
               </div>
               <div class="col-sm-4">
                <div class="form-group">
                  <div class="form-line">
                    <label>Shipment Print</label>
                    <select class="form-control" id="shipment_print" name="shipment_print">
                        <option value="">-Shipment Print-</option>
                        <option value="1">Short</option>
                        <option value="2">Long</option>
                    </select>
                  </div>
                </div>
               </div>
            </div>  
            @endif
            <div class="row">

              <br>
              <div class="col-sm-12">
                <input type="submit" value="Update" class="btn btn-success  pull-right" >
              </div>
            </div>
            <div class="row">
               @forelse ($zones as $key=>$zone)
               <hr>
               <div class="row">
                <div class="col-sm-2">
                  <p><a href="javascript:void()" class="pull-right">{{ $zone->name }}</a></p>
                </div>
                <div class="col-sm-6" style="height: 300px;position:relative;">
                  <div style="max-height:100%;overflow:auto; padding:5px">
                    @forelse ($zone->areas as $key=>$area)
                    <div class="col-sm-8" style="
                    float: left;">
                      <p><input type="checkbox" name="ba_areas[]" class="ba_chk_area" id="ba_area_{{$area->area_id}}" value="{{$area->area_id}}" style="width:10px;"> &nbsp;{{ $area->name }}</p>
                    </div>
                    <div class="col-sm-4" style="
                    float: left;">
                      <select name="ba_area_type[]" data-area-id="{{$area->area_id}}" class="area_type_option pull-right">  
                        <option value="1" {{ $area->area_type==1 ? "selected" : ""  }}>Regular</option>
                        <option value="2" {{ $area->area_type==2 ? "selected" : ""  }}>Remote</option>
                      </select>
                    </div>
                    @empty
                    @endforelse
                  </div>
                </div>
              </div>
              @empty
                <p>No zone found</p>
              @endforelse
            </div>

        </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        </div>
    </div>
    </div>
<!-- edit shipment Modal code end-->
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<script type="text/javascript">
    $('.shipment_edit').on('click',function(event){
        //console.log( $(this).attr('shipping_row') );
        $('.ba_chk_area').removeAttr('checked');
        var row      = JSON.parse( $(this).attr('shipping_row') );
        console.log(row);
        $("#shipping_name").html(row.name);
        $('#charges').val(row.shipping_charges);
        $('.mobile_number').val(row.mobile_number);
        $('#shipping_id').val(row.shipping_provider_id);
        $('#payment_type').val(row.payment_type).change();
        $('#pickup_start_time').val(row.pickup_start_time).change();
        $('#pickup_end_time').val(row.pickup_end_time).change();
        $('#shipment_print').val(row.shipment_print).change();
        var ba_areas = JSON.parse( row.ba_areas );
        // console.log(row);
        if( ba_areas ){
          //ba_areas.forEach((x, i) => console.log(x));
          ba_areas.forEach(function(x, i){
            $('#ba_area_'+x).prop('checked','checked');
          });
        }
       
        
    });
    $('#frm_edit_shipment').on('submit',function(e){
        e.preventDefault();
        var form_data = $(this).serialize();
        console.warn($(this).serialize());
        $.ajax({
            method: "POST",
            //url: APP_URL + "/orders/activity-details",
            url: "{{route('purchase_manage.updateShippingProvider')}}",
            data: form_data,
            dataType: 'json',
            cache: false,
            success:function(data){
                if(data.status==1){
                  $('.alert-success').html(data.msg).removeClass('hidden').show();
                  setTimeout(function(){
                    $('.alert-success').hide();
                  },5000);
                  location.reload();
                }else{
                    alert(data.msg);
                }
            },
            error: function(er) {
                if(er.responseText.indexOf('password') !== -1) {
                    var str = JSON.parse(er.responseText);
                    $('.invalid-response').text(str.password[0]);
                    setTimeout(() => {
                    $('.invalid-response').text('');
                    },5000);
                            }
            }
        });
    });
$('.delete-user').click(function(){
    if(!confirm('Are you sure to delete user?')) return false;
});
$(document).on('change','.area_type_option',function(){
  var current_area_type = $(this).val();
  var area_id = $(this).attr('data-area-id');
  if( area_id === undefined ){
    return;
  }
  $.ajax({
    method: "POST",
    url: APP_URL+"/change-area-type",
    data: { current_area_type:current_area_type,area_id:area_id },
    dataType: 'json',
    cache: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success:function(data){
        if(data.status==1){
          $('.alert-success').html(data.msg).removeClass('hidden').show();
          setTimeout(function(){
            $('.alert-success').hide();
          },5000);
        }else{
            alert(data.msg);
        }
    },
    error: function(er) {
        if(er.responseText.indexOf('password') !== -1) {
            var str = JSON.parse(er.responseText);
            $('.invalid-response').text(str.password[0]);
            setTimeout(() => {
            $('.invalid-response').text('');
            },5000);
        }
    }
  });
});
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush
