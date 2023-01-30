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
                            <form method="get" action="{{ URL::to('accounts/pending/receipts') }}">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <select class="form-control"  name="search_by_courier">
                                                    <option value="">--Courier--</option>
                                                    @forelse($shipping_data as $key => $val)
                                                        <option value="{{ $val->shipping_provider_id }}"  {{ @$old_input['search_by_courier'] == $val->shipping_provider_id ? "selected" : "" }}>{{ $val->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" value="{{ @$old_input['date_from'] }}" class="date-time-picker form-control" data-options='{
                                                    "timepicker":false,
                                                    "format":"Y-m-d"
                                                    }' autocomplete="off" placeholder="Date From" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_to" id="date_to" class="date-time-picker form-control" value="{{ @$old_input['date_to'] }}" autocomplete="off" data-options='{
                                                    "timepicker":false,
                                                    "format":"Y-m-d"
                                                    }' placeholder="Date To" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                      <div class="form-group form-float">
                                          <div class="form-line">
                                              <select class="form-control"  name="by_store">
                                                  <option value="">-Select Store-</option>
                                                  <option value="1" {{ (@$old_input['by_store']==1) ? "selected" : "" }}>Business Arcade</option>
                                                  <option value="2" {{ (@$old_input['by_store']==2) ? "selected" : "" }}>Dress Fair</option>
                                              </select>
                                          </div>
                                      </div>
                                    </div>
                                    <div class="col-sm-2">
                                      <div class="form-group form-float">
                                          <div class="form-line">
                                              <select class="form-control"  name="courier_delivered">
                                                  <option value="">--Courier Delivered--</option>
                                                  <option value="1" {{ (@$old_input['courier_delivered']==1) ? "selected" : "" }}>Yes</option>
                                                  <option value="0" {{ (@$old_input['courier_delivered']=="0") ? "selected" : "" }}>No</option>
                                              </select>
                                          </div>
                                      </div>
                                    </div>
                                    <div class="col-sm-2">
                                      <div class="form-group form-float">
                                          <div class="form-line">
                                              <select class="form-control"  name="oms_delivered">
                                                  <option value="">--OMS Delivered--</option>
                                                  <option value="4" {{ (@$old_input['oms_delivered']==4) ? "selected" : "" }}>Yes</option>
                                                  <option value="0" {{ (@$old_input['oms_delivered']=="0") ? "selected" : "" }}>No</option>
                                              </select>
                                          </div>
                                      </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <select class="form-control"  name="payment">
                                                    <option value="">--Payment Status--</option>
                                                    <option value="1" {{ (@$old_input['payment']==1) ? "selected" : "" }}>Paid</option>
                                                    <option value="0" {{ (@$old_input['payment']=="0") ? "selected" : "" }}>Un-Paid</option>
                                                </select>
                                            </div>
                                        </div>
                                      </div>
                                       <div class="col-sm-2">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <select class="form-control"  name="payment_type">
                                                    <option value="">--Payment Type--</option>
                                                    <option value="cod_order_fee,cod" {{ (@$old_input['payment_type'] == 'cod_order_fee,cod' ) ? "selected" : "" }}>COD</option>
                                                    <option value="ccavenuepay" {{ (@$old_input['payment_type']== 'ccavenuepay' ) ? "selected" : "" }}>PrePaid</option>
                                                </select>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-2">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <select class="form-control"  name="order_type">
                                                    <option value="">--Order Type--</option>
                                                    <option value="1" {{ (@$old_input['order_type'] == 1 ) ? "selected" : "" }}>Order</option>
                                                    <option value="2" {{ (@$old_input['order_type']==  2 ) ? "selected" : "" }}>Exchange</option>
                                                </select>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="order_number" class="form-control" value="{{ @$old_input['order_number'] }}"  placeholder="Order number">
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="airway_bill_number" class="form-control" value="{{ @$old_input['airway_bill_number'] }}"  placeholder="AirwayBill number">
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="order_amount" class="form-control" value="{{ @$old_input['order_amount'] }}"  placeholder="Amount">
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <label for="only_shipped"><input type="checkbox" name="only_shipped" id="only_shipped" {{ @$old_input['only_shipped'] == 'on' ? 'checked' : '' }}> Shipped</label>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-1">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <button type="submit" id="search_filter" class="btn btn-sm btn-primary active pull-right"><i class="fa fa-filter"></i> Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <button type="submit" id="search_filter" name="export" class="btn btn-sm btn-warning active" value="Yes">Export</button>
                                                </div>
                                            </div>
                                        </div>
                                      @if( @$old_input['only_shipped'] == 'on' && @$old_input['search_by_courier'] > 0 )
                                      <div class="col-sm-1">
                                        <button type="submit" name="courier_print_submit" value="courier_print_submit" class="btn btn-success active"><i class="fa fa-print"></i> Courier Print</button>
                                      </div>
                                      @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Pending Reciept Vouchers</div>

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
                                <th scope="col">Order Id</th>
                                <th scope="col"><center>AWB No</center></th>
                                <th scope="col"><center>Courier</center></th>
                                <th scope="col"><center>Amount</center></th>
                                <th scope="col"><center>Courier Delivered</center></th>
                                <th scope="col"><center>OMS Delivered</center></th>
                                <th scope="col"><center>Payment Recieved</center></th>
                                <th scope="col"><center>Created At</center></th>
                               </tr>
                             </thead>
                             <tbody>
                                @if($data->count())
                                @foreach($data as $key=>$row)
                                    <tr class="row_{{ $row->order_id }}" style="border-bottom: 1px solid lightgray !important">
                                        <td class="col-sm-1">{{ ($key+1) }}</td>
                                        <td class="col-sm-1">{{ $row->order_id }} {{ $row->order_type == 2 ? "-1" : "" }}</td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->airway_bill_number }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->courier_name }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ number_format($row->total_amount,2) }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{!! $row->courier_delivered == 1 ? "<strong style='color:green'>Yes" : "<strong style='color:red'>No</strong>" !!} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{!! $row->oms_order_status == 1 ? "<strong style='color:green'>Yes" : "<strong style='color:red'>No</strong>" !!}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{!! $row->payment_status == 1 ? "<strong style='color:green'>Yes" : "<strong style='color:red'>No</strong>" !!}</center></td>
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
