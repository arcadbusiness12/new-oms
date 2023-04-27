
    <style>
  .add-more-page {
    margin-top: 29px;
    float: right;
    margin-right: 41px;
    padding: 7px 10px;
}
.remove-page {
    margin-top: 29px;
    float: right;
    padding: 7px 10px;
    background-color: red !important;
    color: white;
    font-weight: bold;
}
    </style>
            <div class="sub-setting-loop col-md-12" >
                          <form name="setting_form" class="setting_form" class="" action="{{url('add/group')}}" method="post">
                              {{ csrf_field() }}
                              <input type="hidden" name="store" value="{{$store}}">
                              <div class="row setting-form-row">
                                <div class="col-sm-4">
                                  <input type="hidden" name="design_user" class="design_user">
                                  <label>Desining:</label>
                                  <select id="designing" name="designing" class="form-control designing">
                                    
                                    <option value="">Select User</option>
                                    @forelse($staff as $key => $row)
                                      <option value="{{ $row->user_id }}" {{ $row->user_id == $settings->designing_person ? "selected" : "" }} >{{ $row->firstname }}</option>
                                    @empty
                                    @endforelse
                                  </select>
                                </div>
                                <div class="col-sm-4">
                                  <input type="hidden" name="post_user" class="post_user">
                                  <label>Posting:</label>
                                  <select id="posting" name="posting" class="form-control posting">
                                    
                                    <option value="">Select User</option>
                                    @forelse($staff as $key => $row)
                                      <option value="{{ $row->user_id }}" {{ $row->user_id == $settings->posting_person ? "selected" : "" }}>{{ $row->firstname }}</option>
                                    @empty
                                    @endforelse
                                  </select>
                                </div>
                                <div class="col-sm-4">
                                  <label>Activities:</label>
                                  <select id="acitvity" name="acitvity" class="form-control">
                                    <option>Select Activity</option>
                                    @forelse($activities as $key => $row)
                                      <option value="{{ $row->id }}" {{ $row->id == $settings->duty_activity_id ? "selected" : "" }}>{{ $row->name }}</option>
                                    @empty
                                    @endforelse
                                  </select>
                                </div>
                              </div>
                              <div class="row setting-form-row" >
                                @if(count($settings->pages) > 0)
                                @foreach($settings->pages as $k => $page)
                                <div class="col-md-5">
                                  <div class="col-md-7" style="float: left;display: inline-block;">
                                    <label>Page</label>
                                    <input type="text" name="pages[]" placeholder="page" value="{{$page}}" class="form-control">
                                  </div>
                                  <div class="col-md-5" style="float: left;display: inline-block;">
                                    <label>Posting By</label>
                                    <select name="page_posting_by[]" class="form-control">
                                      @forelse($staff as $key => $row)
                                        <option value="{{ $row->user_id }}">{{ $row->firstname }}</option>
                                      @empty
                                      @endforelse
                                    </select>
                                  </div>
                                  
                                </div>
                                @if($k == 0)
                                <div class="col-md-1">
                                  
                                  <button type="button" id="add-more-page" class="btn btn-sm btn-success add-more-page"><i class="fa fa-plus-circle"></i> </button>
                                </div>
                                @endif
                                @endforeach
                                @else 
                                <div class="col-md-5" style="float: left;">
                                  <label>Page</label>
                                  <input type="text" name="pages[]" placeholder="page" class="form-control">
                                  
                                </div>
                                <div class="col-md-1">
                                  
                                  <button type="button" id="add-more-page" class="btn btn-sm btn-success add-more-page"><i class="fa fa-plus-circle"></i> </button>
                                </div>
                                @endif
                                
                                <div class="more-page"></div>
                                
                                
                              </div>
    
                              <div class="row setting-form-row">
                              @foreach($socials as $social)
                                <div class="col-md-2">
                                  <label class="social-lable"><input type="checkbox" name="social[]" value="{{$social->id}}" id="social-check{{$social->id}}" {{(in_array($social->id, $settings->social_ids)) ? 'checked' : ''}}>&nbsp; {{$social->name}}</label>  
                                </div>
                              @endforeach
                              </div>
                              <input name="postIng_type" type="hidden" value="1">
                              <input name="main_setting_id" type="hidden" value="{{$settings->id}}">
                              @if(count($settings->settingSchedules) > 0)
                              @foreach($settings->settingSchedules as $key => $setting)
                                <div class="row field-roww pt-4" id="srow_{{$setting->id}}">
                                <input name="setting[]" type="hidden" id="setting{{$setting->id}}" value="{{$setting->id}}">
                                <input name="time[]" type="hidden" id="time{{$setting->id}}" value="{{$setting->schedule_time}}">
                                  <div class="col-md-3">
                                    <!-- <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                    <input type="text" name="time[]" class="form-control" value="{{$setting->schedule_time}}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span> -->
                                    <div id="timepicker-spinbox{{$setting->id}}">
                                  </div>
                                </div>
                                    <div class="col-md-3 setting-input">
                                    @foreach($types_for_setting as $type)
                                          @if($setting->promotion_product_type_id == $type->id)
                                          <input type="hidden" id="selected_type{{$setting->id}}" value="{{$type->name}}">
                                          @endif
                                          @endforeach
                                    <select class="form-control product_change_status{{$setting->id}}" name="type[]" id="product_change_status" onchange="checkType('{{$setting->id}}')">
                                          <option value="">Select Type</option>
                                          @foreach($types_for_setting as $type)
                                          <option value="{{$type->id}}" {{($setting->promotion_product_type_id == $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 setting-input">
                                    <input type="hidden" id="selected_val{{$setting->id}}" value="{{$setting->category_id}}">
                                    <input type="hidden" name="category[]" id="category{{$setting->id}}" value="{{$setting->category_id}}">
                                    @if(count(explode(',', $setting->category)) > 1 || $setting->type->name == 'Promo Video' || $setting->type->name == 'New Arrival' || $setting->type->name == 'Clearance' || $setting->type->name == 'Season' || $setting->type->name == 'All')
                                    <div class="cate-multi{{$setting->id}}" style="min-width: 16%;"> 
                                    <select class="form-control multiselect{{$setting->id}} " onchange="cateVlue('{{$setting->id}}', 2)" multiple>
                                          <option value="">Category</option>
                                          @foreach($categories as $cate)
                                          <option value="{{$cate->id}}" @selected(in_array($cate->id, explode(',', $setting->category_id)))>{{$cate->name}}</option>
    
                                          <!-- <option value="{{$cate->name}}" {{($setting->category == $cate->name) ? 'selected' : ''}}>{{$cate->name}}</option> -->
                                          @endforeach
                                        </select>
                                    </div>
                                        <select class="form-control singleselect{{$setting->id}}" style="display:none!important;" onchange="cateVlue('{{$setting->id}}', 1)" data-live-search="true" placeholder="Select upto 5 tags">
                                          <option value="">Select Category</option> 
                                          @foreach($categories as $cate)
                                          <option value="{{$cate->id}}" >{{$cate->name}}</option>
                                          @endforeach
                                        </select>
                                        
                                    @else
    
                                    <div class="cate-multi{{$setting->id}} none{{$setting->id}}" style="min-width: 16%;">
                                    <select class="form-control multiselect{{$setting->id}} none{{$setting->id}}" onchange="cateVlue('{{$setting->id}}', 2)" title="Select Categories" multiple data-actions-box="true" data-live-search="true" placeholder="Select upto 5 tags">
                                          <!--<option value="">Select Category</option> -->
                                          @foreach($categories as $cate)
                                          <option value="{{$cate->id}}" @selected(in_array($cate->id, explode(',', $setting->category_id)))>{{$cate->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                        <select class="form-control singleselect{{$setting->id}}" onchange="cateVlue('{{$setting->id}}', 1)" >
                                          <option value="">Category</option>
                                          @foreach($categories as $cate)
                                          @php $ca = ($cate->category_name != null) ? $cate->category_name : $cate->name; @endphp
                                          <option value="{{$cate->id}}" {{($setting->category_id == $cate->id) ? 'selected' : ''}}>{{$cate->name}}</option>
    
                                          <!-- <option value="{{$cate->name}}" {{($setting->category == $cate->name) ? 'selected' : ''}}>{{$cate->name}}</option> -->
                                          @endforeach
                                        </select>
                                    @endif
                                    </div> 
                                    
                                    <div class="col-md-2 setting-input">
                                    <input type="hidden" name="is_active[]" id="is_active{{$setting->id}}1" value="{{$setting->is_active}}">
                                    <input class="form-check-input" <?php if($setting->is_active == 0){echo 'checked' ; } ?> type="checkbox" id="is_active_check{{$setting->id}}1" onchange="isActiveOrNot('{{$setting->id}}',1)"/>&nbsp;&nbsp; Is Active
                                    </div>
    
                                    <div class="col-md-2 setting-input">
                                      <button type="button" id="add-more" class="btn btn-sm btn-danger " onclick="deleteSetting('{{$setting->id}}')"><i class="fa fa-minus-circle"></i> </button>
                                    </div>  
                                  
                                </div>
                                @endforeach
                                @endif
                                <div class="form-rows"></div>
                                
                                <div class="row">
                                  <div class="col-md-12">
                                 <div class="col-md-2">
                                    
                                    
                                    {{-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> --}}
                                  </div> 
                                  <div class="col-md-6 error-msge-div">
                                  
                                    <span class="error-msge" ></span>
                                  </div>
                                  <div class="col-md-12 text-right">
                                    <button type="button" id="add-more" class="btn btn-sm btn-success add-more"><i class="fa fa-plus-circle"></i> </button>
                                    <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                  </div>
                                    </div>  
                                </div>
    
                                <div class="modal-footer">
                                <span class="text-right" id="error_mesge" style="color:red;">  </span>
                                <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                                <button type="submit" class="btn btn-info save-btn">Save</button>
                              </div>
                        </form>
                </div>
    
        
                <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
                <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
                
                <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
                <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
    <script type="text/javascript" class="code-js">
                var data = <?php echo $settings->settingSchedules ?>;
                console.log(data);
                data.forEach(function callback(value, index) {
                  var time = value.schedule_time.split(':');
                  var selector = '#timepicker-spinbox'+ value.id;
                  var tpSpinbox = new tui.TimePicker(selector, {
                    initialHour: time[0],
                    initialMinute: parseInt(time[1]),
                    // disabledHours: [1, 2, 13, 14],
                    inputType: 'spinbox',
                    name: 'time'
                });
                  tpSpinbox.on('change', (e) => {
                    $('#time'+value.id).val(e.hour+':'+e.minute)
                  });
                  console.log(value);
                  $('.multiselect'+value.id).select2();
                  $('.multiselectsc'+value.id).select2();
                  var cate = value.category_id.split(',');
                  if(value.sub_category_id) {
                    var scate = value.sub_category_id.split(',');
                  }else {
                    var scate = [];
                  }
                  console.log(cate.length);
                  if(value.type.name == 'All') {
                    $('.sub-cate-section'+value.id).removeClass('col-md-2');
                  }
                  if(cate.length > 1) {
                    $('.cate-section'+value.id).removeClass('col-md-2');
                  $('.sub-cete'+value.id).css('display', 'none');
                  // $('.budget'+value.id).css('display', 'none');
                  }else{
                    // $('.selectpickersc'+value.id).selectpicker('val', cate);
                  }
                  $('.none'+value.id).css('display', 'none');
    
                });
                
   $(document).ready(function(){
    $('.multiselect').select2();
  });

      function isActiveOrNot(index, param) {
        console.log('index ='+index+' param ='+param);
        // $('#is_custom'+index)
        // console.log($('#is_custom_check'+index).is(":checked"));
        var checked_value = $('#is_active_check'+index+param).is(":checked");
        if(checked_value) {
          $('#is_active'+index+param).val(0);
          console.log("Yes");
        }else {
          $('#is_active'+index+param).val(1);
          console.log("no");
        }
    }
    
      $(document).ready(function(){
          // $('.clockpicker').clockpicker();
        });
    function cateVlue(i,select) {
      if(select == 2) {
    
        var valuee = $('.multiselect'+i).val();
      }else {
        var valuee = $('.singleselect'+i).val();
      }
      $('#category'+i).val(valuee);
    }
    
    function multiSelector(index) {
      $('.multiselect'+index).attr('multiple', true);
        $('.cate-multi'+index).css('display', 'block');
      // $('.multiselect'+index).css('display', 'inline-block');
      $('.singleselect'+index).css('display', 'none');
      $('.sub-cete'+index).css('display','none');
      var selector = "select.singleselect"+index;
      $(selector+' option:selected').removeAttr('selected');
      $('.multiselect'+index).val(null).trigger('change');
      $('.multiselect'+index).select2({
        allowClear: true
      });
      // $('.selectpicker'+index).selectpicker("refresh");
    }
function checkType(index) {
  var selector = "select.product_change_status"+index;
  var type = $(selector+" option:selected").text();
  $('#category'+index).val('');
  if(type == 'New Arrival' || type == 'Clearance' || type == 'Promo Video' || type == 'Season' || type == 'All' || type == 'Best Sellers') {
    // $('.sub-cete'+index).css('display','none');
    // $('.budget'+index).css('display','none');
    $('.cate-section'+index).removeClass('col-md-2');
    multiSelector(index);
  }else {
    // $('.singleselect'+index).val(null).trigger('change');
    console.log('singleselect'+index);
    var se = $('.multiselect'+index);
    se.removeAttr('multiple');
    $('.singleselect'+index).css('display', 'inline-block');
    $('.cate-section'+index).addClass('col-md-2');
    $('.multiselect'+index).css('display', 'none');
    $('.cate-multi'+index).css('display', 'none');
    var selected = "select.selectpicker"+index;
    $('.multiselect'+index).select2({
        allowClear: true
    });
    // $('.multiselect'+index).select2('val', []);
    // control.selectpicker('val', []);
  }
  
}
      function loadTimePicker(index) {
        // $('.clockpicker').clockpicker();
        var selector = '#timepicker-spinbox-'+index;
        var tpSpinbox = new tui.TimePicker(selector, {
                    initialHour: 0,
                    initialMinute: 0,
                    // disabledHours: [1, 2, 13, 14],
                    inputType: 'spinbox'
                },$('#time'+index).val('12:0'));
                tpSpinbox.on('change', (e) => {
                $('#time'+index).val(e.hour+':'+e.minute)
              });
      }
    $(document).ready(function() {
      var max_fields = 12;
      var wrapper    = $('.form-rows');
      var add_btn    = $('.add-more');
      var setting = <?php echo $settings ?>;
      var x = 1;
      $(add_btn).click(function(e) {
        if(setting.length < 1) {
        $('.save-btn').css('display', 'block');
        $('#add-more').html('<i class="fa fa-plus-circle"></i> More');
        }
        e.preventDefault();
          x++;
          $(wrapper).append('<div class="row field-row pt-4 row_'+x+' appended" id="appended">' +'\n'+
                                '<div class="col-md-3"> '+'\n'+
                                '<input name="setting[]" type="hidden" id="setting'+x+'" value="">'+'\n'+
                                ' <input name="time[]" type="hidden" id="time'+x+'" value="">'+'\n'+
                                '<div id="timepicker-spinbox-'+x+'"></div>'+'\n'+
                                   '</div>' +'\n'+
                                   '<div class="col-md-3  setting-input"><select class="form-control product_change_status'+x+'" name="type[]" id="product_change_status" onchange="checkType('+x+')">' +'\n'+
                                  '<option value="">Select Type</option>' +'\n'+
                                  <?php foreach($types_for_setting as $type){?>
                                  '<option value="<?php echo $type->id; ?>"><?php echo $type->name ;?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>' +'\n'+
                            '<div class="col-md-2  setting-input"><input type="hidden" name="category[]" id="category'+x+'">' +'\n'+
                              '<div class="cate-multi'+x+'" style="min-width: 16%;">' +'\n'+
                              '<select class="form-control custom-select multiselect'+x+'" onchange="cateVlue('+x+', 2)" style="display: none;" multiple data-live-search="true" id="product_change_status">' +'\n'+
                                  
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>' +'\n'+
                                '<select class="form-control singleselect'+x+'" onchange="cateVlue('+x+', 1)" style=""  id="product_change_status">' +'\n'+
                                  '<option value="">Select Category</option>' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select>' +'\n'+
                                '</div>'+'\n'+
                                '<div class="col-md-2 setting-input">'+'\n'+
                                    '<input type="hidden" name="is_active[]" id="is_active'+x+'0" value="1">'+'\n'+
                                    '<input class="form-check-input" type="checkbox" id="is_active_check'+x+'0" onchange="isActiveOrNot('+x+',0)"/>&nbsp;&nbsp; Is Active'+'\n'+
                                    '</div>'+'\n'+
                                '<div class="col-md-2">'+'\n'+
                                '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-minus-circle"></i> </button>'+'\n'+
                                '</div>'+'\n'+
                            '</div>');
           loadTimePicker(x);
      $('.'+x).css('display', 'none');
      });
      $(wrapper).on('click','.btn-remove', function(e) {
        e.preventDefault();
        $(this).parent('div').parent('div').remove();
        x--;
        console.log($(".appended").length);
        if($(".appended").length <= 0 && setting < 1){
          $('.save-btn').css('display', 'none');
          $('#add-more').text('Add Setting');
        }
        // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
      })
    }) ;
    
    $(document).ready(function() {
      var max_fields = 12;
      var wrapper    = $('.more-page');
      var add_btn    = $('.add-more-page');
      var x = 1;
      $(add_btn).click(function(e) {
        
        e.preventDefault();
        if(x < max_fields) {
          
          $(wrapper).append('<div class="page"><div class="col-md-5 " style="float: left;"><label>Page</label><input type="text" name="pages[]" placeholder="page" class="form-control"></div>' +'\n'+
                                '<div class="col-md-1" style="float: left;"><button type="button" id="add-more-page" class="btn btn-sm remove-page">X </button></div></div>');
                            
      x++;
        }
      });
      $(wrapper).on('click','.remove-page', function(e) {
        e.preventDefault();
        $(this).parent('div').parent('div').remove();
        x--;
        console.log($(".appended").length);
      })
    }) ;
    
    $('.setting_form').submit(function(event) {
      event.preventDefault();
      $.ajax({
        url: "{{url('/productgroup/svae/promotion/setting')}}",
        type: "POST",
        data: $(this).serialize(),
        caches: false,
        beforeSend: function() {
          $('.save-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
          $('.save-btn').prop('disabled', true);
        },
        complete: function() {
          $('.save-btn').html('Save');
          $('.save-btn').prop('disabled', false);
        },
        error: function(error) {
          console.log(error.responseText);
          if(error.responseJSON.social_ids) {
            $('.error-msge').text('The Social template has already been taken.');
            setTimeout(() => {
            $('.error-msge').text('');
          },5000);
          }else if(error.responseJSON.social) {
            $('.error-msge').text('Please select at least one social.');
            setTimeout(() => {
            $('.error-msge').text('');
          },5000);
          }else if(error.responseText.indexOf('type') !== -1) {
            var str = JSON.stringify(error.responseText);
            $('.error-msge').text('Please select all fields of types.');
            setTimeout(() => {
            $('.error-msge').text('');
            },5000);
          }else if(error.responseText.indexOf('category') !== -1) {
            var str = JSON.stringify(error.responseText);
            $('.error-msge').text('Please select all fields of category.');
            setTimeout(() => {
            $('.error-msge').text('');
            },5000);
          }else if(error.responseText.indexOf('pages') !== -1) {
            var str = JSON.stringify(error.responseText);
            $('.error-msge').text('Page field is required, fill all fields.');
            setTimeout(() => {
            $('.error-msge').text('');
            },5000);
          }
        } 
      }).then(function(respo) {
        if(respo.status == 'exist') {
          $('.error-msge').text(respo.meassge);
            setTimeout(() => {
            $('.error-msge').text('');
          },5000);
        }else {
          $('.setting_view_modal').modal('toggle');
          $('.msg_success').text('Setting saved successfully.');
          $('.msg_success').css('display', 'block');
          
          $('.setting-body-data').html(respo);
          setTimeout(() => {
            $('.msg_success').css('display', 'none');
          },5000);
        }
        // console.log("ok return");
        // $('.setting_view_modal').modal('toggle');
        //   if(respo.status) {
        //     console.log(respo.meassge);
        //   $('.msg_success').text(respo.meassge);
        //   $('.msg_success').css('display', 'block');
        //   setTimeout(() => {
        //     $('.msg_success').css('display', 'none');
        //   },5000);
        // }
      })
    });
    
    $('.designing').on('change', function() {
      console.log($(this).val());
      $('.design_user').val($(this).val());
      var _token = $('meta[name="csrf-token"]').attr('content');
      var ids = [];
      ids.push($('.design_user').val());
      ids.push($('.post_user').val());
      $.ajax({
        url: "{{url('/user/activities')}}",
        type: "POST",
        data: {_token: _token, users: ids},
        caches: false,
        success: function(resp) {
          
        }
      });
    });
    $('.posting').on('change', function() {
      $('.post_user').val($(this).val());
      var _token = $('meta[name="csrf-token"]').attr('content');
      var ids = [];
      ids.push($('.design_user').val());
      ids.push($('.post_user').val());
      $.ajax({
        url: "{{url('/user/activities')}}",
        type: "POST",
        data: {_token: _token, users: ids},
        caches: false,
        success: function(resp) {
    
      }
      });
    
      });
    
    </script>