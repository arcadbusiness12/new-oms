@extends('layouts.app')

@section('content')
<style>
.form-control {
    border: 1px solid #c1c6cb;
}
.badge {
    font-weight: 900;
}


</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12 text-right">
                        <div class="card-header white">

                                        <div class=" ">
                                        <a href="{{route('shipping.method')}}"> <button id="" type="button" class="btn btn-secondary active add-method">
                                            <i class="icon-arrow_back"></i>  back
                                        </button>
                                        </a>
                                        </div>

                        </div>
                </div>
                </div>
            </div>
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        
                        <div class="card-header white">
                            <div class="d-flex justify-content-between">
                                {{-- <div class="align-self-center">
                                    <strong>Awesome Title</strong>
                                </div> --}}
                                <div class="align-self-end float-right">
                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#w5-{{$shippingMethod->store->name}}" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">{{$shippingMethod->store->name}}</a>
                                            </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-content">
                            <div class="tab-content">
                              <div class="tab-pane fade active show text-center p-5" id="w5-{{$shippingMethod->store->name}}" role="tabpanel" aria-labelledby="w5-data">
                                <div class="form-content p-4">
                                    <form id="edit-shipping-form" action="" method="POST">
                                        {{ csrf_field() }}
                                        <div class="row mt-4">  
                                            <label class="col-2 control-lable text-black">Name<span class="text-danger">*</span></label>
                                            <div class="col-10">
                                              <input type="text" name="name[]" id="name" value="{{$shippingMethod->name}}" class="form-control">
                                              <input type="hidden" name="store_id" id="store_id" value="{{$shippingMethod->store->id}}" class="form-control">
                                              <input type="hidden" name="shipping_method_id" id="shipping_method_id" value="{{$shippingMethod->id}}" class="form-control">
                                            </div>
                                          </div>
                                          <div class="row mt-4">  
                                            <label class="col-2 control-lable text-black">Geo Zone<span class="text-danger">*</span></label>
                                            <div class="col-10">
                                            <select class="form-control custom-select" name="geo_zone[]" id="select-country">
                                                <option value="0">All Geo Zones</option>
                                                @foreach($geoZones as $geoZone)
                                                <option value="{{$geoZone->id}}" @selected($geoZone->id == $shippingMethod->geo_zone_id)>{{$geoZone->name}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        @php 
                                        $wDisplayProperty = ''; $aDisplayProperty = '';

                                        @endphp
                                        @if($shippingMethod->shipping_type == 1)
                                           @php $wDisplayProperty = 'none;'; $aDisplayProperty = 'flex;'; 
                                           @endphp
                                        @else 
                                           @php $wDisplayProperty = 'block;'; $aDisplayProperty = 'none;';
                                           @endphp
                                        @endif
                                        <div class="row mt-4">  
                                            <label class="col-2 control-lable text-black">Shipping Type<span class="text-danger">*</span></label>
                                            <div class="col-10">
                                            <select class="form-control custom-select" name="shipping_type" onchange="selectShippingType(this.value)" id="select-country">
                                                <option value="1" @selected($shippingMethod->shipping_type == 1)>Flat</option>
                                                <option value="2" @selected($shippingMethod->shipping_type == 2)>Weight</option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="row mt-4 fixed-amount" style="display:{{$aDisplayProperty}}">  
                                            <label class="col-2 control-lable text-black">Amount</label>
                                            <div class="col-10">
                                                <input type="number" name="amount[]" id="amount" value="{{$shippingMethod->amount}}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="weight-box" style="display: {{$wDisplayProperty}}">
                                        @foreach($shippingMethod->shippingWeightAmounts as $k => $amount)
                                            <div class="row mt-4 weight-rows{{$amount->id}} weight-dynamic-rows">
                                                  <div class="col-2 col-grid">
                                                  </div>
                                                  <div class="col-4 col-grid">
                                                      <label class=" control-lable text-black">Weight {{$amount->additional_amount}}</label>
                                                  <select class="form-control custom-select" name="weight[{{$k}}][]" id="select-weight">
                                                      <option value="0.5" @selected($amount->weight == 0.5)>0.5 kg</option>
                                                      <option value="1" @selected($amount->weight == 1)>1 kg</option>
                                                      <option value="1.5" @selected($amount->weight == 1.5)>1.5 kg</option>
                                                      <option value="2" @selected($amount->weight == 2)>2 kg</option>
                                                      <option value="2.5" @selected($amount->weight == 2.5)>2.5 kg</option>
                                                      <option value="3" @selected($amount->weight == 3)>3 kg</option>
                                                      <option value="3.5" @selected($amount->weight == 3.5)>3.5 kg</option>
                                                      <option value="4" @selected($amount->weight == 4)>4 kg</option>
                                                      <option value="4.5" @selected($amount->weight == 4.5)>4.5 kg</option>
                                                      <option value="5" @selected($amount->weight == 5)>5 kg</option>
                                                  </select>
                                                  </div>
  
                                                  <div class="col-5 col-grid">
                                                      <label class="control-lable text-black">Amount</label>
                                                      <input type="number" name="amount_weight[{{$k}}][]" id="amount_weight" value="{{$amount->amount_weight}}" class="form-control">
                                                  </div>
                                                  <div class="col-1 col-grid" style="padding-top: 22px;">
                                                    <a href="javascript:;" class="btn btn-danger close-btn remove-weight-row" onclick="romovedatabaseWeightrow('{{$amount->id}}')"><i class="icon-close2 " style="font-size:25px"></i></a>
                                                      {{-- <a href="javascript:;" class="btn btn-primary close-btn add-row" data-row="{{$k}}"><i class="icon-plus-circle"></i></a> --}}
                                                  </div>
                                          </div>
                                      @endforeach
                                      </div>
                                      <div class="row mt-4">  
                                          <label class="col-2 control-lable text-black">Additional 1 kg</label>
                                          {{-- <div class="col-2">
                                              <label class="control-lable text-black">1 KG</label>
                                          </div> --}}
                                          <div class="col-10">
                                              <input type="number" name="additional_amount[]" id="additional_amount" value="{{@$shippingMethod->additional_amount}}" class="form-control">
                                          </div>
                                      </div>

                                            {{-- <hr class="" style="height:2px;border-width:0;color:gray;background-color:gray" > --}}

                                        {{-- <div class="new-rows{{$store->id}}"></div> --}}
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-primary float-right update-shipping-method">update Method</button>
                                                <button type="button" class="btn btn-primary close-btn float-right mr-2 add-row" data-row="0" style="display: {{$wDisplayProperty}}">
                                                    <i class="icon-plus-circle"></i>
                                                </button>
                                            
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


<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
<script>
    

$('.update-shipping-method').on('click', function() {
    var store = $(this).data('store');
    $.ajax(
        {
            url: "{{route('save.shipping.method')}}",
            type: 'POST',
            data: $('#edit-shipping-form').serialize(),
            catche: false,
            success: function(resp) {
                if(resp.status) {
                        $(".toast-action").data('title', 'Action Done!');
                        $(".toast-action").data('type', 'success');
                        $(".toast-action").data('message', resp.mesge);
                        $(".toast-action").trigger('click');
                        $('.price-error').html('');
                        $('.meta-title-error').html('');
                        $('.name-error').html('');
                    } else {
                        $(".toast-action").data('title', 'Went Wrong!');
                        $(".toast-action").data('type', 'error');
                        $(".toast-action").data('message', resp.mesge);
                        $(".toast-action").trigger('click');
                    }
            }
        }
    )
});

function selectShippingType(value) {
    console.log(value);
    if(value == 1)  {
        $('.fixed-amount').css('display', 'flex');
        $('.weight-box').css('display', 'none');
        $('.weight-rows').remove();
        $('.add-row').css('display','none');
    }else {
        $('.fixed-amount').css('display', 'none');
        $('.weight-box').css('display', 'block');
        $('.add-row').css('display','inline-block');
    }
}

$(document).ready(function() {
    var btnWeight = $('.add-row');
    var weightWrapper = $('.weight-box');
    var y = 1;

    $(btnWeight).on('click', function() {
        var shipRow = $(this).data('row');
        var row1 = '<div class="row mt-4 weight-row-'+y+' weight-rows"><div class="col-2 col-grid"></div><div class="col-4 col-grid"><label class=" control-lable text-black">Weight</label>'+'\n'+
                                                '<select class="form-control custom-select" name="weight['+shipRow+'][]" id="select-weight">'+'\n'+
                                                    '<option value="0.5">0.5 kg</option><option value="1">1 kg</option><option value="1.5">1.5 kg</option><option value="2">2 kg</option><option value="2.5">2.5 kg</option>'+'\n'+
                                                    '<option value="3">3 kg</option><option value="3.5">3.5 kg</option><option value="4">4 kg</option><option value="4.5">4.5 kg</option><option value="5">5 kg</option></select></div>'+'\n'+
                                                    '<div class="col-5 col-grid"><label class="control-lable text-black">Amount</label>'+'\n'+
                                                    '<input type="number" name="amount_weight['+shipRow+'][]" id="amount_weight" class="form-control"></div>'+'\n'+
                                                    '<div class="col-1 col-grid" style="padding-top: 22px;">'+'\n'+
                                                    '<a href="javascript:;" class="btn btn-danger close-btn remove-weight-row" onclick="romoveWeightrow('+y+')"><i class="icon-close2 " style="font-size:25px"></i></a></div></div></div>';
      $(weightWrapper).append(row1);

    y++;
    });

});
function romoveWeightrow(index) {
    $('.weight-row-'+index).remove();
}

function romovedatabaseWeightrow(id) {
    console.log(id);
    if(id) {
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
        var url = "{{route('destroy.weight.amount', ":id")}}",
        url = url.replace(':id', id);
        $.ajax(
            {
                url: url,
                type: 'GET',
                cache: false,
                success: function(respo) {
                    if(respo.status) {
                        $('.weight-rows'+id).remove();
                        $(".toast-action").data('title', 'Action Done!');
                        $(".toast-action").data('type', 'success');
                        $(".toast-action").data('message', 'Weight amount removed successfully.');
                        $(".toast-action").trigger('click');
                        $('.price-error').html('');
                        $('.meta-title-error').html('');
                        $('.name-error').html('');
                    }else {
                        $(".toast-action").data('title', 'Went Wrong!');
                        $(".toast-action").data('type', 'error');
                        $(".toast-action").data('message', 'Opps! Something went wrong, Try again.');
                        $(".toast-action").trigger('click');
                    }
                }
            }
        )
    } else {
            e.dismiss;
        }

        }, function (dismiss) {
        return false;
        })
  }
}
</script>
@endpush
