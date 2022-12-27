@extends('layouts.app')

@section('title', 'Home')

@section('content')
 <style>

        @media all {
            .card .body {
                font-size: 14px;
                color: #555;
                padding: 20px;
            }
          .page-break  { display: none; }
          .row_8 td{ border: 0px !important}
          .order-products td{border:none !important}
          .count{
                  display: inline-block !important;
                  height: 20px !important;
                  width: 20px !important;
                  line-height: 21px !important;
                  -moz-border-radius: 30px !important;
                  border-radius: 23px !important;
                  background-color: #000 !important;
                  color: white !important;
                  text-align: center !important;
                  font-size: 14px !important;
                }
         }

         @media print {
          .page-break  { display: block; page-break-before: always; }
          .panel.panel-default{border: none !important}
          .row_8 td{ border: 0px}
          .order-products td{border:none !important}
         }
         .comment-box {
            border: 1px dashed black !important;
            padding: 8px !important;
         }
    </style>
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <div class="row row-eq-height my-3 mt-3">
        @if (count($orders) > 0)
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="card no-b">
                    <div class="panel panel-default">
                        <div class="body">
                            @foreach ($orders as $order)
                            @php
                                        $whatsapp = '';
                                        if($order->alternate_number) {
                                            $whatsapp = '<i class="material-icons" style="color:green" title="Whatsapp Number">whatsapp</i>';
                                        }
                                        @endphp
                            <table style="width:100%;">
                                {{-- <tr>
                                    <td><img src="{{URL::asset('/assets/images/logo.jpg')}}"  style="width: 300px;" /></td>
                                    <td>
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{$order['order_id']}}"
                                             jsbarcode-textmargin="0" jsbarcode-height="100"  ></svg>
                                    </td>
                                </tr> --}}
                                <tr>
                                  <td align="left" style="width: 40%">
                                    @if(isset($order->reseller_logo))
                                      @if($order->reseller_logo != '')
                                        <img src="{{URL::asset($order->reseller_logo)}}" style="max-width:40%; max-height:40%"/ >
                                      @else
                                      <h1>{{$order->reseller_name}}</h1>
                                      @endif
                                    @else

                                        <img src="{{URL::asset('/assets/images/'.$order->omsStore->logo) }}" alt="{{ $order->omsStore->name }}" />
                                    @endif
                                </td>
                                  <td align="right" style="width: 30%">
                                      <div class="pull-right"><svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{$order->order_id}}"
                                           jsbarcode-textmargin="0" jsbarcode-height="100"></svg></div>
                                  </td>
                                  <td align="right" style="width: 30%">
                                    @if( $order->omsOrder->assignedCourier )
                                    <b>{{ $order->omsOrder->assignedCourier->name }}</b><br>
                                    {{--  <img src="{{ @$order->courier_datail->company_logo }}" alt="{{ @$order->courier_datail->name }}">  --}}
                                    <img src="{{ URL::to($order->omsOrder->assignedCourier->company_logo) }}" alt="{{ $order->omsOrder->assignedCourier->name }}" width="45%">
                                    @else
                                      {{--  <b>Courier not selected at picklist.</b>  --}}
                                    @endif
                                  </td>
                              </tr>

                            </table>

                            @if($order->comment)
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="col-md-1 col-sm-1" style="padding-top: 12px;">
                                        <strong>Comment</strong>
                                    </div>
                                    <div class="col-md-11 col-sm-11 comment-box">
                                        <p>{{$order->comment}}</p>
                                    </div>

                                </div>
                            </div>
                            @endif
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Telephone</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="row_{{$order->order_id}}">
                                        <td>{{$order->order_id}}</td>
                                        <td>{{$order->firstname}} {{$order->lastname}}</td>
                                        <td>{{$order->mobile}}<br>
                                            {!! $whatsapp !!} {{$order->alternate_number}}
                                        </td>
                                        <td><span class="font-10 font-bold"> {{$order->currency_code}} </span> {{$order->total_amount}}</td>
                                    </tr>
                                    <tr class="row_{{$order->order_id}}">
                                        <td colspan="1">Total Product(s) = <span class="count">{{ $order->orderProducts->count() }}</span></td>
                                        <td colspan="1"><strong>Address: </strong><i>{{$order->shipping_city_area}}, {{$order->shipping_address_1}}, {{$order->shipping_street_building }}, {{$order->shipping_villa_flat }}</i></td>
                                        <td colspan="1"><strong>City: </strong><i>{{ $order->shipping_city ? $order->shipping_city : $order->shipping_zone }}</i></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="margin-0 padding-0" >
                                            <table class="table ">
                                              <tbody>
                                                @foreach ($order->orderProducts as $ordered_product )
                                                <tr class="order-products">
                                                    <td>
                                                        <img width="100" src="{{ URL::asset('uploads/inventory_products/'.$ordered_product->product->image)}}" />
                                                    </td>
                                                    <td>
                                                        {{ $ordered_product->name }}
                                                        <div class="m-t-5">
                                                            @if(  $ordered_product->product?->option_value > 0  )
                                                                    <strong>{{ $ordered_product->option_name }}</strong> : {{ $ordered_product->option_value }} ,
                                                            @endif
                                                            <strong>Color : </strong>{{ $ordered_product->product?->option_name }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $ordered_product->sku }}</td>
                                                    <td><span class="count">{{$ordered_product->quantity}}</span></td>
                                                    <td>{{$ordered_product->price}}</td>
                                                    <td>{{$ordered_product->total}}</td>
                                                </tr>
                                                @endforeach
                                              </tbody>
                                          </table>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            @if(!$loop->last)
                              <div class="page-break"></div>
                            @endif
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-success">
            Great ! No orders to generate picking list
        </div>
        @endif
    </div>
</div>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript">
    JsBarcode(".barcode").init();
</script>
@endsection
