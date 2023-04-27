<div class="modal fade edit_setting_popup" id="edit_setting_popup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header text-center">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;">Photography Settings <span id="changed-group" style="color: green;"></span></h5>
          <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body">
            <div class="ajax_content">
              //ajax response will come here
            </div>
           <div class="modal-footer">
          </div>
        </div>
    </div>
  </div>
</div>
            <!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/bootstrap-clockpicker.min.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/bootstrap-clockpicker.min.js') }}"></script> -->
            
            <link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
            <script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
            <script type="text/javascript" src="{{URL::asset('assets/js/bootstrap-multiselect.js') }}"></script>

            <link rel="stylesheet" href="{{URL::asset('assets/css/bootstrap-multiselect.css') }}">
        <script type="text/javascript" class="code-js">
            
          //   var tpSpinbox = new tui.TimePicker('#timepicker-spinbox', {
          //       initialHour: 12,
          //       initialMinute: 0,
          //       // disabledHours: [1, 2, 13, 14],
          //       inputType: 'spinbox',
                
          //   },$('#time').val('12:0'));
          //   tpSpinbox.on('change', (e) => {
          //   $('#time').val(e.hour+':'+e.minute)
          // });
        </script>
<script>
$(document).on('click','.social-check',function(){
  var $this = $(this);
  if( $this.is(':checked') ){
    $('.social_postig_user'+$this.val()).removeClass('hidden');
  }else{
    $('.social_postig_user'+$this.val()).addClass('hidden');
  }
});
$(document).ready(function(){
console.log("OKkkkkkkkkkkkkkk");
// var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
// removeItemButton: true,
// maxItemCount:5,
// searchResultLimit:5,
// renderChoiceLimit:5
// });
$('.selectpicker').selectpicker();

// $('.selectpickersc0').selectpicker();

});

$('.ads_type-select').on('change', function() {
  $('#estimate-lable').html($('#ads_type option:selected').text());
  $('#estimate_cost').attr('placeholder', 'Estimate Cost Per '+$('#ads_type option:selected').text());
});

function cateVlue(i,select) {
  if(select == 2) {
    
    var valuee = $('.selectpicker'+i).val();
  }else {
    var valuee = $('.singleselect'+i).val();
  }
  // console.log('valuee= '+valuee.length);
  if(valuee && valuee.length > 1) {
    console.log("More");
    $('.multi-sub'+i).css('display', 'none');
    $('.single-sub'+i).css('display', 'none');
  }else {
    console.log("Single");
    var selector = "select.product_change_status"+i;
    var type = $(selector+" option:selected").text();
    if(type == 'All') {
      $('.multi-sub'+i).css('display', 'inline-block');
    }else {
      $('.single-sub'+i).css('display', 'inline-block');
    }
    
  }
  // console.log(valuee);
  $('#category'+i).val(valuee);
}

function cateSubVlue(i,select) {
  if(select == 2) {
    
    var valuee = $('.selectpickersc'+i).val();
  }else {
    var valuee = $('.ssub-cete'+i).val();
  }
  
  console.log(valuee);
  $('#sub_category'+i).val(valuee);
}

function multiSelector(index) {
  console.log("ok yes");
  $('.selectpicker'+index).attr('multiple', true);
  $('.'+index).css('display', 'inline-block');
  $('.singleselect'+index).css('display', 'none');
  $('.sub-cete'+index).css('display','none');
  var selector = "select.singleselect"+index;
  $(selector+' option:selected').removeAttr('selected');
  $('.selectpicker'+index).selectpicker("refresh");
  $('.selectpicker'+index).selectpicker();
  // $('.selectpicker'+index).selectpicker("refresh");
}
function checkType(index) {
  var selector = "select.product_change_status"+index;
  var type = $(selector+" option:selected").text();
  $('#category'+index).val('');
  console.log(type);
  if(type == 'New Arrival' || type == 'Clearance' || type == 'Promo Video' || type == 'Season' || type == 'All' || type == 'Best Sellers') {
    // $('.sub-cete'+index).css('display','none');
    // $('.budget'+index).css('display','none');
    multiSelector(index);
  }else {
    // $('.sub-cete'+index).css('display','inline-block');
    // $('.budget'+index).css('display','inline-block');
    var se = $('.selectpicker'+index);
    se.removeAttr('multiple');
    $('.singleselect'+index).css('display', 'inline-block');
    $('.'+index).css('display', 'none');
    var selected = "select.selectpicker"+index;
    $('.selectpicker'+index).selectpicker('val', []);
    // control.selectpicker('val', []);
  }
  
  //   if()
}

