@extends('layouts.app')

@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <div class="d-flex justify-content-between">
                                <div class="align-self-center">
                                    <strong>Category Settings</strong>
                                </div>
                                <div class="align-self-end float-left">
                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#w5-tab1" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">Main Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-tab2" role="tab" aria-controls="tab2" aria-selected="false">Sub Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab3" data-toggle="tab" href="#w5-tab3" role="tab" aria-controls="tab3" aria-selected="false">Category And Group Setting</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body no-p">
                            <div class="tab-content">
                                <div class="tab-pane fade active show text-center p-4" id="w5-tab1" role="tabpanel" aria-labelledby="w5-tab1">
                                        
                                        <div class="toast"
                                            data-title="Hi, there!"
                                            data-message="Hope you like paper panel."
                                            data-type="success" style="display: none;">
                                        </div>
                                    <h5 class="card-title">Main Category Setting</h5>
                                                  <span id="btn_delete_all_selected" style="float:right; display: none"><button  class="btn btn-primary delete_all" data-url="{{ url('myproductsDeleteAll') }}">Delete All Selected</button></span>
                                                  <div id="status_changed_msg" style="display: none"></div>
                                                  <div class="sub-setting-loop col-md-12 text-center" >
                                                            <form name="group_category_form" class="group_category_form" class="" action="{{url('save/group/main/category')}}" method="post">
                                                                {{ csrf_field() }}
                                                                @php $cate_no = 0; @endphp
                                                                @foreach($categories as $key => $catt)
                                                                @php $cate_no = $cate_no+1; @endphp
                                                                  <div class="row cate-row mt-2" id="cate_row_{{$key}}">
                                                                    <input name="category_id[]" type="hidden" value="{{$catt->id}}">
                                                                      <div class="col-md-6">
                                                                        <input name="cate_name[]" class="form-control" placeholder="Enter category name" value="{{$catt->name}}">
                                                                      </div>
                                                                      <div class="col-md-4">
                                                                        <input name="cate_code[]" class="form-control" placeholder="Enter category code" value="{{@$catt->code}}">
                                                                      </div>
                                                                      <div class="col-md-1">
                                                                        <button type="button" id="add-more" class="btn btn-sm btn-danger " onclick="deleteCateSetting('{{$catt->id}}','{{$key}}')">
                                                                        <i class="icon icon-minus-circle"></i> 
                                                                         <!-- Remove -->
                                                                        </button>
                                                                      </div>  
                                                                  </div>
                                                                  
                                                                  @endforeach
                                                                  <div class="cate-form-rows"></div>
                                                                  
                                                                  <div class="row">
                                                                    <div class="col-md-12 more-row">
                                                                      
                                                                     
                                                                      <div class="col-md-6">
                                                                      @if(count($categories) < 1)
                                                                     <p>No main category available..</p>
                                                                     @endif
                                                                      <span class="cat-error-msge error-mesge mt-2" style="color: red;"></span>
                                                                    </div>
                                                                      <div class="col-md-12 text-right more-paid mb-2"style="padding-right: 25px;" >
                                                                        <button type="button" id="add-more-cate" class="btn btn-sm btn-success add-more-cate">
                                                                            <i class="icon icon-plus-circle"></i> 
                                                                        </button>
                                                                        <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                                                      </div>
                                                                      </div>  
                                                                      <div class="col-md-12 more-row text-right category-save">
                                                                          <button type="submit" class="btn btn-sm btn-info category-save-btn">Save</button>
                                                                      </div>
                                                                  </div>
                                                          </form>
                                                        </div>
                                                        <span class="alert alert-success category_msg_success col-md-12" style="display: none;"></span>
                                </div>

                                <div class="tab-pane fade text-center p-4" id="w5-tab2" role="tabpanel" aria-labelledby="w5-tab2">
                                    <h5 class="card-title">Sub Category Setting</h5>
                                            <span id="btn_delete_all_selected" style="float:right; display: none"><button  class="btn btn-primary delete_all" data-url="{{ url('myproductsDeleteAll') }}">Delete All Selected</button></span>
                                            <div id="status_changed_msg" style="display: none"></div>
                                            <div class="sub-setting-loop col-md-12" >
                                                        <form name="sub_cates_form" class="sub_cates_form" class="" action="{{url('add/group')}}" method="post">
                                                            {{ csrf_field() }}
                                                            @php $subcate_no = 0; @endphp
                                                            @foreach($subcategories as $key => $catt)
                                                            @php $subcate_no = $subcate_no+1; @endphp
                                                            <div class="row cate-row mt-2" id="cate_row_{{$key}}">
                                                            <input name="sub_category_id[]" type="hidden" value="{{$catt->id}}">

                                                                <div class="col-md-4">
                                                                <select class="form-control" name="main_cate[]" id="product_change_status">
                                                                        <option value="">Select Category</option>
                                                                        @foreach($categories as $cate)
                                                                        <option value="{{$cate->id}}" {{($cate->id == $catt->group_main_category_id) ? 'selected' : ''}}>{{$cate->name}}</option>
                                                                        
                                                                        @endforeach
                                                                    </select>
                                                                </div> 
                                                                
                                                                <div class="col-md-4">
                                                                    <input name="sub_cate[]" class="form-control" placeholder="Sub category" value="{{$catt->name}}">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <input name="sub_cate_code[]" class="form-control" placeholder="Sub cate code" value="{{@$catt->code}}">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button type="button" id="add-more" class="btn btn-sm btn-danger " onclick="deleteSubCateSetting('{{$catt->id}}','{{$key}}')">
                                                                    <i class="icon icon-minus-circle"></i> 
                                                                    <!-- Remove -->
                                                                    </button>
                                                                </div>  
                                                            </div>
                                                            @endforeach
                                                            <div class="sub-cate-form-rows"></div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-12 more-row">
                                                                
                                                                
                                                                <div class="col-md-6">
                                                                @if(count($subcategories) < 1)
                                                                <p class="sub">No sub category available..</p>
                                                                @endif
                                                                <span class="sub-cat-error-msge error-mesge" ></span>
                                                                </div>
                                                                <div class="col-md-12 text-right more-paid mb-5" >
                                                                    <button type="button" id="add-more-sub-cate" class="btn btn-sm btn-success add-more-sub-cate">
                                                                        <i class="icon icon-plus-circle"></i> 
                                                                    </button>
                                                                    <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                                                </div>
                                                                </div>  
                                                                <div class="col-md-12 more-row text-right sub-category-save">
                                                                    <button type="submit" class="btn btn-sm btn-info sub-category-save-btn">Save</button>
                                                                </div>
                                                            </div>
                                                    </form>
                                                    </div>

                                    <span class="alert alert-success sub_category_msg_success col-md-12 " style="display: none;"></span>
                                </div>

                                <div class="tab-pane fade text-center p-5" id="w5-tab3" role="tabpanel" aria-labelledby="w5-tab3">
                                    <h4 class="card-title">Category Setting With Group</h4>
                        
                                    <span id="btn_delete_all_selected" style="float:right; display: none"><button  class="btn btn-primary delete_all" data-url="{{ url('myproductsDeleteAll') }}">Delete All Selected</button></span>
                                    <div id="status_changed_msg" style="display: none"></div>
                                    <div class="sub-setting-loop col-md-12" >
                                                <form name="category_group_setting_form" class="category_group_setting_form" class="" action="{{url('add/group')}}" method="post">
                                                    {{ csrf_field() }}
                                                    @php $cate_no = 0; @endphp
                                                    @foreach($groups as $key => $catt)
                                                    @if($catt->category_name != '' || $catt->category_name != null)
                                                    @php $cate_no = $cate_no+1; @endphp
                                                    <div class="row cate-row mt-2" id="cate_row_{{$key}}">
                                                        <div class="col-md-5">
                                                        <select class="form-control" name="category[]" id="product_change_status">
                                                                <option value="">Select Category</option>
                                                                @foreach($groups as $cate)
                                                                <option value="{{$cate->group_ids}}" {{($catt->name == $cate->name) ? 'selected' : ''}}>{{$cate->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div> 
                                                        <div class="col-md-5">
                                                            <!-- <input name="cate_name[]" class="form-control" placeholder="Enter Category Nmae" value="{{$catt->category_name}}"> -->
                                                            <select class="form-control" name="cate_name[]" id="product_change_status">
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $mcate)
                                                                <option value="{{$mcate->id}}" {{($catt->category_id == $mcate->id) ? 'selected' : ''}}>{{$mcate->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" id="add-more" class="btn btn-sm btn-danger " onclick="deleteCateSetting('{{$catt->group_ids}}','{{$key}}')">
                                                            <i class="icon icon-minus-circle"></i> 
                                                            <!-- Remove -->
                                                            </button>
                                                        </div>  
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                    <div class="cate-group-setting-form-rows"></div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 more-row">
                                                        
                                                        <div class="col-md-6">
                                                    
                                                        <span class="cat-error-msge error-mesge" ></span>
                                                        </div>
                                                        <div class="col-md-12 text-right more-paid mb-2">
                                                            <button type="button" id="add-more-group-cate-setting-btn" class="btn btn-sm btn-success add-more-group-cate-setting-btn">
                                                                <i class="icon icon-plus-circle"></i> 
                                                            </button>
                                                            <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                                        </div>
                                                        </div>  
                                                        <div class="col-md-8">
                                                            <span class="alert alert-success group_category_msg_success col-md-12" style="display: none;"></span>
                                                        </div>
                                                        <div class="col-md-4 more-row text-right category-save">
                                                            <button type="submit" class="btn btn-sm btn-info category-save-btn">Save</button>
                                                        </div>
                                                    </div>
                                            </form>
                                            </div>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
  $(".nav-tabs a").click(function(){
    $(this).tab('show');
  });
});
 

// MAin Cate
$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.cate-form-rows');
  var add_btn    = $('.add-more-cate');
  var categories = <?php echo $cate_no ?>;
  if(categories < 1) {
    $('.category-save-btn').css('display', 'none');
    $('#add-more-cate').text('Add');
  }
  var x = 1;
  $(add_btn).click(function(e) {
    if($(".appended-cate").length >= 0) {
    $('.category-save-btn').css('display', 'inline-block');
    $('#add-more-cate').html('<i class="icon icon-plus-circle"></i>');
    $('p .sub').css('display', 'none');
  }else {
    
  }
    e.preventDefault();
      x++;
      $(wrapper).append('<div class="row field-row row_'+x+' mt-2 appended-cate cate-row" id="appended-cate">' +'\n'+
                            '<input name="category_id[]" type="hidden" value="">' +'\n'+
                            '<div class="col-md-6">' +'\n'+
                            '<input name="cate_name[]" class="form-control" placeholder="Enter category name">'+'\n'+
                            '</div>' +'\n'+
                            '<div class="col-md-4">'+'\n'+
                             '<input name="cate_code[]" class="form-control" placeholder="Enter category code" value="{{@$catt->code}}">'+'\n'+
                             '</div>'+'\n'+
                            '<div class="col-md-2">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="icon icon-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
   
  });
  $(wrapper).on('click','.btn-remove', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    if($(".appended-cate").length <= 0 && categories < 1){
      $('.category-save-btn').css('display', 'none');
      $('#add-more-cate').text('Add');
      $('p .sub').css('display', 'inline-block');
    }
    // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
  })
}) ;

