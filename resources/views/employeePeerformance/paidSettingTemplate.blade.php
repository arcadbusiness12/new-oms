<style>
  .fw-blod {
    font-weight: 700 !important;
  }
</style>

<div class="sub-setting-loop col-md-12 pt-4" >
          <div class="row setting-form-row">
                    <div class="col-12 col-md-6 col-sm-6">
                    
                      <input type="text" name="campaign" id="campaign-input" value="{{$campaignName}}" style="border: 1px solid gainsboro;" class="form-control mb-4">
                    
                      </div>
                      <div class="user-select col-12 col-md-6 col-sm-6">
                        <select class="form-control custom-select" name="user" id="product_change_status">
                          <option value="">Select User</option>
                          @foreach($users as $user)
                          <option value="{{$user->user_id}}" {{($settings->user_id == $user->user_id) ? 'selected' : ''}}>{{$user->firstname}} {{$user->lastname}}</option>
                          @endforeach
                        </select>
                      </div>
          </div>
          <div class="row setting-form-row">
                    <center> -------------------------------------------- <span class="fw-bold"> Template </span> -------------------------------------------- </center>
          </div>
                           <div class="row setting-form-row">
                             <div class="col-md-6">
                               <h5><span class="fw-bold">{{$settings->setting_name}} ({{$settings->title}})</span> </h5>
                             </div>
                              
                              
                           </div>
                           <hr style="margin-top: 0.5rem;margin-bottom: 0.5rem; border-top: 2px solid rgb(0 0 0 / 29%);width: 97%;">
                          <input name="postIng_type" type="hidden" value="2">
                          <div class="row pt-4" id="">
                            <div class="col-md-4 text-black">
                              <label class="fw-bold">Ads Type:</label> {{$settings->adsType->name}}
                            </div> 
                            <div class="col-md-4 text-black">
                               <label class="fw-bold">Budget Type:</label> {{$settings->budgetType->name}}
                            </div>
                            <input name="main_setting_id" type="hidden" value="{{$settings->id}}">
                            
                            <div class="col-md-4 text-black">
                              <label class="fw-bold">Estimate Cost Per:</label> {{$settings->estimated_cost_per_ad_type}}
                           </div>
                           @if($settings->optimization_type != '')
                           <div class="col-md-4 text-black">
                            <label class="fw-bold">Optimization Type:</label> {{$settings->optimization_type}}
                            </div>
                          @endif 
                           @if($settings->optimization_type == 'CBO')
                              <div class="col-md-4 text-black" >
                               <label class="fw-bold">Campaign Budget:</label> {{$settings->campaign_budget}}
                             </div>
                           @endif
                           
                           <div class="col-md-4 text-black">
                           <label class="fw-bold">Range:</label> {{$settings->range}}
                           </div>
                              {{-- <span class="alert-error alert-error-optimization_type"></span> --}}
                        </div>
                          
                       <hr style="margin-top: 0.5rem;margin-bottom: 0.5rem; border-top: 2px solid rgb(0 0 0 / 29%);width: 97%;">
                       <table class="table" width="100%" style="border-bottom: 1px solid #2196f3">
                         <caption>Check the you want</caption>
                         <thead>
                           <tr style="border-bottom: 1px solid #323131 !important;background-color: #3f51b5;color:white">
                            <th class="fw-blod">Ad Set Name</th>
                            <th class="fw-blod">Promotion Type</th>
                            <th class="fw-blod">Categogy</th>
                            <th class="fw-blod">Sub Category</th>
                            <th class="fw-blod">Budget</th>
                            <th class="fw-blod">Creative Type</th>
                            <th class="fw-blod">Action</th>
                          </tr>
                         </thead>
                         <tbody>
                            @if(count($settings->settingSchedules) > 0)
                            @foreach($settings->settingSchedules as $key => $setting)
                                <tr>
                                  <td>{{($setting->ad_set_name != '') ? $setting->ad_set_name : '--'}}</td>
                                  <td>{{$setting->type->name}}</td>
                                  <td>{{$setting->category}}</td>
                                  <td>{{$setting->sub_category}}</td>
                                  <td>{{$setting->budget}}</td>
                                  <td>{{$setting->creativeType->name}}</td>
                                  <td>
                                    <input type="checkbox" name="setting_id[]" value="{{$setting->id}}" class=""> 
                                    
                                  </td>
                                </tr>
                            @endforeach

                            @endif
                            
                         </tbody>
                       </table>

                       
                            <div class="form-rows"></div>
                            
                            <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-2">
                                
                                 <!-- <button type="submit" class="btn btn-sm btn-info save-btn">Save</button> -->
                                  <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                </div>
                                <div class="col-md-4">
                                  
                                  <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                </div>
                                </div>  
                            </div>
                           
                            
            </div>
           
        <script type="text/javascript" class="code-js">
            
            var data = <?php echo $settings->settingSchedules ?>;
            data.forEach(function callback(value, index) {
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

$('.ads_type-select').on('change', function() {
  $('#estimate-lable').html($('.ads_type-select option:selected').text());
  $('#estimate_cost').attr('placeholder', 'Estimate Cost Per '+$('.ads_type-select option:selected').text());
});

function cateVlue(i,select) {
  if(select == 2) {
    
    var valuee = $('.multiselect'+i).val();
  }else {
    var valuee = $('.singleselect'+i).val();
  }
  
  if(valuee && valuee.length > 1) {
    $('.multi-sub'+i).css('display', 'none');
    $('.multi-sub'+i).css('display', 'none');
    $('.single-sub'+i).css('display', 'none');
  }else {
    var selector = "select.product_change_status"+i;
    var type = $(selector+" option:selected").text();
    if(type == 'All') {
      $('.multi-sub'+i).css('display', 'inline-block');
    }else {
      $('.single-sub'+i).css('display', 'inline-block');
    }
    
  }
  $('#category'+i).val(valuee);
}

function cateSubVlue(i,select) {
  if(select == 2) {
    
    var valuee = $('.multiselectsc'+i).val();
  }else {
    var valuee = $('.ssub-cete'+i).val();
  }
  $('#sub_category'+i).val(valuee);
}


function multiSelector(index) {
  $('.multiselect'+index).attr('multiple', true);
    $('.cate-multi'+index).css('display', 'block');
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
    
    $('.cate-section'+index).removeClass('col-md-2');
    multiSelector(index);
  }else {
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

function isActiveOrNot(index, param) {
    // $('#is_custom'+index)
    var checked_value = $('#is_active_check'+index+param).is(":checked");
    if(checked_value) {
      $('#is_active'+index+param).val(0);
    }else {
      $('#is_active'+index+param).val(1);
    }
}


function getSubCategories(cate, index) {
  var selector = "select.product_change_status"+index;
  var type = $(selector+" option:selected").text();
  // console.log(cate);
  if(type == 'All') {
    var valuee = $('.multiselect'+index).val();
    if(valuee && valuee.length > 1) {
      $('.multi-sub'+index).css('display', 'none');
      return;
    }
    $('.multi-sub'+index).css('display', 'inline-block');
    $('.ssub_cate'+index).css('display', 'none');
    // $('.multiselectsc'+index).selectpicker("refresh");
    $('.multiselectsc'+index).select2();
  }else {
    $('.multi-sub'+index).css('display', 'none');
    $('.sub_cate'+index).css('display', 'none');
    $('.ssub_cate'+index).css('display', 'inline-block');
  }
  $.ajax({
    url: "{{url('/productgroup/sub/categories/for/paid/setting')}}/"+cate,
    type: "GET",
    cache: false,
    success: function(respo) {
      var html = '';
      if(respo.status) {
        // html += '<option value="" >Select sub category</option>';
        respo.cates.forEach(function callback(value, index) {
          html += '<option value="'+value.id+'" >'+value.name+'</option>';
        });
      }else {
        html += '<option value="" >Not available.</option>';
      }

      if(type == 'All') {
        $('.sub-cate-section'+index).removeClass('col-md-2');
        $('.multiselectsc'+index).select2();
        $('.multiselect'+index).attr('multiple', true);
        $('.multiselectsc'+index).html(html);
        $('#sub_cate'+index).html(html);
      }else {
        $('.sub-cate-section'+index).addClass('col-md-2');
        var fist = '<option value="">sub Category</option> ';
        html = fist+html;
        $('#ssub_cate'+index).html(html);
      }
      // $('#sub_cate'+index).html(html);
    }
  })
}

$('.setting_form').submit(function(event) {
  // console.log($(this).serialize());
  event.preventDefault();
  var store = $("#store").val();
  var st = (store == 1) ? 'ba' : 'df';
  $.ajax({
    // url: "{{url('/save/promotion/paid/ads/setting')}}",
    url: "{{url('/productgroup/save/promotion/paid/ad/setting')}}",
    type: "POST",
    data: $(this).serialize(),
    caches: false,
    beforeSend: function() {
      $('.save-btn').html('<i class="icon icon-spin icon-circle-o-notch"></i>');
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
      else if(error.responseText.indexOf('optimization_type') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-optimization_type').text('The optimization type field is required.');
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
        $('.alert-error-range').text('The budget type field is required.');
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
      }
    } 
  }).then(function(respo) {
    if(respo.status == 'exist') {
      $('.error-msge').text(respo.meassge);
        setTimeout(() => {
        $('.error-msge').text('');
      },5000);
    }else {
    var setting_list = [];
    setting_list.forEach(function(item) {
    });
    $('.setting_view_modal').modal('toggle');
      $('.msg_success').text('Setting saved successfully.');
      $('.msg_success').css('display', 'block');
      $(".toast-action").data('title', 'Action Done!');
      $(".toast-action").data('type', 'success');
      $(".toast-action").data('message', 'Setting saved successfully.');
      $(".toast-action").trigger('click');
      $('.price-error').html('');
      $('.meta-title-error').html('');
      $('.name-error').html('');
      $('.setting-body-data-'+st).html(respo);
      setTimeout(() => {
        $('.msg_success').css('display', 'none');
      },5000);
    }
  })
});

$('.optimization_type-select').on('change', function() {
  if($(this).val() == 'CBO') {
    $('#campaign-budget').html('<input type="text" name="campaign_budget" id="campaign_budget" class="form-control" placeholder="Campaign Budget">');
  }else {
    $('#campaign-budget').html('');
  }
  
})

</script>