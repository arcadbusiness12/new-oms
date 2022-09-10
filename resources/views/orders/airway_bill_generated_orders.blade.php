@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 col-grid">
            <div class="card text-black">
              <form method="post" action="{{ URL::to('/orders/print/awb') }}" target="_blank" id="form_awb_bills">
                @csrf
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-6 pt-2">
                            Airwaybill Generated Orders
                        </div>
                        <div class="col-sm-6">
                            <button name="submit" value="awb" class="btn btn-sm btn-success float-right">Print AWB</button>
                        </div>
                    </div>
                </div>
                 <div class="body table-responsive">
                    <table class="table">
                        <thead>
                            <tr style="background-color: #3f51b5;color:white">
                                <th>
                                  <label style="height: 11px;"  for="md_checkbox_all"> <input type="checkbox" id="md_checkbox_all" class="chk-col-green"  /> </label>
                                </th>
                                <th>Order ID</th>
                                <th>Order Status</th>
                                <th>Last Shipped With</th>
                                <th>Airway Bill History</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rowCount = 1; ?>
                            @foreach ($omsOrders as $order)
                            <tr>
                                <td>
                                  <label style="height: 11px;"  for="md_checkbox_{{$order['order_id']}}"><input name="order_id[]" type="checkbox" id="md_checkbox_{{$order['order_id']}}" class="chk-col-green" value="{{$order['order_id']}}"  /></label>
                                </td>
                                <td scope="row">
                                    <a href="{{URL::to('/orders')}}?order_id={{$order['order_id']}}" target="_blank"> {{$order['order_id']}} </a>
                                </td>
                                <td>{{$order['status']}}</td>
                                <td>{{ ($order['shipping_provider']) ? $order['shipping_provider']['name'] : "" }}</td>
                                <td>
                                    @if (count($order['airway_bills']) > 0)
                                    <table class="table">
                                        <?php
                                        $count = 1;
                                        ?>

                                        <tr>
                                            <th>Awb #</th>
                                            <th>Date</th>
                                            <th>Shipping Provider</th>
                                            <th>Download</th>
                                        </tr>


                                        @foreach ($order['airway_bills'] as $awb)
                                        <?php
                                        $shippingCompany = "App\\Platform\\ShippingProviders\\" . $awb['shipping_provider']['name'];
                                        $class = new $shippingCompany();
                                        if( $awb['pdf_print_link'] != "" ){
                                          $airway_bill_url = $awb['pdf_print_link'];
                                        }else{
                                          $airway_bill_url = $class->getAirwayBillUrl($awb['airway_bill_number'])."?awb=".$awb['airway_bill_number'];
                                        }
                                        ?>
                                        <tr>
                                            <td>

                                                <input @if ( $count ==1 ) checked="checked" @endif id="{{$awb['airway_bill_number']}}" type="radio" name="{{$order['order_id']}}" value="{{$awb['airway_bill_number']}}"  />
                                                        <label for="{{$awb['airway_bill_number']}}">{{$awb['airway_bill_number']}}</label>
                                                        <?php $count++; ?>

                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$awb['created_at'])->toFormattedDateString()}}</td>
                                            <td>{{$awb['shipping_provider']['name']}}</td>
                                            <td>
                                                <a target="_blank" href="{{ $airway_bill_url }}" data-toggle="tooltip" data-placement="top" data-original-title="Download AirwayBill">
                                                   <center> <i class="icon icon-download"></i></center>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                    @else
                                    <div class="text-red">
                                        No History Found.
                                    </div>
                                    @endif
                                </td>
                                <td>
                                </td>


                            </tr>


                            @endforeach
                        </tbody>
                    </table>
                </div>
              </form>
            </div> {{--card end--}}
        </div>
    </div>
    {{  $omsOrders->render() }}

</div>
@endsection
