<style>
  
</style>
            <div class="sub-setting-loop col-md-12" >
                      <form name="change_schedule" class="change_schedule" class="" action="{{url('add/group')}}" method="post">
                          {{ csrf_field() }}
                          <input type="hidden" name="main_setting" id="main_setting" value="{{$main_setting}}">
                          <input type="hidden" name="setting" id="setting" value="{{$setting_id}}">
                          <input type="hidden" name="group_type" id="group_type" value="{{$group_type}}">
                          <input type="hidden" name="category" id="category" value="{{$category}}">
                          <input type="hidden" name="socials" id="socials" value="{{$socials}}">
                          <input type="hidden" name="date" id="@date" value="{{@$date}}">
                          <input type="hidden" name="last_date" id="@end" value="{{@$end}}">
                          <input type="hidden" name="time" id="time" value="{{$range}}">
                          <input type="hidden" name="store" id="store" value="{{$store}}">
                          <input type="hidden" name="post_type" id="post_type" value="{{$post_type}}">
                          <input type="hidden" name="budget" id="budget" value="{{$budget}}">
                          <input type="hidden" name="sub_category" id="sub_category" value="{{$sub_category}}">
                          <input type="hidden" name="row" id="row" value="{{@$row}}">
                          <input type="hidden" id="old_post_id" value="{{@$post_id}}">
                            <div class="row " id="">
                            <div class="col-md-2">
                            <!-- <div id="timepicker-selectbox"></div> -->
                            <label>{{$type}}  </label> {{$group_name}} sadasda
                            </div>
                            <div class="col-md-7">
                                <!-- <label>{{$category}}</label> -->
                                @foreach($cate as $c)
                                <div class="col-md-2">
                                   <input type="radio" id="{{$c}}" name="selected_category" value="{{$c}}" onchange="getgroupOfCategory('{{$group_type}}','{{$type}}','{{$c}}','{{$sub_category}}')" {{($group_name == $c) ? 'checked' : ''}}>
                                    <label for="{{$c}}">{{$c}}</label> 
                                </div>
                              @endforeach
                            </div>
                            <div class="col-md-3" id="select_div">
                            <select class="form-control schedule_group" searchable="Search here.." name="schedule_group" onchange="loadDetails(this.value)" data-actions-box="true" data-live-search="true" title="Select code"  data-live-search="true" placeholder="Select upto 5 tags" id="schedule_group">
                                      @foreach($collection as $group)
                                      <option value="{{$group->id}}" >{{$group->name}}</option>
                                      @endforeach
                                    </select>
                            </div>
                            </div>
                           
                              
                            <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="schedule_error text-center" id="schedule_error" style="color:red; font-weight: bold;margin-right: 245px"></span>
                            <button type="button" onclick="saveNewSchedule()" class="btn btn-info save-btn">Save</button>
                          </div>
                    </form>

                    
                      <div class="history-loader" style="display: none;">
                        <tr>
                          <td colspan="2">Please wait while history is loading..</td>
                        </tr>
                      </div>
                       <div id="body-data"></div>

             
            </div>
           
            <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
            
            <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
            
<script>
$(document).ready(function(){
console.log("OKkkkkkkkkkkkkkk");
console.log(<?php echo @$group_id ?>);
loadDetails(<?php echo @$group_id ?>);
$('.selectpicker').css('display', 'block');
// var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
// removeItemButton: true,
// maxItemCount:5,
// searchResultLimit:5,
// renderChoiceLimit:5
// });
$('.selectpicker').selectpicker();
$('.selectpicker').css('display', 'block');

});
function getgroupOfCategory(group_type, type, cate, sub_cate = null) {
  $.ajax({
    url: "{{url('get/group/for/selected/category/for/marketing')}}/"+group_type +'/'+ type +'/'+cate+'/'+sub_cate,
    type: 'GET',
    cache: false,
    success: function(respo) {
      console.log(respo);
      var html = '';
      if(respo.status) {
        
        html += '<option value="">Select sechedule</option>'
        respo.groups.forEach(function callback(value, index) {
          // html += '<option value="'+value.id+'" >'+value.name+'</option>';
        
          
          html += '<option value="'+value.id+'">'+value.name+'</option>'
       });
      }else {
        html += '<option value="">Not found..</option>'
      }
      console.log(html);
      // console.log($('#select_div').children('select'));
      // $('#select_div').html(html);
      $('.selectpicker').selectpicker('val', []);
      $('.schedule_group').html(html);
      $('.selectpicker').selectpicker();
      $('.selectpicker').selectpicker('refresh');
      // $('select.selectpicker').html(html);
    }
  })
}
function loadDetails(value) {
  $('#body-data').html('');
  if(value) {
    $('.history-loader').css('display', 'block');
    $.ajax({
      url: "{{url('get/schedule/group/detail')}}/"+value,
      type: 'GET',
      cache: false,
      success: function(resp) {
        $('.history-loader').css('display', 'none');
      $('#body-data').html(resp);
      }
    })
  }
}
function saveNewSchedule() {
  $.ajax({
    url: "{{url('/svae/change/schedule')}}",
    type: "POST",
    data: $('.change_schedule').serialize(),
    caches: false,
    beforeSend: function() {
      $('.save-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.save-btn').prop('disabled', true);
    },
    complete: function() {
      $('.save-btn').html('Save');
      $('.save-btn').prop('disabled', false);
    },
  }).then(function(respo) {
      console.log(respo.status);
    if(respo.status == true) {
      if($('#post_type').val() == 1) {
        $('.setting_view_modal').modal('toggle');
        $('#success').css('display', 'inline');
        $('#schedule_row_first_'+$('#post_id').val()).addClass('stop-schedule');
        if(respo.action == 'old') {
        $('#schedule_row_'+$('#post_id').val()).text(respo.code);
        }else {
          console.log($('#date').val());
          console.log($('#row').val());
          console.log("Yes New");
          $('#empty_schedule_row_'+$('#row').val()+$('#date').val()).text(respo.code);
        }
        
        setTimeout(() => {
          $('#success').css('display', 'none');
        }, 3500);
      $('#schedule_view_content').html();
      }else {
        location.reload();
        // $('.setting_view_modal').modal('toggle');
        // $('#success').css('display', 'inline');
        // $('#schedule_row_first_'+$('#post_id').val()).addClass('stop-schedule');
        // $('#schedule_row_'+$('#post_id').val()).text(respo.code);
        // setTimeout(() => {
        //   $('#success').css('display', 'none');
        // }, 3500);
      }
    }else if(respo.status == 'no_quantity') {
      $('#schedule_error').text(respo.mesge);
        $('#schedule_error').css('display', 'inline');
        setTimeout(() => {
        $('#schedule_error').css('display', 'none');
      }, 3500);
    }else {
        $('#schedule_error').text("The code "+respo.code+" already posted in "+respo.main_setting);
        $('#schedule_error').css('display', 'inline');
        setTimeout(() => {
        $('#schedule_error').css('display', 'none');
      }, 3500);
    }
  })
}



</script>