@extends('layout.theme')

@section('title', 'Home')

@section('content')

<section class="content">
 <div class="container-fluid">
  
   <div class="container">
      <!-- Setting Sub Tab Sectopn start -->

      <div id="setting" class="">
        <div class="container-fluid sub-tabs">
        
        <div class="tab-content">
        <!-- BA Setting Section Start -->
        <div id="baSetting" class="tab-pane fade in active">
        
        <h5>Duties</h5>
        <span class="alert alert-success msg_success" style="display: none;"></span>
        <div class="row new-setting-btn text-right">
          <!--<h5 style="display: inline;">BusinessArcade Setting</h5> -->
          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target=".setting_view_modal"><i class="fa fa-plus-circle"></i> New</button>
        </div>
          <div class="table-responsive">
          <div id="status_changed_msg" style="display: none"></div>
                <table class="table" width="100%" style="border: 1px solid #2196f3">

                <thead >

                  <tr style="background-color: #2196f3;color:white">

                  <th scope="col"><center>Name</center></th>
                  <th scope="col"><center>Action</center></th>

                  </tr>

                </thead>

                <tbody class="setting-body-data-ba">
                  @if(count($duties) > 0)
                  @foreach(@$duties as $key=>$duty)
                  <tr id="row_ba{{$key}}" style="border-top: 1px solid gray">

                            <td><center><label>{{ $duty->name }}</label></center></td>
                            <td>
                            <center><label>
                            <a href="#"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Add Products" onclick="editDuty('{{ $duty }}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
                            <a href="#"><i class="fa fa-trash-o fa-2x" onclick="deleteMainSetting('{{ $duty->id }}', '{{$key}}','ba')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a>
                            
                            </label></center>
                            </td>

                  </tr>
                @endforeach
               
                @else
                <tr id="tr_{{@$group->id}}" style="border-top: 1px solid gray">

                <td colspan="2" class="column col-sm-12">
                    <center><label>No Duty Available..</label></center>
                </td>
                </tr>
                @endif

            </tbody>

            </table>
            </div>
        
            </div>
                </div>
          </div>
        </div>
        
      </div>

      </div>
</div>
    

</section>
<!-- 
  Store social post Modal code end-->

  <!--  Setting modal end -->
<div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Add duty</h5>
        <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" > 
      <div class="sub-setting-loop col-md-12" >
                      <form name="duty_form" id="duty_form" class="" action="{{url('add/group')}}" method="post">
                         {{ csrf_field() }}
                          <input name="id" type="hidden" id="id">
                          <input type="hidden" id="activity-length">
                            <div class="row " id="" style="padding-bottom: 12px;">
                            <div class="col-md-10">
                            <!-- <div id="timepicker-selectbox"></div> -->
                            <input type="text" name="duty_name" id="duty_name" class="form-control" placeholder="Duty name">
                            </div>
                            </div>

                            <div class="row " id="" style="padding-top: 12px;padding-bottom: 12px;">
                            <div class="col-md-4 text-right">
                            <span class="">_______________________</span>
                            </div>

                            <div class="col-md-2 text-center">
                            <span class=""><strong> Activities</strong></span>
                            </div>

                            <div class="col-md-4 text-left">
                            <span class="">_______________________</span>
                            </div>
                            </div>

                            <div class="row loadingg" id="">
                            <div class="col-md-10 text-center">
                            <span class="mesg-box">Please waite while activities are loading..</span>
                            </div>
                            </div>
                            <div class="edit_section" style="display: none;">
                            <div class="exist_activities">
                             
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
                                <div class="col-md-4">
                                  <button type="button" id="add-more" class="btn btn-sm btn-success add-more"><i class="fa fa-plus-circle"></i> </button>
                                  <!-- <button type="submit" id="excel_export" name="generate_csv" value="generate_csv" class="btn btn-warning">Export</button> -->
                                </div>
                                </div>  
                            </div>
                            </div>
                            <!-- <div class="form-rows"></div> -->
                            
                            <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                            <button type="button" class="btn btn-info save-btn">Save</button>
                          </div>
                    </form>
            </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
</div>



<!-- update promotion prices code end-->
@include('inventory_management.popup_order_details_form_status');
@endsection

@push('scripts')

<!-- Sweet alert css -->

<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<!-- SweetAlert Plugin Js -->

<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/inventory_management.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/promotion.js') }}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap-multiselect.js') }}"></script>

<link rel="stylesheet" href="{{URL::asset('assets/css/purchase.css') }}">
<link rel="stylesheet" href="{{URL::asset('assets/css/bootstrap-multiselect.css') }}">



<script>

