<?php 
$baList = json_decode(session::get('ba_main_setting_list'));
$dfList = json_decode(session::get('df_main_setting_list'));
?>
<?php 
if(session('user_group_id') == 13 OR session('user_group_id') == 1 ) {
foreach($baList as $li) { 
    
$op_path = "employee-performance/designer/save-daily-work/".$li->id;
    ?>
<a href="{{route('employee-performance.designer.save-daily-work',[$li->id])}}" class="<?php if(Request::path() == $op_path){ ?> active <?php } ?>"><div class="tab-box">{{$li->title}}<small>-(BA)</small></div></a>
<?php } }?>

<?php 
if( session('user_group_id') == 14 OR session('user_group_id') == 3 OR session('user_group_id') == 1 ) {
foreach($dfList as $li) { 
    
$op_path = "employee-performance/designer/save-daily-work/".$li->id;
    ?>
<a href="{{route('employee-performance.designer.save-daily-work',[$li->id])}}" class="<?php if(Request::path() == $op_path){ ?> active <?php } ?>"><div class="tab-box">{{$li->title}}<small>-(DF)</small></div></a>
<?php } }?>
