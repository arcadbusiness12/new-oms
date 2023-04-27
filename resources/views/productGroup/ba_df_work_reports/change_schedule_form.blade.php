<style>
  
</style>
            <div class="sub-setting-loop col-md-12" >
                      <form name="change_schedule" class="change_schedule" class="" action="{{url('add/group')}}" method="post">
                          {{ csrf_field() }}
                          <input type="hidden" name="post_id" id="post_id" value="{{$post_id}}">
                          <input type="hidden" name="main_setting" id="main_setting" value="{{$main_setting}}">
                          <input type="hidden" name="setting" id="setting" value="{{$setting_id}}">
                          <input type="hidden" name="group_type" id="group_type" value="{{$group_type}}">
                          <input type="hidden" name="category" id="category" value="{{$category}}">
                          <input type="hidden" name="socials" id="socials" value="{{$socials}}">
                          <input type="hidden" name="date" id="date" value="{{$date}}">
                          <input type="hidden" name="last_date" id="@last_date" value="{{@$last_date}}">
                          <input type="hidden" name="time" id="time" value="{{$time}}">
                          <input type="hidden" name="store" id="store" value="{{$store}}">
                          <input type="hidden" name="post_type" id="post_type" value="{{$post_type}}">
                          <input type="hidden" name="row" id="row" value="{{@$row}}">
                          <input type="hidden" name="action" id="action" value="{{@$action}}">
                          <input type="hidden" name="range" id="range" value="{{@$range}}">
                          <input type="hidden" id="type" value="{{$type}}">
                          <input type="hidden" id="sub_category" value="{{$sub_category}}">
                          <input type="hidden" name="old_group_code" id="group_code" value="{{$group_code}}">
                            <div class="row " id="">
                            @if($post_type == 1)
                            <input type="hidden" id="action_type" value="1">
                            @php $cate_col = 7; $group_col = 3; @endphp
                            @else
                            <input type="hidden" id="action_type" value="2">
                            @php $cate_col = 4; $group_col = 6; @endphp
                            @endif
                            <div class="col-md-2">
                            <!-- <div id="timepicker-selectbox"></div> -->
                            <label>{{$type}}</label> 
                            </div>
                            
                            <div class="col-md-{{$cate_col}}">
                                <!-- <label>{{$category}}</label> -->
                                @foreach($cate as $c)
                                <div class="col-md-4">
                                   <input type="radio" id="{{$c}}" name="selected_category" value="{{$c}}" onchange="getgroupOfCategory('{{$group_type}}','{{$type}}','{{$c}}')" {{($group_name == $c) ? 'checked' : ''}}>
                                    <label for="{{$c}}">{{$c}}</label> 
                                </div>
                              @endforeach
                            </div>
                            <div class="col-md-{{$group_col}}" id="select_div">
                            @if($post_type == 1)
                            <select class="form-control selectpicker" searchable="Search here.." name="schedule_group" onchange="loadDetails(this.value)" data-actions-box="true" data-live-search="true" title="Select code"  data-live-search="true" placeholder="Select upto 5 tags" id="schedule_group">
                                      @foreach($collection as $group)
                                      <option value="{{$group->id}}" >{{$group->name}}</option>
                                      @endforeach
                                    </select>

                              @else 
                              <input list="browsers" placeholder="Search Code.." class="form-control" autocomplete="off" onkeyup="searchCode(this.value)" onchange="getSelectedGroupId(this.value)" id="dataList"> 
                            <datalist id="browsers" class="dataList">
                            </datalist>
                            <input type="hidden" name="schedule_group" id="selected_group_id">
                              @endif
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
                       <div id="body-data" class="body-data"></div>

             
            </div>
           
            <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
            
            <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
            
