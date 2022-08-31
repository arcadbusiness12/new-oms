@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            @include('orders.order_search_section')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 13px !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                <th scope="col"><center>Order Id</center></th>
                                <th scope="col"><center>Customer</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Date Added</center></th>
                                <th scope="col"><center>Date Modified</center></th>
                                <th scope="col"><center>Telephone</center></th>
                                <th scope="col"><center>Email</center></th>
                                <th scope="col"><center>Total</center></th>
                               </tr>
                             </thead>
                             <tbody>
                                @if($data->count())
                                @foreach($data as $key=>$order)
                                    <tr>
                                        <td class="col-sm-1"><center>{{ $order->order_id }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->firstname }} {{ $order->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->courier_name }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->date_added }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->date_modified }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->telephone }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->email }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $order->total }} </center></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>Address:</strong>{{ $order->payment_area }},{{ $order->payment_address_1 }},{{ $order->shipping_address_2 }}</td>
                                        <td colspan="1"><strong>City:</strong>{{ $order->shipping_city ? $order->shipping_city : $order->shipping_zone }}</td>
                                        <td colspan="5" >@include('orders.order_progress_bar')</td>
                                    </tr>
                                    @if( $order->orderd_products )
                                        <tr>
                                            <td colspan="8">
                                                <center>
                                                <table width="100%" style="font-size:12px;">
                                                    @foreach ( $order->orderd_products as $ordered_product )
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td><img src="{{ $ordered_product?->product_details?->image }}" /></td>
                                                            <td>{{ $ordered_product->name }}<br>
                                                                @forelse ($ordered_product->order_options as $order_option )
                                                                    <strong>{{ $order_option->name }} :</strong> {{ $order_option->value }}
                                                                @empty
                                                                @endforelse
                                                            </td>
                                                            <td>{{ $ordered_product->model }}</td>
                                                            <td>{{ $ordered_product->quantity }}</td>
                                                            <td>{{ $ordered_product->price }}</td>
                                                            <td>{{ $ordered_product->total }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr  class="order-action" style="border-bottom: 7px solid #e9e9e9 !important">
                                        <td colspan="4">
                                            @if( (!empty($created_by) && $created_by->user_id == session('user_id') ) ||  session('role')=='ADMIN')
                                                <div class="row">
                                                    <div class="col-2">
                                                        <a  href="javascript:void(0)" class="waves-effect waves-blue" data-toggle="tooltip" data-placement="top" data-original-title="Cancel Order">
                                                            <form action="{{URL::to('orders/cancel-order')}}" id="cancel_order_form_{{ $order->order_id }}">
                                                                {{csrf_field()}}
                                                                <input type="hidden" name="order_id" value="{{ $order->order_id  }}" />
                                                                <button order_id="{{ $order->order_id  }}" type="button" class="btn btn-danger btn-sm cancel-order">
                                                                    Cancel Order
                                                                </button>
                                                            </form>
                                                        </a>
                                                    </div>
                                                    <div class="col-2">
                                                        <a data-orderid={{ $order->order_id  }} data-toggle="modal" data-target="#addressModal"
                                                        class="btn btn-info btn-sm  btn-edit-customer-adress">Edit Details</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                     </tr>
                                @endforeach
                                @endif
                             </tbody>
                            </table>
                        {{  $data->appends(@$old_input)->render() }}
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('inventoryManagement.dashboardModals')
<style>
.table td {
    border-top: none !important;
}
thead, tbody, tfoot, tr, td, th {
    border: none !important;
}
</style>
@endsection

@push('scripts')
<script>
    function changeProductStatusAjax(status,product_id){
        $.ajax({
            url: '{{route("change.product.status")}}',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: 'product_id='+product_id+'&status='+status,
            success: function (data) {
                if (data['status']) {
                    $(".toast-action").data('title', 'Action Done!');
                    $(".toast-action").data('type', 'success');
                    $(".toast-action").data('message', data['msg']);
                    $(".toast-action").trigger('click');
                } else {
                    $(".toast-action").data('title', 'Went Wrong!');
                    $(".toast-action").data('type', 'error');
                    $(".toast-action").data('message', data['msg']);
                    $(".toast-action").trigger('click');
                }
            }
        });
  }

  function viewInventory(sku){

    $('#history-tbl').css('display', 'none');
      var url = "{{ route('view.inventory.product.details', ':sku') }}";
      url     = url.replace(':sku', sku);
      console.log(url);
    $.ajax({
      type: 'GET',
      url: url,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      cache: false,
      success: function (data) {
        $('#porduct_view_content').html(data);
      }
    });
  }

  $('.history-btn').on('click', function() {
    // console.log($("input[name=product_id]").val());
    $('table .history').html('');
    $('.history-load').css('display', 'block');
    var id = $("input[name=product_id]").val();
    // $('.history').html("<b><center>Loaidng...</center></b>");
    console.log(id);
    var url = "{{route('inventory.product.history', ':id')}}";
    url = url.replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      cache: false,
      success: function(history) {
      $('.history-load').css('display', 'none');
        if(history.status){
          var html = '';
          $('#history-tbl').css('display', 'block');
            history.history.forEach(function(v) {
              var full_name = "";
              if( v.user ){
                full_name = v.user.firstname+" "+v.user.lastname;
              }
            html += '<tr><td class="text-center">'+full_name+'</td><td class="text-center">'+v.comment+'</td><td class="text-center">'+v.reason+'</td><td class="text-center">'+v.created_at+'</td></tr>';
          })
          $('table .history').html(html);
        }else{
          console.log("Notat");
          $('.msge').css('display', 'block');
        }

      }
    })
  });
  function editLocation(product_id){
    var url = "{{route('inventory.edit.product.location', ':id')}}";
    url = url.replace(':id', product_id);
    $.ajax({
      url: url,
      type: 'GET',
      cache: false,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      // data: 'product_id='+product_id+'&status='+status,
      success: function (data) {
        $('#porduct_location_content').html(data);
      }
    });
  }

  function editInventory(product_id){
    var url = "{{route('edit.inventory.product', ':id')}}";
        url = url.replace(":id", product_id);
    $.ajax({
      url: url,
      type: 'GET',
      cache: false,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      // data: 'product_id='+product_id+'&status='+status,
      success: function (data) {
        $('#edit_inventory_content').html(data);
      }
    });
  }

  function processPopupData(data,product_id){
    var count_obj =  Object.keys(data).length;
    var url = "{{route('inventory.print.pending.stock.label', ':id')}}";
    url = url.replace(':id', product_id);
    // var url = $('#frm_print').attr('data-url')+"/"+product_id
    $('#frm_print').attr('action',url);
    var content = "";
    var size_det = data.products_sizes;
    Object.keys(size_det).forEach(function(key) {
      console.log(key, size_det[key]);
      let row = size_det[key];
      let type = "";
      let size = "";
      if( data.option_value > 0 ){
        type = data.oms_options.option_name;
        size = row.oms_option_details.value;
      }else{
        type = "Color";
        size = data.option_name;
      }
      let style_center = 'align="center"';
      let text_box = '<input type="text" name="print_quant['+product_id+']['+row.option_value_id+']" size="3" placeholder="Enter value to print" class="form-control">';
      content += "<tr><td "+style_center+">"+type+"</td><td "+style_center+">"+size+"</td><td "+style_center+">"+row.available_quantity+"</td><td "+style_center+">"+text_box+"</td></tr>";


    });
    $('#printModal_content').html( content );

  }
  $('#frm_print').on('submit',function(){
    $('#printModal').modal('toggle');
  });

</script>
@endpush
