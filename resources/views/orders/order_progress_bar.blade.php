@php
$reship_data = App\Models\Oms\OmsOrdersModel::with(['airway_bills'=>function($query){
  $query->select('order_id','payment_status');
}])->where('order_id',$order['order_id'])->first();
if( !empty($reship_data) && $reship_data->reship==1 ){
    @endphp
    <span class="label label-warning" style="margin-left: 142px;">R</span>
    @php
    echo $label = '<span class="label label-warning" style="margin-left: 50px;">R</span>';
    // echo $label.$label.$label.$label;
}
@endphp
<div class="process-step">
    @if(isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] == 5)
    <div class="circle done undelivered">
        <span class="p-label">
            <i class="fa fa-close" aria-hidden="true"></i>
        </span>
        <span class="title">Cancelled</span>
    </div>
    @else
    <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 0 && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : '' }}">
        @if(isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 0  && $omsOrderStatus[$order['order_id']] <= 7)
        <span class="p-label">
            <i class="fa fa-check" aria-hidden="true"></i>
        </span>
        @else
        <span class="p-label">
        </span>
        @endif
        <span class="title">Picklist</span>
    </div>
    <span class="bar {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 1  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}"></span>
    <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 1  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}">
        @if(isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 1  && $omsOrderStatus[$order['order_id']] <= 7)
        <span class="p-label">
            <i class="fa fa-check" aria-hidden="true"></i>
        </span>
        @else
        <span class="p-label"></span>
        @endif
        <span class="title">Packed</span>
    </div>
    <span class="bar {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 2  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}"></span>
    <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 2  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}">
        @if(isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 2  && $omsOrderStatus[$order['order_id']] <= 7)
        <span class="p-label">
            <i class="fa fa-check" aria-hidden="true"></i>
        </span>
        @else
        <span class="p-label"></span>
        @endif
        <span class="title">AwbGen</span>
    </div>
    <span class="bar {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 3  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}"></span>
    <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 3  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}">
        @if(isset($omsOrderStatus[$order['order_id']]) && $omsOrderStatus[$order['order_id']] >= 3  && $omsOrderStatus[$order['order_id']] <= 7)
        <span class="p-label">
            <i class="fa fa-check" aria-hidden="true"></i>
        </span>
        @else
        <span class="p-label"></span>
        @endif
        <span class="title">Shipped</span>
    </div>
    @if($order['order_status_id'] == 9)
    <span class="bar undone"></span>
    <div class="circle undone">
        <span class="p-label">
            <i class="fa fa-reply" aria-hidden="true"></i>
        </span>
        <span class="title">Returned</span>
    </div>
    @else
        <span class="bar {{isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7)  && $omsOrderStatus[$order['order_id']] <= 7? 'done' : ''}}"></span>
        @if(isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7) && $omsOrderStatus[$order['order_id']] < 7)
        <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7)  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done' : ''}}">
            @if(isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7) && $omsOrderStatus[$order['order_id']] <= 7)
            <span class="p-label">
                <i class="fa fa-check" aria-hidden="true"></i>
            </span>
            @else
            <span class="p-label"></span>
            @endif
            <span class="title">Delivered</span>
        </div>
        @else
        <div class="circle {{isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7)  && $omsOrderStatus[$order['order_id']] <= 7 ? 'done undelivered' : ''}}">
            @if(isset($omsOrderStatus[$order['order_id']]) && ($omsOrderStatus[$order['order_id']] == 4 || $omsOrderStatus[$order['order_id']] == 7) && $omsOrderStatus[$order['order_id']] <= 7)
            <span class="p-label">
                <i class="fa fa-close" aria-hidden="true"></i>
            </span>
            @else
            <span class="p-label"></span>
            @endif
            <span class="title">Delivered</span>
        </div>
        @endif
        @endif
    @endif
    @if( @$omsOrderStatus[$order['order_id']] != 5 && stripos(Request::path(),'orders') === 0)
      <span class="bar {{ (@$reship_data->airway_bills[0]->payment_status == 1) ? 'done' : ''  }}"></span>
      <div class="circle {{ (@$reship_data->airway_bills[0]->payment_status == 1) ? 'done' : ''  }}">
          @if(@$reship_data->airway_bills[0]->payment_status == 1)
          <span class="p-label">
              <i class="fa fa-check" aria-hidden="true"></i>
          </span>
          @else
          <span class="p-label"></span>
          @endif
          <span class="title">Paid</span>
      </div>
    @endif
</div>