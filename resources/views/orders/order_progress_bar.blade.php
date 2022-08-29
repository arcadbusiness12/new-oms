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
      <li><a href=""  class="circle"><i class="icon-check"></i></a><center>Picklist</center></li>
      <li><a  class="circle"><i class="icon-check"></i></a><center>Packed</center></li>
      <li><a  class="circle"><i class="icon-check"></i></a><center>AWB</center></li>
      <li><a  class="circle"><i class="icon-check"></i></a><center>Shipped</center></li>
      <li><a  class="circle"><i class="icon-check"></i></a><center>Delivered</center></li>
      <li><a  class="circle"><i class="icon-check"></i></a><center>Paid</center></li>
   </ul>

</div>
