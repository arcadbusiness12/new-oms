
                        <div class="body" id="print_able">
                           <table style="width:100%;">
                               <tr><td colspan="2" valign="center"><span class="pull-right"><b>{{ $data->bill_no }}</b></span><br></td></tr>
                                <tr>
                                    <td align="right"><img src="{{URL::asset('/assets/images/logo.jpg')}}" style="width:152px" /></td>
                                    <td align="right">
                                        <table class="table table-borderd" style="width:30%; border:1px dashed #0379c1">
                                            <tr>
                                                <td><b>Courier</b></td>
                                                <td>{{ $data->shippingProvider->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Charges/Delivery</b></td>
                                                <td>{{ $data->shippingProvider->shipping_charges }}</td>
                                            </tr>
                                        </table>
                                        {{-- <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{$order['order_id']}}" 
                                             jsbarcode-textmargin="0" jsbarcode-height="100"  ></svg> --}}
                                    </td>
                                </tr>

                            </table> 
                            <center>
                            <table class="table" style="width:50%;">
                                <thead>
                                    <tr>
                                        <th>SNO</th>
                                        <th>Order ID</th>
                                        <th>Order Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tot_prepaid = 0;
                                    @endphp
                                    @foreach ($data->ledgerDetails as $key => $order)
                                        <tr>
                                            <td>{{ ($key+1) }}</td>
                                            <td>{{$order->ref_id}} <small>{{ ( $order->is_prepaid ) ? 'Prepaid' : '' }}</small></td>
                                            <td>{{$order->amount}}</td>
                                        </tr>
                                        @php
                                            if( $order->is_prepaid ){
                                                $tot_prepaid += $order->amount;
                                            }
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <?php
                                        $total_quantity = count($data->ledgerDetails);
                                        $shipping_charges = $data->shippingProvider->shipping_charges;
                                        $total_shipping_charges = $data->shippingProvider->shipping_charges * $total_quantity;
                                        $grand_total = $data->total_amount - $total_shipping_charges - $tot_prepaid;
                                    ?>
                                    <tr>
                                        <td colspan="2" align="center"><b>Total</b></td>
                                        <td ><b>{{ $data->total_amount }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center"><b>Shipping Charges</b> {{ $total_quantity }} x {{ $shipping_charges }}</td>
                                        <td ><b>{{ $total_shipping_charges }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center"><b>Total Prepaid</b></td>
                                        <td ><b>{{ $tot_prepaid }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center"><b>Grand Total</b></td>
                                        <td ><b>{{ $grand_total }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center"><b>Amount Recieved</b></td>
                                        <td><b>{{ $data->paid_amount }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="center"><b>Balance</b></td>
                                        <td><b>{{  $data->balance_amount }}</b></td>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                            </center>
                            <table class="table" style="margin-top: 80px;">
                                <tr>
                                    <td>Courier</td>
                                    <td align="right">Stock Manager_______________</td>
                                </tr>
                            </table>
                            
                        </div>
                    
@push('scripts')
<script type="text/javascript">
    
    /*$('#frm_shipping_provider').on('submit',function(e){
        e.preventDefault();
        var form_data = $(this).serialize();
        console.log("form data"+form_data);
        $.ajax({
            url: APP_URL+"/orders/updateShippingPayment",
            type: 'POST',
            data: form_data,
            cache: false,
            dataType: 'json',
            success: function(data){
                console.log("sucess",data);
                printContent("print_able")
            },
            error: function(data){
                console.log("error",data);
            }
        }); 
    });*/
</script>
@endpush
