<table class="table table-of-contents table-hover orders-table" style="border:1px solid #3379b7">
    <thead style="background-color: #3379b7; color:white">
        <tr>
            <td align="center">SN</td>
            <td align="center"><b>Order Id</b></td>
            <td align="center"><b>AWB No</b></td>
            <td align="center"><b>Amount</b></td>
            <td align="center"><b>Courier Delivered</b></td>
            <td align="center"><b>OMS Delivered</b></td>
            <td align="center"><b>Payment Recieved</b></td>
            <td align="center"><b>Created at</b></td>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach ($data as $key => $row)
            @php
                $prepaid_tag = "";
                if( $row->payment_code != "cod_order_fee" && $row->payment_code != "cod" && $row->payment_code != ""){
                  $prepaid_tag = "<small class='btn-success'>Prepaid</small>";
                }
            @endphp
            <input type="hidden" name="pending_order_ids[]" value="{{ $row->airway_bill_number }}">
            <input type="hidden" name="order_ids[{{ $row->airway_bill_number }}]" value="{{ $row->order_id }}">
            <input type="hidden" name="store_id[{{ $row->airway_bill_number }}]" value="{{ $row->store }}">
            <input type="hidden" name="order_type[{{ $row->airway_bill_number }}]" value="{{ $row->order_type }}">
            <input type="hidden" name="payment_code[{{$row->airway_bill_number}}]" value="{{ $row->payment_code }}">
            <input type="hidden" name="amount[{{ $row->airway_bill_number }}]" value="{{ $row->amount }}">
            <tr class="row_{{$row->order_id}}" style="border-top: 1px solid #c1c1c1; ">
                <td align="center">{{ $key+1 }}</td>
                <td align="center">{{$row->order_id }}{{ $row->order_type == 2 ? "-1" : '' }}</td>
                <td align="center">{{ $row->airway_bill_number }} {!! $prepaid_tag !!}</td>
                <td align="center">{{ number_format($row->amount,2) }}</td>
                <td align="center">{!! ($row->courier_delivered==1 ? 'Yes' : 'No' )!!}</td>
                <td align="center">{!! ($row->oms_order_status==4 ? 'Yes' : 'No' )!!}</td>
                <td align="center">{!! ($row->payment_status==1 ? 'Paid' : 'Un-Paid' )!!}</td>
                <td align="center">{{date('d M Y',strtotime($row->created_at))}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
