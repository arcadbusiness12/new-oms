@extends('layout.theme')
@section('title', 'Home')
@section('content')
<style>
    /* .duty-box {
        margin-bottom: 20px;
    } */
    
    .assign-duty-div {
        position: relative;overflow-x: hidden;
    }
    .duty-section {
        margin-bottom: 12px;
    }
    .action-btn{
        display: inline-block;
        float: left;

    }
    #detail-tag {
        text-align: justify;
    }
    .modal-content-loader {
        margin-left: 265px !important;
    }
    .modal .modal-content .modal-body {
    padding: 2px 25px;
    }
</style>
<section class="content">
    <div class="container-fluid">
        {{-- tab code start=============== --}}
        <div class="container" style="width:1560px">
       

              <!-- Duties Tab Start  -->
                      {{ csrf_field() }}
                      <div class="col-sm-12">
                              <?php if(Session::has('message')) { ?>
                              <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                  <?php echo Session::get('message') ?>
                              </div>
                              <?php } ?>
                          </div>
                      <div class="row ">
                        <div class="col-12">
                          <div class="col-sm-3 col-sm-3 col-xs-12">
                              <div class="card" style="padding: 15px;overflow: hidden;">
                                          <label class="form-label">To Do</label>
                                          <div class="assign-duty-div {{count($not_started) > 0 ? 'box-extend ': '' }}">
                                          @foreach($not_started as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                                <div class="col-sm-10">
                                                    <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">Start:</label> {{$duty->start_date}}
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">End:</label> {{$duty->end_date}}
                                                </div>
                                                @foreach($duty['files'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                                                    @if(in_array($file->extension, $extensions)) 
                                                    @php $flag = false; @endphp
                                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}}; height:{{(count($duty['files']) > 1) ? '100px' : 'auto'}}; padding: 10px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a>
                                                    @else
                                                    @php $flag = true; @endphp
                                                    <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a>
                                                    @endif
                                                    
                                                    @if($flag)
                                                    <h5>Download your file </h5>
                                                    @endif
                                                    </div>
                                                    @endforeach
                                                
                                               
                                                
                                            </div>
                                            <div class="col-sm-6">
                                            <button type="button" class="btn btn-info btn-block action-btn" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 0)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-success btn-block action-btn" onclick="dutyDetails('{{$duty->id}}')" data-toggle="modal" data-target="#detailModal">Details</button>
                                            </div>
                                            </div> 
                                            
                                                <hr style="width: 85%;" size="2">
                                            @endforeach
                                                 
                                          </div>
                              </div>
                          </div>

                          
                          <div class="col-sm-3 col-sm-3 col-xs-12">
                              <div class="card" style="padding: 15px;overflow: hidden;">
                                  <div class="row">
                                      <div class="col-sm-12">
                                          <label class="form-label">Doing</label>
                                          <div class="assign-duty-div {{count($started) > 0 ? 'box-extend' : '' }}">
                                          @forelse($started as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                                <div class="col-sm-10">
                                                    <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">Start:</label> {{$duty->start_date}}
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">End:</label> {{$duty->end_date}}
                                                </div>
                                                @foreach($duty['files'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                                                    @if(in_array($file->extension, $extensions)) 
                                                    @php $flag = false; @endphp
                                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}}; height:{{(count($duty['files']) > 1) ? '100px' : 'auto'}}; padding: 10px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a>
                                                    @else
                                                    @php $flag = true; @endphp
                                                    <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a>
                                                    @endif
                                                    
                                                    @if($flag)
                                                    <h5>Download your file </h5>
                                                    @endif
                                                    </div>
                                                    @endforeach
                                                
                                               
                                                
                                            </div>
                                            <button type="button" class="btn btn-success btn-block" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 1)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div> 
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>

                                      </div>
                                  </div>
                              </div>
                          </div>

                          
                          <div class="col-sm-3 col-sm-3 col-xs-12">
                              <div class="card" style="padding: 15px;overflow: hidden;">
                                  <div class="row">
                                      <div class="col-sm-12">
                                          <label class="form-label">Testing</label>
                                          <div class="assign-duty-div {{count($in_testing) > 0 ? 'box-extend' : '' }}">
                                          @forelse($in_testing as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                                <div class="col-sm-10">
                                                    <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">Start:</label> {{$duty->start_date}}
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">End:</label> {{$duty->end_date}}
                                                </div>
                                                @foreach($duty['files'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                                                    @if(in_array($file->extension, $extensions)) 
                                                    @php $flag = false;  @endphp
                                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}}; height:{{(count($duty['files']) > 1) ? '100px' : 'auto'}}; padding: 10px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a>
                                                    
                                                    @else
                                                    @php $flag = true; @endphp
                                                    <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a>
                                                    @endif
                                                    
                                                    @if($flag)
                                                    <h5>Download your file </h5>
                                                    @endif
                                                    </div>
                                                    @endforeach
                                                
                                               
                                                
                                            </div>
                                            <button type="button" class="btn btn-success btn-block" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 2)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div> 
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="col-sm-3 col-sm-3 col-xs-12">
                              <div class="card" style="padding: 15px;overflow: hidden;">
                                  <div class="row">
                                      <div class="col-sm-12">
                                          <label class="form-label">Completed</label>
                                          <div class="assign-duty-div {{count($completed) > 0 ? 'box-extend' : '' }}">
                                          @forelse($completed as $k => $duty)
                                            <div class="row duty-box">
                                            <div class="duty-section col-sm-12">
                                                <div class="col-sm-10">
                                                    <h4 id="main">{{$duty->title}}</h4>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">Start:</label> {{$duty->start_date}}
                                                </div>
                                                <div class="col-sm-6">
                                                    <label id="main">End:</label> {{$duty->end_date}}
                                                </div>
                                                @foreach($duty['files'] as $k => $file)
                                                @php $flag = false; @endphp
                                                    <div class="old-file-div{{$k}}{{$file->id}}" style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}};position: relative; left: 0; top: 0;display:inline-block; float:left;border: 1px solid gainsboro;">
                                                    @if(in_array($file->extension, $extensions)) 
                                                    @php $flag = false; @endphp
                                                    <a href='javascript:;' onclick='popupImg("{{$k}}{{$file->id}}")'><img id='img-src{{$k}}{{$file->id}}' style="width: {{(count($duty['files']) > 1) ? '150px' : '100%'}}; max-height:{{(count($duty['files']) > 1) ? '100px' : '370px'}}; padding: 10px;position: relative;top: 0;left: 0;max-height: 400px;" src="{{asset($file->file)}}" id="image-tag0"></a>
                                                    @else
                                                    @php $flag = true; @endphp
                                                    <a href="{{asset($duty->file)}}" download><i class="fa fa-download"></i>Dwonload</a>
                                                    @endif
                                                    
                                                    @if($flag)
                                                    <h5>Download your file </h5>
                                                    @endif
                                                    </div>
                                                    @endforeach
                                                
                                               
                                                
                                            </div>
                                            <button type="button" class="btn btn-success btn-block" onclick="changeStatus('{{$duty->id}}','{{$duty->title}}', 5)" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div> 
                                                <hr style="width: 85%;" size="2">
                                            @empty
                                            <div class="col-sm-12 text-center">
                                                    No duty..
                                                </div>
                                            @endforelse
                                                 
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                </div>

              <!-- Duties Tab End  -->
              <div id="tab3" class="tab-pane fade">
                <h3>Tab 3</h3>
                <p>Content for tab 3.</p>
              </div>
            </div>
            <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">              
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <img src="" class="imagepreview" style="width: 100%;height: 100%;" >
                    </div>
                    </div>
                </div>
           </div>

           <!-- Change status Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Progress Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="duty_status_form">
                            {{csrf_field()}}
                         <div class="col-sm-12">
                             <input type="hidden" name="duty_id" id="duty_id">
                                <div class="form-group form-float">
                                <label class="form-label">Status</label><span style="color: red;">*</span>    
                                        <div class="form-line">
                                            <select name="status" class="form-control show-tick">
                                               
                                                <option value="" >Select status</option>
                                                <option value="0" >Not Started</option>
                                                <option value="1" >Started</option>
                                                <option value="2" >Testing</option>
                                                <option value="5" >Completed</option>
                                                
                                            </select>
                                        </div> 
                                        @if($errors->has('status'))
                                        <span class="invalid-response" role="alert">{{$errors->first('status')}}</span>
                                        @endif
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="form-line">
                                        <textarea name="comment" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                    <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary change-btn">Save changes</button>
                    </div>
                    
                    </form>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Details Modal -->
                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-body">
                        
                        <div class="modal-content-loader"></div>
                        <div class="duty-details"></div>
                       
                    <div class="modal-footer">
                            <span class="text-right" id="error_mesge" style="color:red;">  </span>
                            <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary change-btn">Save changes</button>
                    </div>
                    </div>
                    </div>
                </div>
                </div>
        </div>
        {{-- tab code end=============== --}}
    </div>
