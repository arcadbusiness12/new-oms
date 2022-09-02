<div class="order_progress_bar_new">

@if($order['order_status_id'] != 7)
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
      <?php array_pop($order_statuses);
      $i = 1;
      foreach ($order_statuses as $key => $value) { ?>

      <li class="<?php if($value['order_status_id'] <= $order['order_status_id'] + 1) { ?> active <?php }?>">
        <a href=""  class="circle">
          {{-- <i class="icon-check"></i> --}}
        </a>
       <span class="text-black"><?php echo $value['name']; ?> </span>
      </li>
    <?php $i++; } ?>
   </ul>
</div>
@else
  <i style="color: red" class="icon icon-cancel s-24"></i>
  <p>Cancelled</p>
@endif
</div>