<script>
$(document).ready(function(){
console.log("YEs");
// console.log($('#old_post_id').val());
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
function getgroupOfCategory(group_type, type, cate) {
  $('#dataList').val('');
  var option_html = '';
  $.ajax({
    url: "{{url('get/group/for/selected/category')}}/"+group_type +'/'+ type +'/'+cate,
    type: 'GET',
    cache: false,
    success: function(respo) {
      console.log("ok")
      var html = '';
      if(respo.status) {
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
      $('.selectpicker').html(html);
      $('.selectpicker').selectpicker();
      $('.selectpicker').selectpicker('refresh');
      // $('select.selectpicker').html(html);
    }
  })
}
function loadDetails(value) {
  // console.log("Yes Here");
    $('.body-data').html('');
  if(value) {
    $('.history-loader').css('display', 'block');
    $.ajax({
      url: "{{url('get/schedule/group/detail')}}/"+value,
      type: 'GET',
      cache: false,
      success: function(resp) {
        console.log(resp);
        $('.history-loader').css('display', 'none');
      $('.body-data').html(resp);
      }
    })
  }
}
function searchCode(value) {
  var cate = $('#category').val();
  var type = $('#type').val();
  var group_type = $('#group_type').val();
  var sub_category = $('#sub_category').val();
  if(value) {
    $.ajax({
      url: "{{url('search/group/code')}}/"+value+ "/" + cate + "/" + group_type + "/" + type+ "/" + sub_category,
      type: 'GET',
      cache: false,
      success: function(resp) {
        // console.log(resp);
        var option_html  = '';
        var option_data = [];
        resp.forEach(function callback(v, k) {
          console.log(v.name);
          option_data.push(v.name);
          // option_html += '<option value="'+v.id+'">'+v.name+'</option>'
        });
        console.log(option_data);
        $( "#dataList" ).autocomplete({
        source: option_data
      });
        // $('.dataList').html(option_html);
      //   $('.history-loader').css('display', 'none');
      // $('.body-data').html(resp);
      }
    })
  }
}
function saveNewSchedule() {
  var action_type = $('#action_type').val();
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
    console.log("sdiodf alsdjkl;a");
    if(respo.status == true) {
      if($('#post_type').val() == 1) {
        console.log("If"); 
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
      
      location.reload();
      }else {
        // console.log("Else "+action_type);
        // if(action_type == 2) {
        //   if($('#action').val() == 'current') {
        //     $('.code-'+respo.old_code).html(respo.code);
        //     $('.current-'+respo.row+respo.id).prepend(respo.old_code);
        //   }else {
        //     $('.code-next-'+respo.old_code).html(respo.code);
        //   }

          
        //   $('.puased-code-'+respo.old_code).html(respo.old_code);
        //   $('#setting_view_modal').modal('toggle');
        //   $(document).find('#setting_view_modal').on('hidden.bs.modal', function() {
        //   $('body').addClass('modal-open');
        //  });
        // }else {
          location.reload();
        // }
        
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
        $('#schedule_error').text("The code "+respo.code+" already posted in "+respo.exist_date);
        $('#schedule_error').css('display', 'inline');
        setTimeout(() => {
        $('#schedule_error').css('display', 'none');
      }, 3500);
    }
  })
}

function searchCode(value) {
  var cate = $('#category').val();
  var type = $('#type').val();
  var group_type = $('#group_type').val();
  var sub_category = $('#sub_category').val();
  var selected_cate = $("input[type='radio'][name='selected_category']:checked").val();
  if(value && selected_cate) {
    $.ajax({
      url: "{{url('search/group/code')}}/"+value+ "/" + cate +"/"+ selected_cate + "/" + group_type + "/" + type+ "/" + sub_category,
      type: 'GET',
      cache: false,
      success: function(resp) {
        var option_html  = '';
        var option_data = [];
        resp.forEach(function callback(v, k) {
          option_html += '<option value="'+v.name+'">'+v.name+'</option>'
        });
        $('#browsers').html(option_html);
      }
    })
  }else{
    $('.schedule_error').text('Please select category and search category wise.');
    setTimeout(() => {
      $('.schedule_error').text('');
    },3500);
  }
}

function getSelectedGroupId(value) {
  if(value) {
    $.ajax({
      url: "{{url('get/searched/group/code/id')}}/"+value,
      type: 'GET',
      cache: false,
      success: function(resp) {
        if(resp.status) {
          $('#selected_group_id').val(resp.id);
          loadDetails(resp.id);
        }
      }
    });
  }
}

</script>