$('.group_category_form').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: "{{route('save.main.category')}}",
    type: "POST",
    data: $(this).serialize(),
    cache: false,
    beforeSend: function() {
      $('.category-save-btn').html('<i class="icon icon-spin fa-circle-o-notch"></i>');
      $('.savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('.category-save-btn').html('Save');
      $('.savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      $('.cat-error-msge').text('Please fill all fields.');
      setTimeout(() => {
        $('.cat-error-msge').text('');
      }, 4000)
    }
  }).then(function(resp) {
    if(resp) {
      // $('.toast').toast('show');
      $('.category_msg_success').show();
      $('.category_msg_success').text(resp.mesg);
     setTimeout(() => {
         $('.category_msg_success').hide();
         $('.category_msg_success').text('');
       }, 4000)
    }
 })
});

$('.category_group_setting_form').submit(function(e) {
  e.preventDefault();
  $.ajax({
    url: "{{url('/save/category/name')}}",
    type: "POST",
    data: $(this).serialize(),
    cache: false,
    beforeSend: function() {
      $('.category-save-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('.category-save-btn').html('Save');
      $('.savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      $('.cat-error-msge').text('Please fill all fields.');
      setTimeout(() => {
        $('.cat-error-msge').text('');
      }, 4000)
    }
  }).then(function(resp) {
     $('.group_category_msg_success').show();
     $('.group_category_msg_success').text(resp.mesg);
    setTimeout(() => {
        $('.group_category_msg_success').hide();
        $('.group_category_msg_success').text('');
      }, 4000)
  })
});

function deleteCateSetting(id, key) {
    swal({
            title: "Are sure delete the category?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        },function (e) {
        if (e === true) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{url('/destroy/group/main/cate/setting')}}/",
                type: 'POST',
                data: {_token: CSRF_TOKEN, id:id},
                dataType: 'JSON',
                success: function (results) {

                    if (results.status === true) {
                      $('#cate_row_'+key).remove();
                        swal("Done!", results.mesg, "success");
                    } else {
                        swal("Sorry!", "There are some issues.", "error");
                    }
                }
            });

        } else {
            e.dismiss;
        }

        }, function (dismiss) {
        return false;
        })
  }
 
  function deleteSubCateSetting(id, key) {
    swal({
            title: "Are sure delete the category?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        },function (e) {
        if (e === true) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{url('/destroy/group/sub/cate/setting')}}/",
                type: 'POST',
                data: {_token: CSRF_TOKEN, id:id},
                dataType: 'JSON',
                success: function (results) {

                    if (results.status === true) {
                      $('#cate_row_'+key).remove();
                        swal("Done!", results.mesg, "success");
                    } else {
                        swal("Sorry!", "There are some issues.", "error");
                    }
                }
            });

        } else {
            e.dismiss;
        }

        }, function (dismiss) {
        return false;
        })
  }
  // Sub Cate
