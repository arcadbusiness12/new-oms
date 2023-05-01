@extends('layout.theme')

@section('title', 'Home')

@section('content')
<style>
  .modal-header {
    text-align: center;
    background-color: darkgray;
  }
</style>
<section class="content">
 <div class="container-fluid">
  
   <div class="container">
      <!-- Setting Sub Tab Sectopn start -->

      <div id="setting" class="">
        <div class="container-fluid sub-tabs">
        
        <div class="tab-content">
        <!-- BA Setting Section Start -->
        <div id="baSetting" class="tab-pane fade in active">
        
        <h5>Activities</h5>
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

                  <th scope="col"><center>Duty Name</center></th>
                  <th scope="col"><center>Parent Duty</center></th>
                  <th scope="col"><center>Action</center></th>

                  </tr>

                </thead>

                <tbody class="setting-body-data-ba">
                  @if(count($sub_activaties) > 0)
                  @foreach(@$sub_activaties as $key=>$activity)
                  <tr id="row_ba{{$key}}" style="border-top: 1px solid gray">

                            <td><center><label>{{ $activity->name }}</label></center></td>
                            <td><center><label>{{ $activity->activities->name }}</label></center></td>
                            <td>
                            <center><label>
                            <a href="#"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Add Products" onclick="editDuty('{{ $activity }}')" data-toggle="modal" data-target=".edit_modal"></i></a> |
                            <a href="#"><i class="fa fa-trash-o fa-2x" onclick="deleteMainSetting('{{ $activity->id }}', '{{$key}}','ba')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a>
                            
                            </label></center>
                            </td>

                  </tr>
                @endforeach
               
                @else
                <tr id="tr_{{@$group->id}}" style="border-top: 1px solid gray">

                <td colspan="2" class="column col-sm-12">
                    <center><label>No Sub Duty Available..</label></center>
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
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Add Sub Duties</h5>
        <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" > 
      <div class="sub-setting-loop col-md-12" >
                      <form action="{{url('save/sub/duty')}}" method="post">
                          {{ csrf_field() }}
                          <input name="id" type="hidden" id="id">
                          <input name="time[]" type="hidden" id="time" value="">
                          <div class="row " id="" style="padding-bottom: 12px;">
                            <div class="col-md-10">
                            <!-- <div id="timepicker-selectbox"></div> -->
                            <select class="form-control duty'+x+'" name="duty" id="duty">
                              <option value="">Select Duty</option>
                              <?php foreach($activities as $duty){?>
                              <option value="<?php echo $duty->id; ?>"><?php echo $duty->name ;?></option>
                              <?php } ?>
                            </select>
                            {{-- <input type="text" name="activity" id="activity" class="form-control" placeholder="Activity"> --}}
                            </div>
                          </div>
                            <div class="row " id="">
                            <div class="col-md-10">
                            <!-- <div id="timepicker-selectbox"></div> -->
                             {{-- <select class="form-control duty" name="duty[]" id="duty" onchange="checkType(0)">
                                      <option value="">Select Duty</option>
                                      @foreach($activities as $duty)
                                      <option value="{{$duty->id}}" >{{$duty->name}}</option>
                                      @endforeach
                                    </select> --}}
                                    <input type="text" name="sub_activity[]" id="sub_activity" class="form-control" placeholder="Sub Duty">
                            </div>
                            {{-- <div class="col-md-2 ">
                                  <button type="button" class="btn btn-sm btn-danger " ><i class="fa fa-minus-circle"></i> </button>
                                </div>  --}}
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
                            <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                            <button type="submit" class="btn btn-info save-sub-duty">Save</button>
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
    if(x < max_fields) {
      
      $(wrapper).append('<div class="row field-row row_'+x+' appended" id="appended">' +'\n'+
                            '<div class="col-md-10"> '+'\n'+
                            ' <input name="time[]" type="hidden" id="time'+x+'" value="">'+'\n'+
                            '<input type="text" name="sub_activity[]" id="sub_activity" class="form-control" placeholder="Sub Activity">'+'\n'+
                               '</div>' +'\n'+
                            '<div class="col-md-2  ">'+'\n'+
                            '<button type="button" id="" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-minus-circle"></i> </button>'+'\n'+
                            '</div>'+'\n'+
                        '</div>');
                        

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

function editDuty(object) {
  var ob = JSON.parse(object);
  $('#id').val(ob.id);
  $('#duty_name').val(ob.name);
  $('#duty_name').focus();
  $('.group-form-btn').val('Update');
}


$('#productsForm').on('click', function(e) {
  e.preventDefault();
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

  function deleteMainSetting(id, rowIndex, row) {
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
                url: "{{url('/destroy/main/setting')}}/" + id,
                data: {_token: CSRF_TOKEN},
                dataType: 'JSON',
                success: function (results) {

                    if (results.success === true) {
                      $('#row_'+row+rowIndex).remove();
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

  
// $('.save-btn').click(function(event) {
//   event.preventDefault();
//   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
//   $.ajax({
//     url: "{{url('/save/duty')}}",
//     type: "POST",
//     data: {_token:CSRF_TOKEN, name:$('#duty_name').val(), id: $('#id').val()},
//     cache: false,
//     beforeSend: function() {
//       $('#savePromoForm').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
//       $('#savePromoForm').prop('disabled', true);
//     },
//     complete: function() {
//       $('#savePromoForm').html('Save');
//       $('#savePromoForm').prop('disabled', false);
//     },
//     error: function(error) {
//       if(error.responseJSON.name) {
//         $('#error_mesge').text('The name field is required.');
//       }
//       setTimeout(() => {
//         $('#error_mesge').text('');
//       }, 3500)
//     }
    
//   }).then(function(resp) {
//     if(resp.status) {
//       $('#m_success').show();
//       $('#m_success').text('Posting added successfully.');
//       setTimeout(() => {
//         location.reload();
//       }, 1000)
//     }else{
//       console.log(resp);
//     }
//   })
// });

</script>

@endpush
<style type="text/css">
  .td-valign{
    vertical-align: middle !important;
  }
</style>
 