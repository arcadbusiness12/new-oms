@if( $order->omsOrder?->oms_order_status != 5 )
<div class="stepper sw-main sw-theme-circles"
   data-options='{
   "theme":"sw-theme-circles",
   "transitionEffect":"fade",
   "toolbarSettings":{
   "showNextButton":false,
   "showPreviousButton":false
   }
   }'>
   <ul class="nav step-anchor">
      <li style="display: none"><a href=""></a></li>
      <li class="@if( $order->omsOrder?->oms_order_status == '0' || $order->omsOrder?->oms_order_status > 0 ) active @endif"><a href=""  class="circle"></a><center>Picklist</center></li>
      <li class="@if( $order->omsOrder?->oms_order_status > 0 ) active @endif"><a  class="circle"></a><center>Packed</center></li>
      <li class="@if( $order->omsOrder?->oms_order_status > 1 ) active @endif"><a  class="circle"></a><center>AWB</center></li>
      <li class="@if( $order->omsOrder?->oms_order_status > 2 ) active @endif" ><a  class="circle"></a><center>Shipped</center></li>
      <li class="@if( $order->omsOrder?->oms_order_status > 3 ) active @endif"><a  class="circle"></a><center>Deliver</center></li>
      <li class="@if( $order->payment_status == 1 ) active @endif"><a  class="circle"></a><center>Paid</center></li>
   </ul>
</div>
@else
  <i style="color: red" class="icon icon-cancel s-24"></i>
  <p>Cancelled</p>
@endif
