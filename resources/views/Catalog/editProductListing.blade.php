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
                                {{-- <div class="align-self-center">
                                    <strong>Awesome Title</strong>
                                </div> --}}
                                <div class="align-self-end float-right">
                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#w5-general" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">General</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-data" role="tab" aria-controls="data" aria-selected="false">Data</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab3" data-toggle="tab" href="#w5-attribute" role="tab" aria-controls="attribute" aria-selected="false">Attribute</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body no-p">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="w5-general" role="tabpanel" aria-labelledby="w5-general">
                                    <div class="card no-b">
                                        <div class="card-header white">
                                            <div class="d-flex justify-content-between">
                                                <div class="align-self-end float-right">
                                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                                        @foreach($stores as $k => $store)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{($k == 0) ? 'active' : ''}} show" id="w5--tab1" data-toggle="tab" href="#w5-{{$store->name}}" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">
                                                                {{$store->name}}
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                        {{-- <li class="nav-item">
                                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-store1" role="tab" aria-controls="tab2" aria-selected="false">Store 2</a>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body no-p">
                                            <div class="tab-content">
                                                @foreach($stores as $k => $store)
                                                <div class="tab-pane fade {{($k == 0) ? 'active' : ''}} show" id="w5-{{$store->name}}" role="tabpanel" aria-labelledby="w5-{{$store->name}}">
                                                   <div class="tab-title text-center text-black pt-2">
                                                       <h5 class="text-black font-weight-bold">{{$store->name}}</h5>
                                                   </div>
                                                    <div class="form-content p-4">
                                                        <form id="{{$store->name}}-form" action="" method="POST">
                                                            {{ csrf_field() }}
                                                            <div class="row">
                                                                <input type="hidden" name="store" value="{{$store->id}}">
                                                                <input type="hidden" name="product_id" value="{{$productList->product_id}}">
                                                                <input type="hidden" name="description_id" value="{{$store->productDescriptions[0]->id}}">
                                                                <label class="col-2 control-lable text-black"><strong> Product Name </strong><span class="text-danger"><b>*</b></span></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_name" value="{{$store->productDescriptions[0]->name}}" id="{{$store->name}}-form-name" class="form-control">
                                                                    <span class="invalid-feedback name-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Description </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="25" cols="118" name="description" class="summernote text-black">{!! $store->productDescriptions[0]->product_description !!}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Title <span class="text-danger">*</span></strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="meta_title" value="{{$store->productDescriptions[0]->meta_title}}" id="{{$store->name}}-form-meta-title" class="form-control">
                                                                    <span class="invalid-feedback meta-title-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Description </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="8" cols="118" name="meta_description" class="text-black">{{ $store->productDescriptions[0]->meta_description }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Keywords </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="5" name="meta_keyword" cols="118">{{$store->productDescriptions[0]->meta_keywords}}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Tags </strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_tags" value="{{$store->productDescriptions[0]->product_tags}}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Price <span class="text-danger">*</span></strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_price" value="{{$store->productDescriptions[0]->price}}" id="{{$store->name}}-form-price" class="form-control">
                                                                    <span class="invalid-feedback price-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <div class="col-12">
                                                                    <button type="button" id="add_manually" value="{{$store->name}}-form" class="btn btn-primary float-right save-description" data-action="">
                                                                        Save
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                             @endforeach
                                            </div>
                    
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade text-center p-5" id="w5-data" role="tabpanel" aria-labelledby="w5-data">
                                    <div class="form-content p-4">
                                        <form id="data-form" action="" method="POST">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <input type="hidden" name="product_id" value="{{$productList->product_id}}">
                                                <label class="col-2 control-lable text-black"><strong> Product SKU </strong><span class="text-danger"><b>*</b></span></label>
                                                <div class="col-10">
                                                    <input type="text" name="product_sku" value="{{$productList->sku}}" id="form-name" readonly class="form-control">
                                                    <span class="invalid-feedback name-error" role="alert">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Minimum Quantity? </strong></label>
                                                <div class="col-10">
                                                    <input type="text" name="minimum_quantity" value="{{$productList->minimum}}" id="form-minimum-quantity" class="form-control">
                                                    <span class="invalid-feedback meta-title-error" role="alert">
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Sort Order </strong></label>
                                                <div class="col-10">
                                                    <input type="text" name="sort_order" value="{{$productList->sort_order}}" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row pt-4">
                                                <div class="col-12">
                                                    <button type="button" id="add_manually" value="data-form" class="btn btn-primary float-right save-data" data-action="">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade text-center p-5" id="w5-attribute" role="tabpanel" aria-labelledby="w5-attribute">
                                    <h4 class="card-title">Attributes</h4>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                    <a href="#" class="btn btn-primary">Attributes <Section></Section></a>
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

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>

 <!-- product location modal start -->
 <div class="modal fade porduct_location_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Add & Edit options</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="porduct_location_content">
          <div class="text-center" id="loader">
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- product location modal end -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js" defer></script>

<script>
 $(document).ready(function() {
          $('.summernote').summernote({
            height: 250,
          });
        });

$('.save-description').on('click', function () {
    var btnForm = $(this).val();
    console.log($(this).form().serialize());
    if(!$('#'+btnForm+'-name').val()) {
        $('.name-error').html('<strong>Product name is required</strong>');
        $('html, body').animate({
            scrollTop: $('.name-error').first().offset().top-200
        }, 500);
        return false;
    }
    if(!$('#'+btnForm+'-meta-title').val()) {
        $('.meta-title-error').html('<strong>Meta tag title is required</strong>');
        $('html, body').animate({
            scrollTop: $('.meta-title-error').first().offset().top-200
        }, 500);
        return false;
    }
    if(!$('#'+btnForm+'-price').val()) {
        $('.price-error').html('<strong>Price is required</strong>');
        $('html, body').animate({
            scrollTop: $('.price-error').first().offset().top-200
        }, 500);
        return false;
    }
    $.ajax(
        {
            url: "{{route('save.listing.description')}}",
            type: 'POST',
            data: $('#'+btnForm).serialize(),
            catche: false,
            success: function(resp) {
                if(resp.status) {
                    console.log(resp);
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

$('.save-data').on('click', function () {
    var btnForm = $(this).form();
    console.log($(this).form().serialize());
    // if(!$('#'+btnForm+'-name').val()) {
    //     $('.name-error').html('<strong>Product name is required</strong>');
    //     $('html, body').animate({
    //         scrollTop: $('.name-error').first().offset().top-200
    //     }, 500);
    //     return false;
    // }
    // $.ajax(
    //     {
    //         url: "{{route('save.listing.description')}}",
    //         type: 'POST',
    //         data: $('#'+btnForm).serialize(),
    //         catche: false,
    //         success: function(resp) {
    //             if(resp.status) {
    //                 console.log(resp);
    //                     $(".toast-action").data('title', 'Action Done!');
    //                     $(".toast-action").data('type', 'success');
    //                     $(".toast-action").data('message', resp.mesge);
    //                     $(".toast-action").trigger('click');
    //                 } else {
    //                     $(".toast-action").data('title', 'Went Wrong!');
    //                     $(".toast-action").data('type', 'error');
    //                     $(".toast-action").data('message', resp.mesge);
    //                     $(".toast-action").trigger('click');
    //                 }
    //         }
    //     }
    // )
});
</script>
@endpush