$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.sub-cate-form-rows');
  var add_btn    = $('.add-more-sub-cate');
  var categories =  <?php echo $subcate_no ?>;
  if(categories < 1) {
    $('.sub-category-save-btn').css('display', 'none');
    $('#add-more-sub-cate').text('Add');
  }
  var x = 1;
  $(add_btn).click(function(e) {
    if($(".appended-sub-cate").length >= 0) {
    $('.sub-category-save-btn').css('display', 'inline-block');
    $('#add-more-sub-cate').html('<i class="icon icon-plus-circle"></i>');
    $('p').css('display', 'none');
  }else {
    
  }
    e.preventDefault();
      x++;
      $(wrapper).append('<div class="row field-row row_'+x+' appended-sub-cate cate-row mt-2" id="appended-sub-cate">' +'\n'+
                            '<input name="sub_category_id[]" type="hidden" value="">' +'\n'+
                            '<div class="col-md-4"><select class="form-control" name="main_cate[]" id="product_change_status">' +'\n'+
                                  '<option value="">Select Category</option>' +'\n'+
                                  <?php foreach($categories as $cate) {?>
                                  '<option value="<?php echo $cate->id; ?>" ><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>'+'\n'+
                            '<div class="col-md-4">' +'\n'+
                            '<input name="sub_cate[]" class="form-control" placeholder="Sub category">'+'\n'+
                            '</div>' +'\n'+
                            '<div class="col-md-2">'+'\n'+
                            '<input name="sub_cate_code[]" class="form-control" placeholder="Sub cate code" value="{{@$catt->code}}">'+'\n'+
                            '</div>'+'\n'+
                            '<div class="col-md-2">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove-sub"><i class="icon icon-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
   
  });
  $(wrapper).on('click','.btn-remove-sub', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    if($(".appended-sub-cate").length <= 0 && categories < 1){
      $('.sub-category-save-btn').css('display', 'none');
      $('#add-more-sub-cate').text('Add');
      $('p').css('display', 'inline-block');
    }
    // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
  })
}) ;