$(document).ready(function() {
  var max_fields = 12;
  var wrapper    = $('.form-rows');
  var add_btn    = $('.add-more');
  var x = 1;
  $(add_btn).click(function(e) {
    
    e.preventDefault();
    console.log($(".activity").length+1);
      $(wrapper).append('<div class="row field-row row_'+x+' appended" id="appended">' +'\n'+
                            '<div class="col-md-4"> '+'\n'+
                            '<input type="hidden" name="activity_id[]" value=""><input type="text" name="activity[]" class="form-control activity" placeholder="Activity">'+'\n'+
                               '</div>' +'\n'+
                               '<div class="col-md-2">' +'\n'+
                                '<input type="hidden" name="is_custom[]" id="is_custom'+x+'0" value="0"><input type="checkbox" id="is_custom_check'+x+'0" onchange="isCustomOrNot('+x+', 0)">&nbsp;&nbsp; Is Custom' +'\n'+
                                  '</div>' +'\n'+
                                  '<div class="col-md-2">' +'\n'+
                                '<input type="hidden" name="is_auto[]" id="is_custom'+x+'0" value="0"><input type="checkbox" id="is_auto_check'+x+'0" onchange="isAutoOrNot('+x+', 0)">&nbsp;&nbsp; Regular' +'\n'+
                                  '</div>' +'\n'+
                                  
                                  '<div class="col-md-2">' +'\n'+
                                '<input type="number" name="points[]" class="form-control activity" placeholder="Points" value="">' +'\n'+
                                  '</div>' +'\n'+
                            '<div class="col-md-2  setting-input">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
                        

 
  $('.'+x).css('display', 'none');
  x++;
  checkActivitiesLength();
  });
  $(wrapper).on('click','.btn-remove', function(e) {
    e.preventDefault();
    $(this).parent('div').parent('div').remove();
    x--;
    console.log($(".appended").length);
    checkActivitiesLength();
    // var lis = document.getElementById(".form-rows").getElementsByTagName("#appended");
  })
}) ;

function isCustomOrNot(index, param) {
  console.log('index ='+index+' param ='+param);
  // $('#is_custom'+index)
  // console.log($('#is_custom_check'+index).is(":checked"));
  var checked_value = $('#is_custom_check'+index+param).is(":checked");
  if(checked_value) {
    $('#is_custom'+index+param).val(1);
    console.log("Yes");
  }else {
    $('#is_custom'+index+param).val(0);
    console.log("no");
  }
}

function isAutoOrNot(index, param) {
  console.log('index ='+index+' param ='+param);
  // $('#is_custom'+index)
  // console.log($('#is_custom_check'+index).is(":checked"));
  var checked_value = $('#is_auto_check'+index+param).is(":checked");
  if(checked_value) {
    $('#is_auto'+index+param).val(1);
    console.log("Yes");
  }else {
    $('#is_auto'+index+param).val(0);
    console.log("no");
  }
}

function checkActivitiesLength() {
  
  if($(".appended").length < 1 && $('#activity-length').val() == 0){
      $('.loadingg').css('display', 'block');
    }else{
      $('.loadingg').css('display', 'none');
    }
}


$('.close-popup').click(function(e) {
  $('.exist_activities').html('');
        $('#duty_name').val('');
});
function editDuty(object) {
  var ob = JSON.parse(object);
  $('#id').val(ob.id);
  $('#duty_name').val(ob.name);
  $('#duty_name').focus();
  $('.group-form-btn').val('Update');
  $('.edit_section').css('display', 'block');
  $.ajax({
    url: "{{url('/get/exist/activeties')}}/"+ob.id,
    type: "GET",
    success: function(response) {
      if(response.status) {
       console.log(response.activities.length);
       var html = '';
       $('#activity-length').val(response.activities.length);
       if(response.activities.length > 0) {
         
       $('.loadingg').css('display', 'none');
        response.activities.forEach(function callback(value, index) {
          var check_yes_no = (value.is_custom == 1) ? 'checked' : '';
          var auto_yes_no = (value.is_auto == 1) ? 'checked' : '';
          html += ' <div class="row row_'+index+'" id="appended">' +'\n'+
                                '<div class="col-md-4">' +'\n'+
                                '<input type="hidden" name="activity_id[]" value="'+value.id+'"><input type="text" name="activity[]" class="form-control activity" placeholder="Activity" value="'+value.name+'">' +'\n'+
                                  '</div>' +'\n'+
                                  '<div class="col-md-2">' +'\n'+
                                '<input type="hidden" name="is_custom[]" id="is_custom'+value.id+'1" value="'+value.is_custom+'"><input type="checkbox" '+check_yes_no+' id="is_custom_check'+value.id+'1" onchange="isCustomOrNot('+value.id+',1)">&nbsp;&nbsp; Is Custom' +'\n'+
                                  '</div>' +'\n'+
                                  '<div class="col-md-2">' +'\n'+
                                '<input type="hidden" name="is_auto[]" id="is_auto'+value.id+'1" value="'+value.is_auto+'"><input type="checkbox" '+auto_yes_no+' id="is_auto_check'+value.id+'1" onchange="isAutoOrNot('+value.id+',1)">&nbsp;&nbsp; Regular' +'\n'+
                                  '</div>' +'\n'+
                                  '<div class="col-md-2">' +'\n'+
                                '<input type="number" name="points[]" class="form-control activity" placeholder="Points" value="'+value.points+'">' +'\n'+
                                  '</div>' +'\n'+
                                '<div class="col-md-2  setting-input">' +'\n'+
                                '<button type="button" id="" onclick="deleteExistActivity('+value.id+','+index+')" class="btn btn-sm btn-danger"><i class="fa fa-minus-circle"></i> </button>'+'\n'+  
                                '</div>' +'\n'+
                              '</div>';
        });
        $('.exist_activities').html(html);
       }else {
        $('.loadingg').css('display', 'block');
        $('.mesg-box').html('No activities available');
       }
       
      }
    },error: function(error) {
      if(error.responseJSON.g_product) {
        
        $('#errorr_text').text('Please select atlest 1 product.');
      }
    }

  })
}

// function deleteExistActivity(id, index) {
//   $.ajax({
//     url: "{{url('/delete/exist/activety')}}/"+id,
//     type: "GET",
//     success: function(response) {
//       if(response.status) {
      
//         $('.row_'+index).remove();
//       }
//     },error: function(error) {
//       if(error) {
        
//         $('#errorr_text').text('Please select atlest 1 product.');
//       }
//     }

//   })
// }
$('#saveForm').click(function(e) {
  e.preventDefault();
  $('#error_text').text('');
  $.ajax({
    url: "{{url('/product/promotion/form')}}",
    type: "POST",
    data: $('#promotion-form').serialize(),
    success: function(result) {
      if(result.status) {
        $('.msge_success').css('display', 'block');
        $('.msge_success').text(result.msge);
        $('#promotion_modal').modal('toggle');
        setTimeout(() =>{
          $('.msge_success').css('display', 'none');
        },5000);
      }
    },error: function(error) {
        $('#error_text').css('display', 'inline-block');
        if(error.responseJSON.product_id) {
          $('#error_text').text('Please select/checked atlest 1 product.');
        }
        if(error.responseJSON.social) {
          $('#error_text').text(error.responseJSON.social[0]);
        }
        if(error.responseJSON.data) {
          $('#error_text').text(error.responseJSON.data[0]);
        }
        if(error.responseJSON.time) {
          $('#error_text').text(error.responseJSON.time[0]);
        }
        
    }
  })
});

$('#productsForm').on('click', function(e) {
  e.preventDefault();
  return;
  $.ajax({
    url: "{{url('/add/products/to/group')}}",
    type: "POST",
    data: $('#products-form').serialize(),
    success: function(response) {
      if(response.status) {
        $('.msgee_success').css('display', 'block');
        $('.msgee_success').text(response.msge);
        $('#add_product_modal').modal('toggle');
        setTimeout(() =>{
          $('.msgee_success').css('display', 'none');
        },5000);
      }
    },error: function(error) {
      if(error.responseJSON.g_product) {
        $('#errorr_text').text('Please select atlest 1 product.');
      }
    }

  })
})

  function productDetails(product) {
    $.ajax({
      url: "{{url('/inventory_manage/group/promotion/products')}}/"+product,
      type: 'GET',
      cache: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token]"').attr('content')
      },
      success: function(result) {
        $('#porduct_view_content').html(result);
      } 
    });
  }


