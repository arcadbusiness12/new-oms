<style>
  
</style>
            <div class="sub-setting-loop col-md-12" >
                      <form name="setting_form" class="setting_form" class="" action="{{url('add/group')}}" method="post">
                          {{ csrf_field() }}

                          <div class="row setting-form-row">
                            <div class="col-sm-12">
                              <label>Activities:</label>
                              <select id="acitvity" name="acitvity" class="form-control">
                                <option>Select Activity</option>
                                @forelse($activities as $key => $row)
                                  <option value="{{ $row->id }}" >{{ $row->name }}</option>
                                @empty
                                @endforelse
                              </select>
                            </div>
                          </div>

                          <input type="hidden" name="store" id="store" value="{{$store}}">
                          <div class="row setting-form-row" >
                            <div class="col-md-5">
                              <label>Page</label>
                              <input type="text" name="pages[]" placeholder="page" class="form-control">
                              
                            </div>
                            <div class="col-md-1 ml-4">
                              
                              <button type="button" id="add-more-page" class="btn btn-sm btn-success add-more-page"><i class="fa fa-plus-circle"></i> </button>
                            </div>
                            <div class="more-page"></div>
                            
                            
                          </div>
                           <div class="row setting-form-row">
                              @foreach($socials as $social)
                                <div class="col-md-2">
                                  <label class="social-lable"><input type="checkbox" name="social[]" id="social-check{{$social->id}}" value="{{$social->id}}" > {{$social->name}}</label> 
                                </div>
                              @endforeach
                           </div>
                          <input name="postIng_type" type="hidden" value="1">
                          <input name="main_setting_id" type="hidden" value="">
                          <input name="time[]" type="hidden" id="time" value="">
                            <div class="row pt-4" id="">
                            <div class="col-md-3">
                            <!-- <div id="timepicker-selectbox"></div> -->
                            <div id="timepicker-spinbox">
                            </div>

                            </div>
                            <!--<div class="col-md-2">
                                <select class="form-control" name="time[]" id="product_change_status">
                                  <option value="00:00" >12:00 AM</option>
                                  <option value="01:00" >01:00 AM</option>
                                  <option value="02:00" >02:00 AM</option>
                                  <option value="03:00" >03:00 AM</option>
                                  <option value="04:00" >04:00 AM</option>
                                  <option value="05:00" >05:00 AM</option>
                                  <option value="06:00" >06:00 AM</option>
                                  <option value="07:00" >07:00 AM</option>
                                  <option value="08:00" >08:00 AM</option>
                                  <option value="09:00" >09:00 AM</option>
                                  <option value="10:00" >10:00 AM</option>
                                  <option value="11:00" >11:00 AM</option>
                                  <option value="12:00" >12:00 PM</option>
                                  <option value="13:00" >01:00 PM</option>
                                  <option value="14:00" >02:00 PM</option>
                                  <option value="15:00" >03:00 PM</option>
                                  <option value="16:00" >04:00 PM</option>
                                  <option value="17:00" >05:00 PM</option>
                                  <option value="18:00" >06:00 PM</option>
                                  <option value="19:00" >07:00 PM</option>
                                  <option value="20:00" >08:00 PM</option>
                                  <option value="21:00" >09:00 PM</option>
                                  <option value="22:00" >10:00 PM</option>
                                  <option value="23:00" >11:00 PM</option>
                                </select>
                                </div> -->
                                <div class="col-md-3 setting-input">
                                <select class="form-control product_change_status0" name="type[]" id="product_change_status" onchange="checkType(0)">
                                      <option value="">Select Type</option>
                                      @foreach($types_for_setting as $type)
                                      <option value="{{$type->id}}" >{{$type->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 setting-input">
                                  <input type="hidden" name="category[]" id="category0">
                                  <div class="cate-multi0" style="min-width: 16%;">
                                  <select class="form-control custom-select multiselect0" multiple="multiple"  onchange="cateVlue(0, 2)" style="display:none;" title="Select Categories" multiple data-actions-box="true" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <!--<option value="">Select Category</option> -->
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" >{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                  </div>

                                    <select style="" class="form-control custom-select singleselect0" onchange="cateVlue(0, 1)" id="product_change_status">
                                      <option value="">Select Category</option> 
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" >{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                </div> 
                                <div class="col-md-2 setting-input">
                                <input type="hidden" name="is_active[]" id="is_active01" value="1">
                                <input class="form-check-input" type="checkbox" id="is_active_check01" onchange="isActiveOrNot('0',1)"/>&nbsp;&nbsp; Is Active
                                
                                </div>
                                <div class="col-md-2 setting-input">
                                  <button type="button" class="btn btn-sm btn-danger " ><i class="fa fa-minus-circle"></i> </button>
                                </div>  
                            </div>
                            <div class="form-rows"></div>
                            
                            <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-2">
                                
                                 <!-- <button type="submit" class="btn btn-sm btn-info save-btn">Save</button> -->
                                  <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
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
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
        <script type="text/javascript" class="code-js">
            
            var tpSpinbox = new tui.TimePicker('#timepicker-spinbox', {
                initialHour: 12,
                initialMinute: 0,
                // disabledHours: [1, 2, 13, 14],
                inputType: 'spinbox',
                
            },$('#time').val('12:0'));
            tpSpinbox.on('change', (e) => {
            $('#time').val(e.hour+':'+e.minute)
          });
        </script>
<script>
$(document).ready(function(){
console.log("OKkkkkkkkkkkkkkk");
// var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
// removeItemButton: true,
// maxItemCount:5,
// searchResultLimit:5,
// renderChoiceLimit:5
// });
$('.selectpicker').selectpicker();

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

  $(document).ready(function(){
      // $('.clockpicker').clockpicker();
      // $('#time').val('12:0');
    });

  function loadTimePicker(index) {
    // $('.clockpicker').clockpicker();
    var selector = '#timepicker-spinbox-'+index;
    var tpSpinbox = new tui.TimePicker(selector, {
                initialHour: 12,
                initialMinute: 0,
                // disabledHours: [1, 2, 13, 14],
                inputType: 'spinbox'
            },$('#time'+index).val('12:0'));
            tpSpinbox.on('change', (e) => {
            console.log(e);
            $('#time'+index).val(e.hour+':'+e.minute)
          });
         
  }

$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.form-rows');
  var add_btn    = $('.add-more');
  var x = 1;
  $(add_btn).click(function(e) {
    
    e.preventDefault();
    if(x < max_fields) {
      
      $(wrapper).append('<div class="row pt-4 field-row row_'+x+' appended" id="appended">' +'\n'+
                            '<div class="col-md-3"> '+'\n'+
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
                            '<div class="col-md-2  setting-input">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
                        
  loadTimePicker(x);
  
  $('.'+x).css('display', 'none');
  x++;
    }
  });
  $(wrapper).on('click','.btn-remove', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    console.log($(".appended").length);
    // if($(".appended").length < 1){
    //   $('.save-btn').css('display', 'none');
    //   $('#add-more').text('Add Setting');
    // }
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
      
      $(wrapper).append('<div class="page"><div class="col-md-5"><label>Page</label><input type="text" name="pages[]" placeholder="page" class="form-control"></div>' +'\n'+
                            '<div class="col-md-1"><button type="button" id="add-more-page" class="btn btn-sm remove-page">X </button></div></div>');
                        
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

function isActiveOrNot(index, param) {
  var checked_value = $('#is_active_check'+index+param).is(":checked");
  if(checked_value) {
    $('#is_active'+index+param).val(0);
    console.log("Yes");
  }else {
    $('#is_active'+index+param).val(1);
    console.log("no");
  }
}

$('.setting_form').submit(function(event) {
  // console.log($('#time').val());
  event.preventDefault();
  var store = $("#store").val();
  var st = (store == 1) ? 'ba' : 'df';
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
      console.log("ok return");
      var setting_list = <?php echo Session::get('ba_main_setting_list') ?>;
      // setting_list = JSON.parse(setting_list);
      console.log(setting_list.title)
      setting_list.forEach(function(item) {
        console.log(item.title);
      })
      // $.each( setting_list, function( key, value ) {
      //   alert( key + ": " + JSON.parse(value) );
      // });
      console.log(setting_list);
      $('.setting_view_modal').modal('toggle');
        $('.msg_success').text('Setting saved successfully.');
        $('.msg_success').css('display', 'block');
        $('.setting-body-data-'+st).html(respo);
        setTimeout(() => {
          $('.msg_success').css('display', 'none');
      },5000);
    }
   
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

</script>