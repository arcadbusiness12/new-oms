<style>
  .alert-error {
    color: red;
    font-weight: 600;
  }
</style>
<div class="sub-setting-loop col-md-12" >
              <form name="setting_form" class="setting_form" class="" action="{{url('add/group')}}" method="post">
                          {{ csrf_field() }}
                          <input type="hidden" name="store" id="store" value="{{$store}}">
                           <div class="row setting-form-row">
                              @foreach($socials as $social)
                                <div class="col-md-2">
                                  <label class="social-lable"><input type="checkbox" name="social[]" id="social-check{{$social->id}}" value="{{$social->id}}" {{(in_array($social->id, $settings->social_ids)) ? 'checked' : ''}}> {{$social->name}}</label> 
                                </div>
                              @endforeach
                           </div>
                          <input name="postIng_type" type="hidden" value="2">
                          <div class="row " id="">
                           
                            <div class="col-md-4 setting-input">
                              <input type="text" name="setting_name" class="form-control" value="{{$settings->setting_name}}" placeholder="Setting Name">
                             
                              <span class="alert-error alert-error-setting_name"></span>
                            </div>
                            <div class="col-md-4 setting-input">
                              <select class="form-control" name="user" id="product_change_status">
                                  <option value="">Select User</option>
                                  @foreach($users as $user)
                                  <option value="{{$user->user_id}}" {{($settings->user_id == $user->user_id) ? 'selected' : ''}}>{{$user->firstname}} {{$user->lastname}}</option>
                                  @endforeach
                                </select>
                               @php $ad_name = ''; @endphp
                              <span class="alert-error alert-error-user"></span>
                            </div> 
                            <div class="col-md-4 setting-input">
                              <select class="form-control ads_type-select" name="ads_type" id="product_change_status">
                                  <option value="">Ads Type</option>
                                  @foreach($ads_types as $type)
                                    {{($settings->ads_type_id == $type->id) ? $ad_name = $type->name : ''}}
                                  <option value="{{$type->id}}" {{($settings->ads_type_id == $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                                  @endforeach
                                </select>

                              <span class="alert-error alert-error-ads_type"></span>
                            </div>
                          </div>
                          <input name="main_setting_id" type="hidden" value="{{$settings->id}}">

                          <div class="row " id="" style="padding-top: 12px;">
                              @foreach($budget_types as $type)
                                  <div class="col-md-2 setting-input">
                                    <input type="radio" id="{{$type->name}}" name="budget_type" value="{{$type->id}}" {{($settings->budget_type_id == $type->id) ? 'checked' : ''}}>
                                      <label for="{{$type->name}}"  >{{$type->name}}</label> 
                                  </div>
                              @endforeach
                              <div class="col-md-2 setting-input">
                                <label >Estimate Cost Per <span id="estimate-lable">{{$ad_name}}</span></label>
                                
                              </div>
                              <div class="col-md-2 setting-input">
                                <input type="text" name="estimate_cost" id="estimate_cost" class="form-control"
                                placeholder="Estimate Cost Per" value="{{$settings->estimated_cost_per_ad_type}}">
                                
                              </div>
                               
                              <div class="col-md-2 setting-input">
                                <select class="form-control optimization_type-select" name="optimization_type" id="product_change_status">
                                  <option value="">Optimization Type</option>
                                    <option value="ABO" {{($settings->optimization_type == 'ABO') ? 'selected' : ''}}>ABO</option>
                                    <option value="CBO" {{($settings->optimization_type == 'CBO') ? 'selected' : ''}}>CBO</option>
                                  </select>
                                  <span class="alert-error alert-error-optimization_type"></span>
                              </div>
                              
                              <div class="col-md-2 setting-input" id="campaign-budget">
                                @if($settings->optimization_type == 'CBO')
                               <input type="text" name="campaign_budget" id="campaign_budget" class="form-control"
                               placeholder="Campaign Budget" value="{{$settings->campaign_budget}}">
                               @endif
                               
                             </div>

                              <span class="alert-error alert-error-setting_name"></span>
                              <span class="alert-error alert-error-budget_type"></span>
                              {{-- <span class="alert-error alert-error-optimization_type"></span> --}}
                          </div>
                          
                       <div class="row " id="">
                           <div class="col-md-12 setting-input">
                           <input type="hidden" name="range" value="7 Days">
                            <select class="form-control" name="range" id="product_change_status">
                                 <option value="">Select Range</option>
                                 <option value="7 Days" {{($settings->range == '7 Days') ? 'selected' : ''}}>7 Days</option>
                                 <option value="10 Days" {{($settings->range == '10 Days') ? 'selected' : ''}}>10 Days</option>
                                 <option value="15 Days" {{($settings->range == '15 Days') ? 'selected' : ''}}>15 Days</option>
                                 <option value="Ongoinig" {{($settings->range == 'Ongoinig') ? 'selected' : ''}}>On Goinig</option>
                               </select>
                               
                              <span class="alert-error alert-error-range"></span>
                           </div> 
                       </div>

                       @if(count($settings->settingSchedules) > 0)
                          @foreach($settings->settingSchedules as $key => $setting)
                          <div class="row field-roww" id="srow_{{$setting->id}}">
                            <input name="setting[]" type="hidden" id="setting{{$setting->id}}" value="{{$setting->id}}">
                            <div class="col-md-2 setting-input">
                              <input type="text" name="ad_set_name[]" class="form-control ad_set_name{{$setting->id}}" placeholder="Ad Set Name" value="{{$setting->ad_set_name}}">
                            </div>
                             
                                <div class="col-md-2 setting-input">
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
                                <div class="col-md-2 setting-input cate-section{{$setting->id}}" style="width: auto;min-width: 16%;max-width: 55%;">
                                <input type="hidden" id="selected_val{{$setting->id}}" value="{{$setting->category_id}}">
                                <input type="hidden" name="category[]" id="category{{$setting->id}}" value="{{$setting->category_id}}">
                                @if($setting->type && (count(explode(',', $setting->category)) > 1 || $setting->type->name == 'Promo Video' || $setting->type->name == 'New Arrival' || $setting->type->name == 'Clearance' || $setting->type->name == 'Season' || $setting->type->name == 'All' || $setting->type->name == 'Best Sellers'))

                                <div class="cate-multi{{$setting->id}}" style="min-width: 16%;">
                                  <select class="form-control multiselect{{$setting->id}}" onchange="cateVlue('{{$setting->id}}', 2), getSubCategories(this.value, '{{$setting->id}}')" title="Select Categories" multiple data-actions-box="true" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <!--<option value="">Select Category</option> -->
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" @selected(in_array($cate->id, explode(',', $setting->category_id)))>{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                    <select class="form-control singleselect{{$setting->id}} custom-select" style="display:none!important;"  onchange="cateVlue('{{$setting->id}}', 1), getSubCategories(this.value, '{{$setting->id}}')" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <option value="">Select Category</option> 
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" @selected($cate->id == $setting->category_id)>{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                @else

                                  <div class="cate-multi{{$setting->id}} none{{$setting->id}}" style="min-width: 16%;">
                                    <select class="form-control multiselect{{$setting->id}} none{{$setting->id}}" onchange="cateVlue('{{$setting->id}}', 2), getSubCategories(this.value, '{{$setting->id}}')" title="Select Categories" multiple data-actions-box="true" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <!--<option value="">Select Category</option> -->
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" @selected(in_array($cate->id, explode(',', $setting->category_id)))>{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                  </div>
                                    <select style="" class="form-control singleselect{{$setting->id}} custom-select" onchange="cateVlue('{{$setting->id}}', 1), getSubCategories(this.value, '{{$setting->id}}')" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <option value="">Select Category</option> 
                                      @foreach($categories as $cate)
                                      <option value="{{$cate->id}}" {{($setting->category_id == $cate->id) ? 'selected' : ''}}>{{$cate->name}}</option>
                                      @endforeach
                                    </select>
                                    @endif
                                </div> 
                                <div class="col-md-2 setting-input sub-cate-section{{$setting->id}}" style="width: auto;min-width: 16%;max-width: 49%;">
                                    {{-- <select name="sub_category[]" class="form-control sub-cete{{$setting->id}}" id="sub_cate{{$setting->id}}">
                                      <option value="">sub Category</option> 
                                      @if($setting->cate)
                                      @foreach($setting->cate->subCategories as $sub_cate)
                                        <option value="{{$sub_cate->id}}" {{($sub_cate->id == $setting->sub_category_id) ? 'selected' : ''}}>{{$sub_cate->name}}</option> 
                                      @endforeach
                                      @endif
                                    </select> --}}
                                    <input type="hidden" id="selected_sub_val{{$setting->id}}" value="{{$setting->sub_category_id}}">
                                    <input type="hidden" name="sub_category[]" id="sub_category{{$setting->id}}" value="{{$setting->sub_category_id}}">
                                    @if(count(explode(",", $setting->sub_category_id)) > 1 || $setting->type->name == 'All')
                                    <div class="multi-sub{{$setting->id}}" style="width: 100%;">
                                      <select style="" onchange="cateSubVlue({{$setting->id}}, 2)" class="form-control sub-cete{{$setting->id}} multiselectsc{{$setting->id}} sub_cate{{$setting->id}}" id="sub_cate{{$setting->id}}" title="Sub Categories" multiple data-actions-box="true" data-live-search="true">
                                        @if(@$setting->cate->subCategories)
                                        @foreach(@$setting->cate->subCategories as $sub_cate)
                                          <option value="{{$sub_cate->id}}" @selected(in_array($sub_cate->id, explode(',',$setting->sub_category_id)))>{{$sub_cate->name}}</option> 
                                        @endforeach
                                        @endif
                                      </select>
                                    </div>

                                    <div class="single-sub{{$setting->id}} " style="display: none;">
                                      <select style="" onchange="cateSubVlue({{$setting->id}}, 1)" class="form-control custom-select ssub-cete{{$setting->id}} ssub_cate{{$setting->id}}" id="ssub_cate{{$setting->id}}">
                                        <option value="">sub Category</option> 
                                        @if($setting->cate)
                                        @if(@$setting->cate->subCategories)
                                        @foreach(@$setting->cate->subCategories as $sub_cate)
                                          <option value="{{$sub_cate->id}}" {{(in_array($sub_cate->id, explode(',',$setting->sub_category_id))) ? 'selected' : ''}}>{{$sub_cate->name}}</option> 
                                        @endforeach
                                        @endif
                                        @endif
                                      </select>
                                  </div>

                                    @else
                                    <div class="single-sub{{$setting->id}}">
                                        <select style="" onchange="cateSubVlue({{$setting->id}}, 1)" class="form-control custom-select ssub-cete{{$setting->id}} ssub_cate{{$setting->id}}" id="ssub_cate{{$setting->id}}">
                                          <option value="">sub Category</option> 
                                          @if($setting->cate)
                                          @if(@$setting->cate->subCategories)
                                          @foreach(@$setting->cate->subCategories as $sub_cate)
                                            <option value="{{$sub_cate->id}}" @selected(in_array($sub_cate->id, explode(',',$setting->sub_category_id)))>{{$sub_cate->name}}</option> 
                                          @endforeach
                                          @endif
                                          @endif
                                        </select>
                                    </div>
                                    <div class="multi-sub{{$setting->id}}" style="width: 100%;display:none;">
                                      <select style="" onchange="cateSubVlue({{$setting->id}}, 2)" class="form-control sub-cete{{$setting->id}} multiselectsc{{$setting->id}} sub_cate{{$setting->id}}" id="sub_cate{{$setting->id}}" title="Sub Categories" multiple data-actions-box="true" data-live-search="true">
                                        @if(@$setting->cate->subCategories)
                                        @foreach(@$setting->cate->subCategories as $sub_cate)
                                          <option value="{{$sub_cate->id}}" @selected(in_array($sub_cate->id, explode(',',$setting->sub_category_id)))>{{$sub_cate->name}}</option> 
                                        @endforeach
                                        @endif
                                      </select>
                                    </div>
                                    @endif
                                </div>

                                <div class="col-md-2 setting-input">
                                  <input type="text" name="budget[]" class="form-control budget{{$setting->id}}" placeholder="budget" value="{{$setting->budget}}">
                                </div>

                                <div class="col-md-2 setting-input">
                                  <select class="form-control creative_types0" name="creative_types[]" id="creative_types">
                                      <option value="">Creative Type</option>
                                      @foreach($creative_types as $type)
                                      <option value="{{$type->id}}" {{($setting->creative_type_id == $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2 setting-input">
                                <input type="hidden" name="is_active[]" id="is_active{{$setting->id}}1" value="{{$setting->is_active}}">
                                <input class="form-check-input" <?php if($setting->is_active == 0){echo 'checked' ; } ?> type="checkbox" id="is_active_check{{$setting->id}}1" onchange="isActiveOrNot('{{$setting->id}}',1)"/>&nbsp;&nbsp; Is Active
                               
                                </div>

                                <div class="col-md-1 setting-input text-center">
                                  <button type="button" class="btn btn-sm btn-danger " onclick="deleteSetting('{{$setting->id}}')"><i class="icon icon-minus-circle"></i> </button>
                                </div>  
                                <hr style="margin-top: 0.5rem;margin-bottom: 0.5rem; border-top: 2px solid rgb(0 0 0 / 29%);width: 97%;">
                            </div>
                            @endforeach
                            @endif
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
                            <div class="modal-footer">
                              <span class="text-left" id="error-msge" style="color:red;">  </span>
                              <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                              <button type="button" id="add-more" class="btn btn-sm btn-success add-more" ><i class="icon icon-plus-circle"></i> </button>
                              <button type="submit" class="btn btn-info save-btn">Save</button>
                            </div>
                    </form>
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

$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.form-rows');
  var add_btn    = $('.add-more');
  var x = 1;
  $(add_btn).click(function(e) {
    
    e.preventDefault();
    if(x < max_fields) {
      
      $(wrapper).append('<div class="row field-row row_'+x+' appended" id="appended">' +'\n'+
                            ' <div class="col-md-2 setting-input">'+'\n'+
                                '<input type="text" name="ad_set_name[]" class="form-control ad_set_name'+x+'" placeholder="Ad Set Name">' +'\n'+
                              '</div>'+ '\n'+
                            '<div class="col-md-2  setting-input"><select class="form-control custom-select product_change_status'+x+'" name="type[]" id="product_change_status" onchange="checkType('+x+')">' +'\n'+
                                  '<option value="">Select Type</option>' +'\n'+
                                  <?php foreach($types_for_setting as $type){?>
                                  '<option value="<?php echo $type->id; ?>"><?php echo $type->name ;?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>' +'\n'+
                            '<div class="col-md-2 setting-input cate-section'+x+'" style="width: auto;min-width: 16%;max-width: 55%;"><input type="hidden" name="category[]" id="category'+x+'">' +'\n'+
                              '<div class="cate-multi'+x+'" style="min-width: 16%;">' +'\n'+
                              '<select class="form-control custom-select multiselect'+x+'" style="display: none;" onchange="cateVlue('+x+', 2), getSubCategories(this.value, '+x+')" multiple data-live-search="true" id="product_change_status">' +'\n'+
                                  // '<option >Select Category</option>' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>' +'\n'+
                                '<select class="form-control custom-select singleselect'+x+'" onchange="cateVlue('+x+', 1), getSubCategories(this.value, '+x+')" style=""  id="product_change_status">' +'\n'+
                                  '<option >Select Category</option>' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>"><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select>' +'\n'+
                                '</div>'+'\n'+
                                '<div class="col-md-2 setting-input sub-cate-section'+x+'" style="width: auto;min-width: 16%;max-width: 49%;">'+'\n'+
                                    '<input type="hidden" name="sub_category[]" id="sub_category'+x+'">'+'\n'+
                                    '<div class="multi-sub'+x+'" style="width: 100%;">'+'\n'+
                                    '<select style="display:none;" onchange="cateSubVlue('+x+', 2)" class="form-control custom-select sub-cete'+x+' multiselectsc'+x+' sub_cate'+x+'" id="sub_cate'+x+'" title="Select Categories" multiple data-actions-box="true" data-live-search="true">'+'\n'+
                                    '</select>'+'\n'+
                                    '</div>'+'\n'+
                                    '<div class="single-sub'+x+'">'+'\n'+
                                    '<select style="" onchange="cateSubVlue('+x+', 1)" class="form-control custom-select ssub-cete'+x+' ssub_cate'+x+'" data-live-search="true" id="ssub_cate'+x+'">'+'\n'+
                                      '<option value="">sub Category</option> '+'\n'+
                                      
                                    '</select></div>'+'\n'+
                                '</div>'+'\n'+
                                '<div class="col-md-2 setting-input">'+'\n'+
                                  '<input type="text" name="budget[]" class="form-control budget'+x+'" placeholder="budget">'+'\n'+
                                '</div>'+'\n'+
                                '<div class="col-md-2  setting-input"><select class="form-control custom-select creative_types'+x+'" name="creative_types[]" id="creative_types">' +'\n'+
                                  '<option value="">Creative Type</option>' +'\n'+
                                  <?php foreach($creative_types as $type){?>
                                  '<option value="<?php echo $type->id; ?>"><?php echo $type->name ;?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>'+'\n'+
                            '<div class="col-md-2 setting-input">'+'\n'+
                                '<input type="hidden" name="is_active[]" id="is_active'+x+'0" value="1">'+'\n'+
                                '<input class="form-check-input" type="checkbox" id="is_active_check'+x+'0" onchange="isActiveOrNot('+x+',0)"/>&nbsp;&nbsp; Is Active'+'\n'+
                                '</div>'+'\n'+
                            '<div class="col-md-1  setting-input  text-center">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="icon icon-minus-circle"></i> </button>'+'\n'+
                            '</div><hr style="margin-top: 0.5rem;margin-bottom: 0.5rem; border-top: 2px solid rgb(0 0 0 / 29%);width: 97%;">'+'\n'+
                        '</div>'
                        );
                        
  // loadTimePicker(x);
  // $('.selectpicker'+x).selectpicker();
  $('.'+x).css('display', 'none');
  x++;
    }
  });
  $(wrapper).on('click','.btn-remove', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    // if($(".appended").length < 1){
    //   $('.save-btn').css('display', 'none');
    //   $('#add-more').text('Add Setting');
    // }
    // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
  })
}) ;


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