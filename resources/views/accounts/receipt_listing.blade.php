@extends('layouts.app')

@section('content')
<style>
    .sw-theme-circles>ul.step-anchor:before {
        //top: 36%!important;
        width: 68%!important;
        margin-left: 39px!important;
    }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <p>Filters</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="row action_button_row" style="display:none">
                            <div class="col-sm-2 exchange_action" style="display:none">
                                <button class="btn btn-info active" id="btn_exchange">Exchange</button>
                            </div>
                            {{--  <div class="col-sm-2 reship_action" style="display:none">
                                <button class="btn btn-warning active" id="btn_reship">Reship</button>
                            </div>  --}}
                        </div>
                        <div class="panel-heading">Reciept Vouchers</div>

                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 14px !important; color:black !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                <th scope="col">&nbsp;</th>
                                <th scope="col">Ref No</th>
                                <th scope="col"><center>Courier</center></th>
                                <th scope="col"><center>Shipments</center></th>
                                <th scope="col"><center>Total Amount</center></th>
                                <th scope="col"><center>Recieved Amount</center></th>
                                <th scope="col"><center>Balance</center></th>
                                <th scope="col"><center>Created By</center></th>
                                <th scope="col"><center>Created At</center></th>
                               </tr>
                             </thead>
                             <tbody>
                                @if($data->count())
                                @foreach($data as $key=>$row)
                                    <tr class="row_{{ $row->order_id }}">
                                        <td class="col-sm-1"><input type="checkbox" class="order_checkbox" value="{{ $row->id }}" /></td>
                                        <td class="col-sm-1"><a href="javascript:void()" data-id="{{$row->id}}" id="order_history" data-toggle="modal" data-target="#receiptModal">{{ $row->bill_no }}</a></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->shippingProvider->name }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ count($row->ledgerDetails) }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->total_amount }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->paid_amount }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->balance_amount  }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->user->firstname }} {{ $row->user->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->created_at }} </center></td>
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
@include('accounts.popup_receipt_details')

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
    $(document).on('click','#order_history',function(event){
        //console.log(event);
        console.log($(this).attr('data-id'));
        var order_id = $(this).attr('data-id');
        $.ajax({
            method: "POST",
            url: APP_URL + "/accounts/get/receipt/popup",
            data: {id:order_id},
            //dataType: 'json',
            cache: false,
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            },
        }).done(function (data)
        {
            //alert(data);
            $('#receiptModal_content').html(data);
        }); // End of Ajax
    });
    function printContent(el){
        var restorepage = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent).css('padding-top','0px');
        window.print();
        $('body').html(restorepage);
        window.location.href=window.location.href;
    }
</script>
@endpush
