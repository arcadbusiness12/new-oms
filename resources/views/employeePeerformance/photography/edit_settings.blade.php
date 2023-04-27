@extends('layout.theme')
@section('title', 'Home')
@section('content') 
<section class="content">
    <div class="container-fluid">
            {{ csrf_field() }}
            <div class="row clearfix">
                @if(Session::has('setting-error'))
                <p class="alert alert-danger">{{ Session::get('setting-error') }}</p>
                @endif
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    {{--  <div class="panel panel-default">
                        <div class="panel-heading">
                            Public Holidays list
                        </div>
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label" for="out_of_stock">Out Of Stock</label>
                                        <input type="number" name="stock_level[out_of_stock]" value="" id="out_of_stock" class="form-control" autocomplete="off" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label" for="low_stock">Low Stock</label>
                                        <input type="number" name="stock_level[low_stock]" value="" id="low_stock" class="form-control" autocomplete="off" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Edit Settings
                        </div>
                        <div class="panel-body">
                           
            <div class="sub-setting-loop col-md-12" >
              <form name="setting_form" class="setting_form" class="" action="{{route('employee-performance.photography.updateSettings')}}" method="post">
                          {{ csrf_field() }}
                          <input type="hidden" name="setting_id" value="{{ $data->id }}">
                          <div class="row setting-form-row">
                            <div class="col-md-4 setting-input">
                              <input type="text" name="setting_name" class="form-control" value="{{ $data->name }}" placeholder="Setting Name">
                              <span class="alert-error alert-error-setting_name"></span>
                            </div>
                            <div class="col-md-4 setting-input">
                              <select class="form-control" name="user" id="product_change_status">
                                  <option value="">Select Model</option>
                                  @foreach($models as $model)

                                  <option value="{{$model->user_id}}" {{ ($model->user_id == $data->model_id) ? "selected" : "" }} >{{$model->firstname}} {{$model->lastname}}</option>
                                  @endforeach
                                </select>
                              <span class="alert-error alert-error-user"></span>
                            </div> 
                          </div>
                           <div class="row setting-form-row">
                              @foreach($socials as $social)
                                  @php
                                    $social_checked = "";
                                    $user_selected  = "";
                                    if( $data->SettingsSocialPosting ){
                                      foreach( $data->SettingsSocialPosting as $key => $val ){
                                        if( $val->promotion_social_id == $social->id ){
                                          $social_checked = 1;
                                          $user_selected  = $val->user_id;
                                          break;
                                        }
                                      }
                                    }
                                  @endphp
                                <div class="col-md-2" style="border:1px solid lightgray; padding:9px">                                
                                  <center><label class="social-lable"><input type="checkbox" name="social[]" class="social-check" id="social-check{{$social->id}}" value="{{$social->id}}" {{ $social_checked==1 ? "checked" : ""  }} > {{$social->name}}</label>
                                  <select class="form-control {{ $user_selected > 0 ? '' : 'hidden' }} social_postig_user{{ $social->id }}" name="posting_staff[{{$social->id}}]">
                                    <option value="">Select</option>
                                    @forelse( $posting_staff as $key => $staff )
                                    <option value="{{ $staff->user_id }}" {{ ( $staff->user_id == $user_selected ? "selected" : "" ) }}>{{ $staff->firstname }}</option>
                                    @empty
                                    @endforelse
                                  </select></center>
                                </div>
                              @endforeach
                           </div>
                          {{--  <input name="postIng_type" type="hidden" value="2">  --}}
                          {{--  <input name="main_setting_id" type="hidden" value="">  --}}

                          @if( $data->settingsDetail )
                            @foreach( $data->settingsDetail as $key => $settingsDetail )
                              <div class="row" id="exist_product0" style="margin-top:5px !important">
                                <div class="col-md-2 setting-input">
                                  <select class="form-control product_change_status{{ $key }}" name="type[]" id="product_change_status" onchange="checkType({{$key}})">
                                    <option value="">Select Type</option>
                                    @foreach($types_for_setting as $type)
                                     <option value="{{$type->id}}" {{ ( $settingsDetail->promotion_product_type_id == $type->id ) ? "selected" : "" }}>{{$type->name}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                <div class="col-md-2 setting-input">
                                  <input type="hidden" name="category[]" id="category{{ $key }}" value="{{ $settingsDetail->category_id }}" />
                                  @php
                                    $selected_cate = explode(",",$settingsDetail->category_id);
                                    {{--  echo "<pre>"; print_r($selected_cate);  --}}
                                  @endphp
                                    <div class="multi_select{{ $key }}" {!! !is_array($selected_cate) ? 'style="display: none"' : "" !!}>
                                      <select  class="form-control selectpicker{{ $key }}"  onchange="cateVlue({{ $key }}, 2), getSubCategories(this.value, {{ $key }})" multiple data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                        <option value="">Select Category</option> 
                                        @foreach($categories as $cate)                                          
                                          <option value="{{$cate->id}}" {{ in_array($cate->id,$selected_cate) ? "selected" : "" }}>{{$cate->name}}</option>
                                        @endforeach
                                      </select> 
                                    </div> 
                                  <div class="single_select{{ $key }}" {!! is_array($selected_cate) ? 'style="display: none"' : "" !!}> 
                                    <select class="form-control singleselect{{ $key }}" onchange="cateVlue({{ $key }}, 1), getSubCategories(this.value, {{ $key }})" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                      <option value="">Select Category</option> 
                                      @foreach($categories as $cate)                                          
                                        <option value="{{$cate->id}}" {{ ( $settingsDetail->category_id == $cate->id ) ? "selected" : "" }}>{{$cate->name}}</option>
                                      @endforeach
                                    </select>  
                                  </div>                                   
                                </div>
                                <div class="col-md-2 setting-input ">
                                  <input type="hidden" name="sub_category[]" id="sub_category{{ $key }}" value="{{ $settingsDetail->sub_category_id }}">
                                  @php
                                    $selected_sub_cate = explode(",",$settingsDetail->sub_category_id);
                                    {{--  echo "value is = ".$settingsDetail->sub_category_id."<pre>"; print_r($selected_sub_cate);  --}}
                                  @endphp
                                  <div class="multi-sub{{ $key }}" style="width: 100%;"  {!! ( count($selected_cate) > 1  AND is_array($selected_sub_cate) ) ? 'style="display: none"' : "" !!}>
                                    <select onchange="cateSubVlue({{ $key }}, 2)" class="form-control sub-cete{{ $key }} selectpickersc{{ $key }} sub_cate{{ $key }}" id="sub_cate{{ $key }}" title="Select Categories" multiple="" data-actions-box="true" data-live-search="true">
                                        @if( $settingsDetail->sub_categories )
                                          @foreach( $settingsDetail->sub_categories as $subcat )
                                            <option value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                                          @endforeach
                                        @endif                                    
                                    </select>
                                  </div>
                                  <div class="single-sub{{ $key }}" {!! ( $settingsDetail->sub_category_id == "" || !is_array($selected_sub_cate) ) ? 'style="display: none"' : "" !!}>
                                    <select onchange="cateSubVlue({{ $key }}, 1)" class="form-control ssub-cete{{ $key }} ssub_cate{{ $key }}" data-live-search="true" id="ssub_cate{{ $key }}">
                                      @if( $settingsDetail->sub_categories )
                                        @foreach( $settingsDetail->sub_categories as $subcat )
                                          <option value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                                        @endforeach
                                      @endif            
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-2 setting-input">
                                  <input type="hidden" name="is_active[]" id="is_active{{ $key }}{{ $settingsDetail->status }}" value="{{ $settingsDetail->status }}">
                                  <input class="form-check-input" type="checkbox" id="is_active_check{{ $key }}{{ $settingsDetail->status }}" onchange="isActiveOrNot({{ $key }},{{ $settingsDetail->status }})" {{ $settingsDetail->status == 1 ? "checked" : "" }}  />&nbsp;&nbsp; Status
                                </div>
                              </div>
                            @endforeach
                          @endif  
                            <div class="form-rows"></div>
                            
                            <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-2">
                                
                                </div>
                                <div class="col-md-6 error-msge-div">
                                
                                  <span class="error-msge" ></span>
                                </div>
                                <div class="col-md-4">
                                  <button type="button" id="add-more" class="btn btn-sm btn-success add-more" style="margin-right: -11px;"><i class="fa fa-plus-circle"></i> </button>
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
           
                    
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('scripts')
<script>

</script>
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
            
<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/timePicker/tui-time-picker.css') }}">
<script type="text/javascript" src="{{URL::asset('assets/js/timePicker/tui-time-picker.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap-multiselect.js') }}"></script>

<link rel="stylesheet" href="{{URL::asset('assets/css/bootstrap-multiselect.css') }}">
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>  --}}
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">

    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            debug: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />loadingggg',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });
    });
</script>  --}}
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
    $('.multi_select'+index).css('display','block');
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
  var x = {{ $key }};
  x = x + 1;
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
                                '<input type="hidden" name="is_active[]" id="is_active'+x+'0" value="0">'+'\n'+
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
    $('#is_active'+index+param).val(1);
    console.log("Yes");
  }else {
    $('#is_active'+index+param).val(0);
    console.log("no");
  }
}

$('.setting_form').submit(function(event) {
  // console.log($('#time').val());
  event.preventDefault();
  var store = $("#store").val();
  var st = (store == 1) ? 'ba' : 'df';
  $.ajax({
    // url: "{{url('/save/promotion/paid/ads/setting')}}",route('employee-performance.photography.updateSettings')
    url: "{{route('employee-performance.photography.updateSettings')}}",
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
      }else if(error.responseText.indexOf('user') !== -1) {
        var str = JSON.stringify(error.responseText);
        $('.alert-error-user').text('The user field is required.');
        setTimeout(() => {
        $('.alert-error').text('');
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
      }
    } 
  }).then(function(respo) {
    $('#m_success').text('Setting saved successfully.');
    location.reload();
  })
});

</script>

@endpush