function getSubCategories(cate, index) {
  var selector = "select.product_change_status"+index;
  var type = $(selector+" option:selected").text();
  console.log('type= '+type);
  if(type == 'All') {
    $('.sub_cate'+index).css('display', 'inline-block');
    $('.ssub_cate'+index).css('display', 'none');
    $('.selectpickersc'+index).selectpicker("refresh");
    $('.selectpickersc'+index).selectpicker();
  }else {
    $('.sub_cate'+index).css('display', 'none');
    $('.ssub_cate'+index).css('display', 'inline-block');
  }
  $.ajax({
    url: "{{url('/sub/categories/for/paid/setting')}}/"+cate,
    type: "GET",
    cache: false,
    success: function(respo) {
      // console.log(respo);
      var html = '';
      if(respo.status) {
        respo.cates.forEach(function callback(value, index) {
          html += '<option value="'+value.id+'" >'+value.name+'</option>';
        });
      }else {
        html += '<option value="" >Not available.</option>';
      }
      if(type == 'All') {
        $('.selectpickersc'+index).selectpicker();
        $('#sub_cate'+index).html(html);
        // $('.selectpickersc'+index).selectpicker('val', ['One', 'Two', 'Three', 'Four','Five']);
        
        $('.selectpickersc'+index).selectpicker("refresh");
      }else {
        var fist = '<option value="">sub Category</option> ';
        html = fist+html;
        $('#ssub_cate'+index).html(html);
        $('.selectpickersc'+index).selectpicker('val', []);
        $('.selectpickersc'+index).selectpicker("refresh");
      }
      
    }
  })
}

