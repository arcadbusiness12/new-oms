@forelse($all_lists as $key => $list)
  @php
  $op_path = "employee-performance/designer/save-daily-work/".$list->id;
  if( $list->store_id == 1 ){
    $store = "BA";
  }else{
    $store = "DF";
  }
  if($list->posting_type == 1){
    $post_type = "Organic";
  }else if( $list->posting_type == 2 ){
    $post_type = "Paid";
  }
  @endphp

  <a href="{{route('employee-performance.designer.save-daily-work',[$list->id])}}" class="<?php if(Request::path() == $op_path){ ?> active <?php } ?>"><div class="tab-box">{{$list->title}}<br><small>({{ $store}} - {{ $post_type }})</small></div></a>
@empty
  
@endforelse
