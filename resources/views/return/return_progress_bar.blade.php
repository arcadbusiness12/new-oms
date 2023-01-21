@php
    $progress_status = $order->oms_order_status;
@endphp
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
      <li class="@if( $progress_status > 1 ) active @endif"><a  class="circle"></a><center>AWB</center></li>
      <li class="@if( $progress_status > 3 ) active @endif"><a  class="circle"></a><center>Deliver</center></li>
   </ul>
</div>