function checkAdType(category, index, type) {
  // console.log(category);
  var adType = $('#ads_type').val();
  if(type == 2) {
    category = $('#category'+index).val();
  }
  console.log(category);
  var product_type = $('.product_change_status'+index).val();
  if(adType && product_type) {
    if(adType == 1) {
      $.ajax({
        url: "{{url('/check/selected/cate/for/adtype')}}/"+category+ '/'+adType+ '/'+product_type,
        type: "GET",
        cache: false,
        success: function(respo) {
          console.log(respo);
          // var html = '';
          if(respo.status) {
            return;
          }else {
            $('#exist_product_text'+index).text('The selected category already exist in '+ respo.template + ' template.');
            $('#exist_product'+index).css('display', 'inline-block');
            if(type == 1) {
              var selector = 'select.singleselect'+index;
              $(selector+" option:selected").prop("selected", false)
            }else{
              $('.selectpicker'+index).selectpicker('val', []);
            }
            setTimeout(() => {
              $('#exist_product').css('display', 'none');
              $('#exist_product_text'+index).text('');
            },5000);
          }
          // $('#sub_cate'+index).html(html);
        }
      })
    }
    // console.log(adType);
  }else {
    $('#adtype_mesge').css('display', 'inline-block');
    if(type == 1) {
      var selector = 'select.singleselect'+index;
      $(selector+" option:selected").prop("selected", false)
    }else{
      $('.selectpicker'+index).selectpicker('val', []);
    }
    setTimeout(() => {
      $('#adtype_mesge').css('display', 'none');
    },5000);
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
      
      $(wrapper).append('<div class="row field-row row_'+x+' appended" id="appended">' +'\n'+
                            '<div class="col-md-2  setting-input"><select class="form-control product_change_status'+x+'" name="type[]" id="product_change_status" onchange="checkType('+x+')">' +'\n'+
                                  '<option value="">Select Type</option>' +'\n'+
                                  <?php foreach($types_for_setting as $type){?>
                                  '<option value="<?php echo $type->id; ?>"><?php echo $type->name ;?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>' +'\n'+
                            '<div class="col-md-2  setting-input"><input type="hidden" name="category[]" id="category'+x+'"><select class="form-control selectpicker'+x+'" style="display: none;" onchange="cateVlue('+x+', 2), getSubCategories(this.value, '+x+')" multiple data-live-search="true" id="product_change_status">' +'\n'+
                                  '<option value="">Select Category</option>' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select>' +'\n'+
                                '<select class="form-control singleselect'+x+'" onchange="cateVlue('+x+', 1), getSubCategories(this.value, '+x+')" style=""  id="product_change_status">' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select>' +'\n'+
                                '</div>'+'\n'+
                                '<div class="col-md-2 setting-input ">'+'\n'+
                                    '<input type="hidden" name="sub_category[]" id="sub_category'+x+'">'+'\n'+
                                    '<div class="multi-sub'+x+'" style="width: 100%;">'+'\n'+
                                    '<select style="display:none;" onchange="cateSubVlue('+x+', 2)" class="form-control sub-cete'+x+' selectpickersc'+x+' sub_cate'+x+'" id="sub_cate'+x+'" title="Select Categories" multiple data-actions-box="true" data-live-search="true">'+'\n'+
                                    '</select>'+'\n'+
                                    '</div>'+'\n'+
                                    '<div class="single-sub'+x+'">'+'\n'+
                                    '<select style="" onchange="cateSubVlue('+x+', 1)" class="form-control ssub-cete'+x+' ssub_cate'+x+'" data-live-search="true" id="ssub_cate'+x+'">'+'\n'+
                                      '<option value="">sub Category</option> '+'\n'+
                                      
                                    '</select></div>'+'\n'+
                                '</div>'+'\n'+
                            '<div class="col-md-2 setting-input">'+'\n'+
                                '<input type="hidden" name="is_active[]" id="is_active'+x+'0" value="1">'+'\n'+
                                '<input class="form-check-input" type="checkbox" id="is_active_check'+x+'0" onchange="isActiveOrNot('+x+',0)"/>&nbsp;&nbsp; Is Active'+'\n'+
                                '</div>'+'\n'+
                            '<div class="col-md-1  setting-input  text-center">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>'
                        );
                        
  // loadTimePicker(x);
      $('.selectpicker'+x).selectpicker();
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
    // url: "{{url('/save/promotion/paid/ads/setting')}}",
    url: "{{route('employee-performance.photography.saveSettings')}}",
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

       if(error.responseText.indexOf('setting_name') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-setting_name').text('The setting name field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
        },5000);
      }
      else if(error.responseText.indexOf('user') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-user').text('The user field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
        },5000);
      }
      else if(error.responseText.indexOf('ads_type') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-ads_type').text('The budget type field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
        },5000);
      }
      else if(error.responseText.indexOf('budget_type') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-budget_type').text('The budget type field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
        },5000);
      }
      else if(error.responseText.indexOf('range') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-range').text('The range type field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
        },5000);
      }
      else if(error.responseJSON.social_ids) {
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
        console.log(error.responseText.indexOf('type'));
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
      }else if(error.responseText.indexOf('estimate_cost') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.error-msge').text('The estimate cost field is required.');
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
    // console.log("ok return");
    var setting_list = <?php echo Session::get('ba_main_setting_list') ?>;
    // setting_list = JSON.parse(setting_list);
    // console.log(setting_list.title)
    setting_list.forEach(function(item) {
      console.log(item.title);
    })
    // $.each( setting_list, function( key, value ) {
    //   alert( key + ": " + JSON.parse(value) );
    // });
    console.log(st);
    $('.setting_view_modal').modal('toggle');
      $('.msg_success').text('Setting saved successfully.');
      $('.msg_success').css('display', 'block');
        $('.setting-body-data-'+st).html(respo);
      setTimeout(() => {
        $('.msg_success').css('display', 'none');
      },5000);
      
    }
   
  })
});

</script>