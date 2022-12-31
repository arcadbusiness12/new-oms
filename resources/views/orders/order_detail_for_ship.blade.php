@if ($order)
@php
if( $order->payment_method_id == 1 ){ // 1 is cash on delivery
    $payment_class = "btn-primary";
    $method_name = "COD";
}else{
    $payment_class = "btn-danger";
    $method_name = "PP";
}
$payment_method = '<span style="border:1px solid black;"  class="badge '.$payment_class.'">'.$method_name.'</span>';
@endphp

<tr class="row_{{ $order->order_id }}">
    <input type="hidden" id="order_shipping_type" value="{{($order->shipping_type) ? $order->shipping_type : 'all'}}">
    <td>
      <label style="height: 11px;"  for="md_checkbox_{{ $order->order_id }}"><input checked="checked" name="generate-awb-chbx[]"  value="{{ $order->order_id }}" type="checkbox" id="md_checkbox_{{ $order->order_id }}" class="chk-col-green fwd-ordr-generate-awb-checkbox"  /></label>
      <input type="hidden" value="{{ $order->store }}" name="store[{{ $order->order_id }}]" >
    </td>
    <td scope="row" class="col-order_id">{{ $order->order_id }}</td>

    {{--  <td>
        <span class="badge bg-teal"> {{ ($order['status']['name'])?$order['status']['name']:'Not defined'}} </span>
    </td>  --}}
    <td>{{ $order->firstname }} {{$order->lastname }}</td>
    <td><span style='border:1px solid black;' class='badge badge-pill badge-primary ship_name'>{!! @$shipping_name !!}</span>@php echo $payment_method @endphp</td>
    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$order->created_at)->toFormattedDateString()}}</td>
    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$order->updated_at)->toFormattedDateString()}}</td>
    <td>{{ $order->mobile }}</td>
    <td>{{ $order->email }}</td>
    <td style="display: flex;">
        {{--  @if(isset($file_amount) && $order->total_amount != $file_amount)
        <div style="padding: 5px;border: 2px solid green;">
            <span class="font-10 font-bold" style="display: block;"> </span> {{$file_amount}}</span>
        </div>
        <div style="padding: 5px;border: 2px solid red;">
            <span class="font-10 font-bold" style="display: block;"> </span> {{$order->total_amount}}</span>
        </div>
        @else  --}}
        <div style="padding: 5px;border: 2px solid green;">
            <span class="font-10 font-bold" style="display: block;"> </span> {{$order->total_amount}}</span>
        </div>
        {{--  @endif  --}}
    </td>
    <td></td>
</tr>
<tr class="row_{{$order->order_id}}">
    <td colspan="2"><b class="sn_{{ $order->order_id }}"></b></td>
    <td colspan="4"><strong>Address: </strong><i><i>{{ $order->shipping_address_1 }} {{ $order->shipping_address_2 ? ", ".$order->shipping_address_2 : "" }}</i></i></td>
    <td colspan=""><strong>City: </strong><i>{{ $order->shipping_city }}</i></td>

</tr>
<tr class="margin-0 padding-0 row_{{$order->order_id}}">
    <td colspan="1" class="order-products">&nbsp;</a>

    <td colspan="8" class="margin-0 padding-0 order-products">
        <center>
            <table width="100%" style="font-size:12px;">
                @foreach ( $order->orderProducts as $ordered_product )
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="width: 5%;"><img src="{{ URL::asset('uploads/inventory_products/'.$ordered_product?->product?->image) }}" /></td>
                        <td>{{ $ordered_product->name }}<br>
                            @if(  $ordered_product->product?->option_value > 0  )
                                <strong>{{ $ordered_product->option_name }}</strong> : {{ $ordered_product->option_value }} ,
                            @endif
                            <strong>Color : </strong>{{ $ordered_product->product?->option_name }}</td>
                        <td>{{ $ordered_product->sku }}</td>
                        <td>{{ $ordered_product->quantity }}</td>
                        <td>{{ $ordered_product->price }}</td>
                        <td>{{ $ordered_product->total }}</td>
                    </tr>
                @endforeach
            </table>
         </center>
    </td>
</tr>
<tr  class="order-action row_{{$order->order_id}}" style="border-bottom:7px solid lightgray">
</tr>



@else
<tr id="not_found_row">
    <td colspan="10">
        <div class="alert alert-danger text-center">
            No orders found.
        </div>
    </td>
</tr>
<script>
setTimeout(function(){
  $("#not_found_row").hide();
},2000);
</script>
@endif
<script>
  $('#shipping_provider_name').val( $('.ship_name').html() );
console.log( $('.ship_name').html() );
</script>