$('.dismiss-btn').on('click', function() {
  
  $('#history-tbl').css('display', 'none');
  $('.msge').css('display', 'none');
})

  function deleteExistActivity(id, row) {
    swal({
            title: "Delete?",
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
                type: 'GET',
                url: "{{url('/delete/exist/activety')}}/" + id,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {
                    console.log(results);
                    if (results.status) {
                      $('.row_'+row).remove();
                        swal("Done!", results.message, "success");
                    } else {
                        swal("Sorry!", results.message, "error");
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
  function deleteSetting(id) {
    swal({
            title: "Delete?",
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
                type: 'GET',
                url: "{{url('/destroy/setting')}}/" + id,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {

                    if (results.success === true) {
                      $('#srow_'+id).remove();
                        swal("Done!", results.message, "success");
                    } else {
                        swal("Sorry!", results.message, "error");
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

  function removeProductFromList(product) {
    $('#product_rom_'+product).remove();
  }



$('.save-btn').click(function(event) {
  event.preventDefault();
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  console.log($('#duty_form').serialize());
  $.ajax({
    url: "{{url('/save/duty')}}",
    type: "POST",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token]"').attr('content')
      },
    data: $('#duty_form').serialize(),
    cache: false,
    beforeSend: function() {
      $('#savePromoForm').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('#savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('#savePromoForm').html('Save');
      $('#savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      if(error.responseJSON.name) {
        $('#error_mesge').text('The name field is required.');
      }
      setTimeout(() => {
        $('#error_mesge').text('');
      }, 3500)
    }
    
  }).then(function(resp) {
    if(resp.status) {
      $('#m_success').show();
      $('#m_success').text('Activity added successfully.');
      setTimeout(() => {
        $('.exist_activities').html('');
        $('#duty_name').val('');
        location.reload();
      }, 1000)
    }else{
      console.log(resp);
    }
  })
});

</script>

@endpush
<style type="text/css">
  .td-valign{
    vertical-align: middle !important;
  }
</style>
 