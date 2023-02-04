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
                            <form method="get" action="{{ route('accounts.payments') }}">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" class="date-time-picker form-control" placeholder="Date From" data-options='{
                                                    "timepicker":false,
                                                    "format":"Y-m-d"
                                                    }' value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_to" id="date_to" class="date-time-picker form-control" data-options='{
                                                    "timepicker":false,
                                                    "format":"Y-m-d"
                                                    }'  placeholder="Date To" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Reciept / Payments Vouchers
                        <a href="javascript:void()" class="btn btn-success btn-sm float-right active" data-toggle="modal" data-target="#paymentModal"> New Payment</a>

                        </div>

                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="body table-responsive">
                            <div class="col-sm-12">
                              <table class="table table-of-contents table-hover orders-table" style="border:1px solid #3379b7; align:center; margin-top:15px">
                                <thead>
                                    <tr>
                                        <td align="center"><b>Available Balance</b></td>
                                        <td><b id="available_bal">0</b></td>
                                    </tr>
                                </thead>
                              </table>
                            </div>
                            <div class="row" style="padding: 10px">
                                        <div class="col-sm-6">
                                        <h3>Receipt</h3>
                                        <table class="table table-of-contents table-hover orders-table" style="border:1px solid #3379b7">
                                            <thead style="background-color: #3379b7; color:white">
                                                <tr>
                                                    {{--  <td align="center">
                                                    <label style="height: 11px;"  for="md_checkbox_all"><input type="checkbox" id="md_checkbox_all" class="chk-col-green fwd-ordr-generate-awb-checkbox"  />
                                                        </label>
                                                    </td>  --}}
                                                    <td align="center"><b>Ref#</b></td>
                                                    <td align="center"><b>Courier</b></td>
                                                    <td align="center"><b>Amount</b></td>
                                                    <td align="center"><b>Created By</b></td>
                                                    <td align="center"><b>Created At</b></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $receipt_total = 0;
                                                @endphp
                                                @forelse($data_receipt as $receipt_row)
                                                    <tr class="row_{{$receipt_row->id}}" style="border-top: 1px solid #c1c1c1; ">
                                                        {{--  <td align="center">
                                                        <label style="height: 11px;"  for="md_checkbox_{{$row->id}}"><input name="generate-awb-chbx[]"  value="{{$row->id}}" type="checkbox" id="md_checkbox_{{$row->id}}" class="chk-col-green fwd-ordr-generate-awb-checkbox"  />
                                                            </label>
                                                        </td>  --}}

                                                        <td align="center">{{$receipt_row->bill_no}}</td>
                                                        <td align="center">{{$receipt_row->shippingProvider->name}}</td>
                                                        <td align="center">{{$receipt_row->paid_amount}}</td>
                                                        <td align="center">{{$receipt_row->user->firstname}} {{ $receipt_row->user->lastname }}</td>
                                                        <td align="center">{{date('d M Y H:i:s',strtotime($receipt_row->created_at))}}</td>
                                                    </tr>
                                                    @php
                                                        $receipt_total += $receipt_row->paid_amount;
                                                        $receipt_total  = $receipt_row->total_receipt;
                                                    @endphp
                                                @empty
                                                @endforelse
                                                <tr>
                                                <td colspan="2" align="right"><b>Total</b></td>
                                                <td align="center"><b>{{ $receipt_total }}</b></td>

                                                </tr>
                                                </tbody>
                                        </table>
                                        <div class="pull-right">
                                            {{ $data_receipt->render() }}
                                        </div>
                                        </div>

                                        <div class="col-sm-6">
                                        <h3>Payment</h3>
                                        <table class="table table-of-contents table-hover orders-table" style="border:1px solid #3379b7">
                                            <thead style="background-color: #3379b7; color:white">
                                                <tr>
                                                    {{--  <td align="center">
                                                    <label style="height: 11px;"  for="md_checkbox_all"><input type="checkbox" id="md_checkbox_all" class="chk-col-green fwd-ordr-generate-awb-checkbox"  />
                                                        </label>
                                                    </td>  --}}
                                                    <td align="center"><b>Description</b></td>
                                                    <td align="center"><b>Amount</b></td>
                                                    <td align="center"><b>Created By</b></td>
                                                    <td align="center"><b>Created At</b></td>
                                                    <td align="center"><b>Confirm</b></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $payment_total = 0;
                                                    $confirm_payment_total = 0;
                                                @endphp
                                                @forelse ($data_payment as $payment_row)
                                                    <tr class="row_{{$payment_row->id}}" style="border-top: 1px solid #c1c1c1;">
                                                        {{--  <td align="center">
                                                        <label style="height: 11px;"  for="md_checkbox_{{$row->id}}"><input name="generate-awb-chbx[]"  value="{{$row->id}}" type="checkbox" id="md_checkbox_{{$row->id}}" class="chk-col-green fwd-ordr-generate-awb-checkbox"  />
                                                            </label>
                                                        </td>  --}}

                                                        <td align="center">{{$payment_row->description}}</td>
                                                        <td align="center">{{$payment_row->total_amount}}</td>
                                                        <td align="center">{{$payment_row->user->firstname}} {{ $payment_row->user->lastname }}</td>
                                                        <td align="center">{{date('d M Y H:i:s',strtotime($payment_row->created_at))}}</td>
                                                        {{--  <td align="center"><input type="checkbox" value="{{ $payment_row->id }}" name="confirm_payment[]" onchange="confirmPayment({{ $payment_row->id }})"></td>  --}}
                                                        <td align="center">
                                                        @if( session('user_group_id') == 1 )
                                                            @if( $payment_row->confirm_payment == 1 )
                                                            <i style="color:green">Approved</i>
                                                            @else
                                                            <input type="checkbox" value="{{ $payment_row->id }}" name="confirm_payment[]" onchange="confirmPayment({{ $payment_row->id }})" {{ $payment_row->confirm_payment == 1 ? 'checked' : '' }}>
                                                            @endif
                                                        @else
                                                            @if( $payment_row->confirm_payment == 1 )
                                                            <i class="fa fa-check-circle green success-fs" title="Confirmed"></i>
                                                            @endif
                                                        @endif

                                                        </td>
                                                    </tr>
                                                    @php
                                                        $payment_total += $payment_row->total_amount;
                                                        if($payment_row->confirm_payment == 1){
                                                        $confirm_payment_total += $payment_row->total_amount;
                                                        }
                                                        $payment_total         = $payment_row->total_payments;
                                                        $confirm_payment_total = $payment_row->total__confirm_payments;
                                                    @endphp
                                                @empty
                                                @endforelse
                                                <tr>
                                                <td colspan="1" align="right"><b>Total</b></td>
                                                <td align="center"><b>{{ $payment_total }}</b></td>
                                                <td align="center"><b></b></td>
                                                {{--  <td align="right"><input type="submit" value="Confirm" name="confirm-payment" class="btn btn-sm btn-success"></td>  --}}
                                                </tr>
                                                </tbody>

                                        </table>
                                        <div class="pull-right">
                                            {{ $data_payment->render() }}
                                          </div>
                                        </div>
                                    </div>
                              </div>
                          </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('accounts.popup_new_payment')

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
    var total_receipt           = parseInt({{ $receipt_total }});
    var total_payment           = parseInt({{ $payment_total }});
    var total_payment_confirm  = parseInt({{ $confirm_payment_total }});
    var balance_amount = (total_receipt-total_payment_confirm);
    $('#available_bal').html(balance_amount);
    function confirmPayment(payment_id){
        $.ajax({
          method: "POST",
          url: APP_URL + "/accounts/payments",
          data: {ledger_id:payment_id},
          //dataType: 'json',
          cache: false,
          headers:
          {
              'X-CSRF-Token': $('input[name="_token"]').val()
          },
      }).done(function (data)
      {
        //alert(data);
        if(data == 1){
          window.location.href = window.location.href;
        }
      }); // End of Ajax
    }
</script>
@endpush