// ============================
// setting category add-more start
$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.cate-group-setting-form-rows');
  var add_btn    = $('.add-more-group-cate-setting-btn');
  var categories = <?php echo $cate_no ?>;
  if(categories < 1) {
    $('.category-save-btn').css('display', 'none');
    $('#add-more-cate').text('Add Setting');
  }
  var x = 1;
  $(add_btn).click(function(e) {
    console.log("Yes Ok");
    if($(".appended-cate").length >= 0) {
    $('.category-save-btn').css('display', 'inline-block');
    $('#add-more-group-cate-setting-btn').html('<i class="icon icon-plus-circle"></i>');
  }else {
    
  }
    e.preventDefault();
    if(x < max_fields) {
      x++;
      $(wrapper).append('<div class="row field-row row_'+x+' appended-cate cate-row" id="appended-cate">' +'\n'+
                            '<div class="col-md-5"><select class="form-control" name="category[]" id="product_change_status">' +'\n'+
                                  '<option value="">Select Category</option>' +'\n'+
                                  <?php foreach($groups as $cate) {?>
                                  '<option value="<?php echo $cate->group_ids; ?>"  <?php if($cate->category_name != null) {echo 'disabled class="disable"';}else{echo '';}?>><?php echo $cate->name; ?></option>' +'\n'+
                                  <?php } ?>
                                '</select></div>'+'\n'+
                            '<div class="col-md-5">' +'\n'+
                            '<select class="form-control" name="cate_name[]" id="product_change_status">'+'\n'+
                                            '<option value="">Select Category</option>'+'\n'+
                                            <?php foreach($categories as $mcate) {?>
                                            '<option value="<?php echo $mcate->id; ?>" ><?php echo $mcate->name; ?></option>'+'\n'+
                                            <?php } ?>
                                          '</select>'+'\n'+
                            '</div>' +'\n'+
                            '<div class="col-md-2">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-cate-group-remove"><i class="icon icon-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
    }
  });
  $(wrapper).on('click','.btn-cate-group-remove', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    if($(".appended-cate").length <= 0 && categories < 1){
      $('.category-save-btn').css('display', 'none');
      $('#add-more-group-cate-setting-btn').text('Add Setting');
    }
    // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
  })
}) ;

$('.sub_cates_form').submit(function(e) {
  e.preventDefault();
  console.log("OOOOOOOOOk");
  $.ajax({
    url: "{{route('save.sub.category')}}",
    type: "POST",
    data: $(".sub_cates_form").serialize(),
    cache: false,
    beforeSend: function() {
      $('.sub-category-save-btn').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('.savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('.sub-category-save-btn').html('Save');
      $('.savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      $('.sub-cat-error-msge').text('Please fill all fields.');
      setTimeout(() => {
        $('.sub-cat-error-msge').text('');
      }, 4000)
    }
  }).then(function(resp) {
    if(resp) {

      $('.sub_category_msg_success').show();
      $('.sub_category_msg_success').text(resp.mesg);
     setTimeout(() => {
         $('.sub_category_msg_success').hide();
         $('.sub_category_msg_success').text('');
       }, 4000)
    }
 })
});
</script>

@endpush