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
      foreach ($order_statuses as $key => $value) { 
        $blink = '';
        $h_class = 'h-class';
        if(count($order['status_history']) > 0) {
          $h_class = '';
        }
        if($value['order_status_id'] <= $order['order_status_id'] + 1) {  $active = 'active-next'; $blink = ''; } else {
          $active = ''; $blink = '';
        }
        if($value['order_status_id'] <= $order['order_status_id']) {$active = 'active'; $blink = '';}
        ?>
       
      <li class="<?php echo $active;?> {{$h_class}}">
        <a href=""  class="circle {{$blink}}">
          {{-- <i class="icon-check"></i> --}}
        </a>
       <span class="text-black"><?php echo $value['name']; ?></span>
       <?php if(isset($order['status_history'][$value['order_status_id']])){ ?>
        <small class="badge badge-secondary" style="font-size:9px">
          <b><?php echo $order['status_history'][$value['order_status_id']] ?></b>
        </small>
       <?php } ?>
        
      </li>
    <?php $i++; } ?>
   </ul>
</div>
@else
  <i style="color: red" class="icon icon-cancel s-24"></i>
  <p>Cancelled</p>
@endif
</div>