</section>
@endsection
@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/css/purchase.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>

<script>
     function popupImg(index) {
     console.log($('#img-src'+index).attr('src'));
     $('.imagepreview').attr('src', $('#img-src'+index).attr('src'));
			$('#imagemodal').modal('show');   
 }
    function calculatePoints(index) {
        var wattage = $('.point-box-'+index).val();
        var quantity = $('.quantity-box-'+index).val();
        var points = wattage/quantity;
        $('.calculated-point-box-'+index).val(points.toFixed(2));
        console.log(points);
    }
    $(document).ready(function(e) {
        setTimeout(() => {
            $('.alert').css('display', 'none');
        },5000);
    });
    function addPoints(index) {
        console.log($('select.duration-box-'+index).val());
        // var checkBox = document.getElementById("myCheck");
        if($('.duty-box-'+index).prop('checked')) {
            $('.point-box-'+index).val(0);
            $('.quantity-box-'+index).val(0);
            $('.btn-group').removeAttr('disabled');
            $('.btn-default').removeAttr('disabled');
            $('.duration-box-'+index).prop('disabled', false);
            $('.quantity-box-'+index).prop('disabled', false);
            var duration = $('select.duration-box-'+index).val();
            if(!duration){
                // var selector = 'select.duration-box-'+index;
                // $(selector+' option[value="0"]').prop('selected', true);
                $('select.duration-box-'+index).val(0).find("option[value='0']").attr('selected', true);
            }
        }else {
            $('.point-box-'+index).val('');
            $('.quantity-box-'+index).val('');
            $('.duration-box-'+index).prop('disabled', true);
            $('.quantity-box-'+index).prop('disabled', true);
            // $('op-'+index+' option[value="0"]').prop('selected', false);
            // $('select.duration-box-'+index+" option:selected").prop("selected", false);
            $("select.duration-box-"+index+ " option:selected").each(function () {
               $(this).removeAttr('selected'); 
               });
        }
        
    }

    function changeStatus(id, title, status) {
        console.log(id+'=='+ title);
        $('#exampleModalLongTitle').html(title);
        $('#duty_id').val(id);
    }

    function dutyDetails(duty) {
        // var contents = JSON.parse(duty_details);
        // $('#detailModalTitle').html(contents.title);
        // $('#detail-tag').html(contents.description);
        // $('#dutyId').val(contents.id);
        
        console.log(duty);
        if(duty) {
            $.ajax({
                url: "{{url('/get/custom/duty/details')}}/"+duty,
                type: "GET",
                cache: false,
                beforeSend: function() {
                    $('.modal-content-loader').css('display', 'block');
                },
                complete: function() {
                    $('.modal-content-loader').css('display', 'none');
                },
                error: function(error) {
                    console.log(error);
                }
            }).then(function(resp) {
                console.log(resp);
                
                $('.modal-content-loader').css('display', 'none');
             $('.duty-details').html(resp);
            });
        }
    }
    $('.change-btn').click(function(event) {
  event.preventDefault();
  console.log($('#duty_status_form').serialize());
  $.ajax({
    url: "{{url('/changes/duty/status')}}",
    type: "POST",
    data: $('#duty_status_form').serialize(),
    cache: false,
    beforeSend: function() {
      $('#savePromoForm').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
      $('#savePromoForm').prop('disabled', true);
    },
    complete: function() {
      $('#savePromoForm').html('Save changes');
      $('#savePromoForm').prop('disabled', false);
    },
    error: function(error) {
      if(error.responseJSON.status) {
        $('#error_mesge').text('Please select status.');
      }
      setTimeout(() => {
        $('#error_mesge').text('');
      }, 3500)
    }
    
  }).then(function(resp) {
    if(resp.status) {
      $('#m_success').show();
      $('#m_success').text('Duty status changed successfully.');
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