@extends('layouts.app')

@section('content')
<style>
    /* .th  {
        font-size: 16px !important;
    } */
    .form-control {
    border: 1px solid #c1c6cb;
}
.modal-lg{
  			max-width: 1000px !important;
		}
</style>
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
                                            <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#w5-images" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">Images</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link show" id="w5--tab1" data-toggle="tab" href="#w5-general" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">General</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-data" role="tab" aria-controls="data" aria-selected="false">Data</a>
                                        </li>
                                        <li class="nav-item">
                                            {{-- <a class="nav-link" id="w5--tab3" data-toggle="tab" href="#w5-attribute" role="tab" aria-controls="attribute" aria-selected="false">Attribute</a> --}}
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab4" data-toggle="tab" href="#w5-specail-price" role="tab" aria-controls="specail-price" aria-selected="false">Special</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab4" data-toggle="tab" href="#w5-seo-url" role="tab" aria-controls="seo-url" aria-selected="false">SEO</a>
                                        </li>
                                        
                                    </ul>
                                </div>
                                <div class="align-self-center">
                                   <span style="text-shadow: 2px 4px 6px #00bef3;"> <strong>{{@$store->name}}</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body no-p">
                            <div class="tab-content">

                                <div class="tab-pane fade  active show text-center p-5" id="w5-images" role="tabpanel" aria-labelledby="w5-images">
                                    
                                    <div class="col-4 col-grid border-right border-light" style="border-right: 2px dotted;">
                                        {{-- <canvas id="canvas" width="360" height="240"></canvas> --}}
                                        <div class="img-box" id="img-box" style="position: absolute;top:0;left:0">
                                            <h4 class="card-title">Feature Image</h4>
                                            <img src="{{URL::asset('uploads/inventory_products/'.$productList->image)}}" class="img-thumbnail featured-img" style="position:relative;top:0;left:0;" id="feature-image">
                                            <input type="button" name="images" id="change_featured_img_btn" class="btn btn-success btn-block change-featured-img-btn " value="Change Image" style="">
                                            <input type="file" name="images" class="featured-img-btn btn btn-primary btn-block" id="upload-file" style="
                                            position:absolute;  bottom: 2px;
                                            /* top: 406px; */
                                            /* left: 95px; */
                                            z-index: 2;
                                            opacity: 0;" 
                                            value="Upload" >
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-8 col-grid">
                                        <h4 class="card-title">Gallery Images</h4>
                                        <div class="row">
                                          @foreach($productList->productImages as $image)
                                            <div class="col-4 col-grid mt-2" id="imge-box{{$image->id}}">
                                                <img src="{{URL::asset('uploads/product_gallery/'.$image->image)}}" class="img-thumbnail featured-img" id="{{$image->id}}" style="position: relative">
                                                <a href="javascript:;"> <i class="icon icon-filter_b_and_w icon-apply-filter" style="font-size: 22px;background-color: white; color:green; position: absolute;  top: 10px;
                                                float: right; 
                                                font-weight: 900;
                                                left: 55px;" data-src="{{URL::asset('uploads/product_gallery/'.$image->image)}}" data-id="{{$image->id}}" id="data-src-{{$image->id}}"></i></a>
                                               <a href="javascript:;" class="remove-gallery-image" data-id="{{$image->id}}"> <i class="icon icon-trash" style="font-size: 22px; color:red; position: absolute;top: 10px;
                                                float: right;
                                                font-weight: 900;background-color: white;
                                                left: 30px; "></i></a>
                                            </div>
                                          @endforeach
                                          
                                          <div class="col-4 col-grid mt-2">
                                        </div>

                                        <div class="new-gallery-imgs" id="new-gallery-imgs">

                                        </div>
                                    </div>
                                    <div class="row mt-4 gallery-imgs-box">
                                        <input type="button" name="images" id="gallery_image" class="btn btn-primary gallery-img-btn" value="Gallery Images" style="">
                                        <input type="file" name="images" class="btn btn-primary" value="gallery image" id="upload-gallery-file" style="position: absolute;
                                        opacity: 0;" multiple>
                                    </div>
                                    </div> 
                                </div>

                                <div class="tab-pane fade" id="w5-general" role="tabpanel" aria-labelledby="w5-general">
                                  {{-- @foreach($productList->productDescriptions as $k => $description) --}}
                                    <div class="card no-b">
                                        <div class="card-body no-p">
                                            <div class="tab-content">
                                                {{-- <div class="tab-pane fade {{($k == 0) ? 'active' : ''}} show" id="w5-{{$description->store->name}}" role="tabpanel" aria-labelledby="w5-{{$description->store->name}}"> --}}
                                                   <div class="tab-title text-center text-black pt-2">
                                                       {{-- <h5 class="text-black font-weight-bold">{{$description->store->name}}</h5> --}}
                                                   </div>
                                                    <div class="form-content p-4">
                                                        <form id="{{@$store->name}}-form" action="" method="POST">
                                                            {{ csrf_field() }}
                                                            <div class="row">
                                                                <input type="hidden" name="store" value="{{$store->id}}">
                                                                <input type="hidden" id="product_id" name="product_id" value="{{@$productList->product_id}}">
                                                                <input type="hidden" name="description_id" value="{{@$productList->productDescriptions[0]->id}}">
                                                                <label class="col-2 control-lable text-black"><strong> Product Name </strong><span class="text-danger"><b>*</b></span></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_name" value="{{@$productList->productDescriptions[0]->name}}" id="{{@$store->name}}-form-name" class="form-control product-title">
                                                                    <input type="hidden" id="seourl" name="seourl" value="{{@$productList->productDescriptions[0]->seoUrls}}">
                                                                    <span class="invalid-feedback name-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Description </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="25" cols="118" name="description" class="summernote text-black">{!! @$productList->productDescriptions[0]->product_description !!}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Title <span class="text-danger">*</span></strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="meta_title" value="{{@$productList->productDescriptions[0]->meta_title}}" id="{{@$store->name}}-form-meta-title" class="form-control">
                                                                    <span class="invalid-feedback meta-title-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Description </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="8" cols="118" name="meta_description" class="text-black">{{ @$productList->productDescriptions[0]->meta_description }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Meta Tag Keywords </strong></label>
                                                                <div class="col-10">
                                                                    <textarea rows="5" name="meta_keyword" cols="118">{{@$productList->productDescriptions[0]->meta_keywords}}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Tags </strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_tags" value="{{@$productList->productDescriptions[0]->product_tags}}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="row pt-4">
                                                                <label class="col-2 control-lable text-black"><strong> Product Price <span class="text-danger">*</span></strong></label>
                                                                <div class="col-10">
                                                                    <input type="text" name="product_price" value="{{@$productList->productDescriptions[0]->price}}" id="{{@$store->name}}-form-price" class="form-control">
                                                                    <span class="invalid-feedback price-error" role="alert">
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="row pt-4">
                                                                <div class="col-12">
                                                                    <button type="button" id="add_manually" value="{{@$store->name}}-form" class="btn btn-primary float-right save-description" data-action="">
                                                                        Save
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                {{-- </div> --}}
                                            </div>
                    
                                        </div>
                                    </div>
                                  {{-- @endforeach --}}
                                </div>
                                <div class="tab-pane fade text-center p-5" id="w5-data" role="tabpanel" aria-labelledby="w5-data">
                                    <div class="form-content p-4">
                                        <form id="{{@$store->name}}-data-form" action="" method="POST">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <input type="hidden" name="product_id" value="{{$productList->product_id}}">
                                                <label class="col-2 control-lable text-black"><strong> Product SKU </strong><span class="text-danger"><b>*</b></span></label>
                                                <div class="col-10">
                                                    <input type="text" name="product_sku" value="{{$productList->sku}}" id="{{@$store->name}}-data-form-sku" readonly class="form-control">
                                                    <span class="invalid-feedback sku-error" role="alert">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Minimum Quantity? </strong></label>
                                                <div class="col-10">
                                                    <input type="text" name="minimum_quantity" value="{{$productList->minimum_quantity}}" id="form-minimum-quantity" class="form-control">
                                                    <span class="invalid-feedback meta-title-error" role="alert">
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Weight </strong></label>
                                                <div class="col-10">
                                                    <input type="text" name="weight" value="{{$productList->weight}}" id="form-weight" class="form-control">
                                                    <span class="invalid-feedback meta-title-error" role="alert">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Weight Class </strong></label>
                                                <div class="col-10">
                                                    
                                                    <select name="weight_class" class="form-control custom-select">
                                                        @foreach($weightClasses as $weightClass)
                                                        <option value="{{$weightClass->id}}" @selected($weightClass->id == $productList->weight_class_id)>{{$weightClass->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Sort Order </strong></label>
                                                <div class="col-10">
                                                    <input type="text" name="sort_order" value="{{$productList->sort_order}}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row pt-4">
                                                <label class="col-2 control-lable text-black"><strong> Status </strong></label>
                                                <div class="col-10">
                                                    
                                                    <select name="status" class="form-control custom-select">
                                                        <option value="2" @selected($productList->status == 2)>Finish</option>
                                                        <option value="1" @selected($productList->status == 1)>Enable</option>
                                                        <option value="0" @selected($productList->status == 0)>Disable</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row pt-4">
                                                <div class="col-12">
                                                    <button type="button" id="add_manually" value="{{@$store->name}}-data-form" class="btn btn-primary float-right save-data" data-action="">
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

                                <div class="tab-pane fade text-center p-5" id="w5-specail-price" role="tabpanel" aria-labelledby="w5-specail-price">
                                    <h4 class="card-title">Specail Price</h4>
                                    <form id="special-price-form" action="" method="POST">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <input type="hidden" name="store" value="{{@$store->id}}">
                                            <input type="hidden" name="product_id" value="{{$productList->product_id}}">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="card no-b">
                                                      <div class="table-responsive">
                                                       <div id="status_changed_msg" style="display: none"></div>
                                                        <table class="table" width="100%" style="border: 1px solid #3f51b5">
                            
                                                         <thead >
                            
                                                          <tr
                                                          style="background-color: #3f51b5;color:white"
                                                          >
                                                            <th class="th"><center>Priority </center></th>
                                                            <th class="th"><center>Price</center></th>
                                                            <th scope="col" class="th"><center>Date Start</center></th>
                                                            <th scope="col" class="th"><center>Date End</center></th>
                                                            <th scope="col" class="th"><center>Action</center></th>
                            
                                                           </tr>
                            
                                                         </thead>
                                                         <tbody class="table-body">
                                                             @if(count($productList->productSpecials) > 0)
                                                             @foreach($productList->productSpecials as $specialPrice)
                                                            <tr id="row-{{$specialPrice->id}}">
                                                                <td>
                                                                    <input type="number" name="sort_order[]" value="{{$specialPrice->sort_order}}" id="form-minimum-quantity" class="form-control" autocomplete="off">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="price[]" value="{{$specialPrice->price}}" id="form-minimum-quantity" class="form-control" autocomplete="off">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="start_date[]" value="{{$specialPrice->date_start}}" id="start_date" autocomplete="off" class="date-time-picker form-control"
                                                                    data-options='{"timepicker":false, "format":"Y-m-d"}'>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="end_date[]" value="{{$specialPrice->date_end}}" id="form-minimum-quantity" autocomplete="off" class="date-time-picker form-control"
                                                                    data-options='{"timepicker":false, "format":"Y-m-d"}'>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" class="remove-row" onclick="reomveOldRow({{$specialPrice->id}})"><i class="icon-close2 text-danger-o text-danger" style="font-size:25px"></i></a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else 
                                                            <tr>
                                                                <td>
                                                                    <input type="number" name="sort_order[]" value="" id="form-minimum-quantity" class="form-control" autocomplete="off">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="price[]" value="" id="form-minimum-quantity" class="form-control" autocomplete="off">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="start_date[]" value="" id="start_date" autocomplete="off" class="date-time-picker form-control"
                                                                    data-options='{"timepicker":false, "format":"Y-m-d"}'>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="end_date[]" value="" id="form-minimum-quantity" autocomplete="off" class="date-time-picker form-control"
                                                                    data-options='{"timepicker":false, "format":"Y-m-d"}'>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                         </tbody>
                            
                                                </table>
                            
                                                </div>
                            
                                        </div>
                            
                            
                                                </div>
                                            </div>
                                        <div class="row pt-4">
                                            <div class="col-12">
                                                <button type="submit" id="special-price" value="special-price" class="btn btn-primary float-right">
                                                    Save
                                                </button>
                                                
                                                <button type="button" id="add-more" class="btn btn-success float-right">
                                                    <i class="icon icon-plus"> </i> More
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade text-center p-5" id="w5-seo-url" role="tabpanel" aria-labelledby="w5-seo-url">
                                    <h4 class="card-title">SEO Url</h4>
                                    @if(count($productList->seoUrls) > 0)
                                    @foreach($productList->seoUrls as $seoUrl)
                                        <input type="text" name="seo_url" readonly value="{{$seoUrl->seo_url}}" id="seo_url"  class="form-control">
                                    @endforeach
                                    @else 
                                        <input type="text" name="seo_url" id="seo_url" value="" placeholder="Seo Url" class="form-control" readonly>
                                    @endif
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

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Image Filter</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="img-container">
                <div class="row">
                    <div class="col-md-8 m-auto">
                      {{-- <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="upload-file1">
                        <label for="upload-file" class="custom-file-label">Choose Image</label>
                      </div> --}}
                      <canvas id="canvas" style="width: 100%"></canvas>
                    </div>
                    
                    <div class="col-md-4">
                       
                            
                      <h4 class="text-center my-3">Filters</h4>
                        
                      <div class="row my-4 text-center">
                       

                        <div class="col-md-6 mt-2">
                          <div class="btn-group btn-group-sm">
                            <label for="bright">Brightness</label>
                          </div>
                          <div class="btn-group btn-group-sm">
                            <input id="bright" name="bright" type="range" min="-50" max="50" value="0">
                          </div>
                        </div>
              
                        <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="bright">Contrast</label>
                              </div>
                              <div class="btn-group btn-group-sm">
                                <input id="contrast" name="contrast" type="range" min="-100" max="100" value="0">
                              </div>
                        </div>
              
                        <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="bright">Gamma</label>
                              </div>
                              <div class="btn-group btn-group-sm">
                                <input id="gamma" name="gamma" type="range" min="1" max="10" value="1">
                              </div>
                        </div>
              
                        <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="saturation">Saturation</label>
                              </div>
                              <div class="btn-group btn-group-sm">
                                <input id="saturation" name="saturation" type="range" min="-100" max="100" value="0">
                              </div>
                          </div>

                        <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="vibrance">Vibrance</label>
                              </div>
                              <div class="btn-group btn-group-sm">
                                <input id="vibrance" name="vibrance" type="range" min="-100" max="100" value="0">
                              </div>
                        </div>

                        {{-- <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="hue">Hue Rotate</label>
                              </div>
                            <div class="btn-group btn-group-sm"> --}}
                              {{-- <button class="filter-btn hue-remove btn btn-info">-</button>
                              <button class="btn btn-secondary btn-disabled" disabled>Hue</button>
                              <button class="filter-btn hue-add btn btn-info">+</button> --}}
                              {{-- <input type="range" id="hue" min="0" max="100" step="1" value="0" data-filter="hue">
                            </div>
                          </div> --}}
                          {{-- <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="blur">Blur</label>
                              </div>
                            <div class="btn-group btn-group-sm"> --}}
                              {{-- <button class="filter-btn hue-remove btn btn-info">-</button>
                              <button class="btn btn-secondary btn-disabled" disabled>Hue</button>
                              <button class="filter-btn hue-add btn btn-info">+</button> --}}
                              {{-- <input type="range" id="blur" min="0" max="100" step="1" value="0">
                            </div>
                          </div> --}}

                          <div class="col-md-6 mt-2">
                            <div class="btn-group btn-group-sm">
                                <label for="exposure">Exposure</label>
                              </div>
                            <div class="btn-group btn-group-sm">
                              <input type="range" id="exposure" min="-100" max="100" step="1" value="0">
                            </div>
                          </div>
                      {{-- </div> --}}
                      <!-- ./row -->
              
                      <h4 class="text-center my-3">Effects</h4>
              
                      <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="filter-btn clarity-add btn btn-dark btn-block">
                                Clarity
                            </button>
                          </div>
                        {{-- <div class="col-md-6">
                          <button class="filter-btn vintage-add btn btn-dark btn-block">
                              Vintage
                            </button>
                        </div> --}}
                        <div class="col-md-6">
                            <button class="filter-btn lomo-add btn btn-dark btn-block">
                                Lomo
                              </button>
                          </div>
                      </div>
              
                      {{-- <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="filter-btn lomo-add btn btn-dark btn-block">
                                Lomo
                              </button>
                          </div>
                          
                        <div class="col-md-6">
                            <button class="filter-btn sincity-add btn btn-dark btn-block">
                                Sin City
                              </button>
                          </div>
                        
                      </div> --}}

                      <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="filter-btn crossprocess-add btn btn-dark btn-block">
                                Cross Process
                              </button>
                          </div>
                          {{-- <div class="col-md-6">
                            <button class="filter-btn pinhole-add btn btn-dark btn-block">
                                Pinhole
                              </button>
                          </div> --}}
                      </div>
                      
                      {{-- <div class="row">
                        <div class="col-md-6">
                            <button class="filter-btn hermajesty-add btn btn-dark btn-block">
                                Her Majesty
                              </button>
                          </div>
                          <div class="col-md-6">
                            <button class="filter-btn nostalgia-add btn btn-dark btn-block">
                                Nostalgia
                              </button>
                          </div>
                      </div> --}}
                      <div class="row">
                        <div class="col-md-6  btn-group-sm" style="float: left;
                        background-color: gainsboro;">
                              <h5 for="bright" style="margin-top: 4px;
                              margin-bottom: 6px;font-weight: 800;">Crop Image</h5>
                        </div>
                            
                        <div class="col-md-6 btn-group-sm" style="    float: left;
                        text-align: right;
                        background-color: gainsboro;
                        /* top: 1px; */
                        padding: 1.5px;">
                              <input type="checkbox" class="crop-filter" id="crop-filter" style="margin-top: 5px;">
                        </div>

                        <div class="col-md-12" style="    padding-top: 15px;
                        right: 14px;">
                            <img src="" id="croped_image" />
                        </div>
                        <img src="" id="orignal-image" style="display: none;" />
                        <input type="hidden" name="image_id" id="image_id">
                      </div>
                      <div class="row mt-5">
                        <div class="col-md-6">
                          <button id="download-btn" class="btn btn-primary">Download Image</button>
                        </div>
                        <div class="col-md-6">
                          <button id="revert-btn" class="btn btn-danger btn-block">Reset Filters</button>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="crop" class="btn btn-primary crop-upload">Crop</button>
                <button type="button" id="saveBase64" class="btn btn-primary simple-upload" >Upload</button>
              <button type="button" class="btn btn-secondary close-modal" id="close-modal" data-dismiss="modal">Cancel</button>
            </div>
      </div>
    </div>
</div>	

<div class="modal fade" id="cropImage" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Crop Image</h5>
              <button type="button" class="close-second-btn" >
                    <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="img-container">
                <div class="row">
                    <div class="col-md-8">
                        <img src="" id="" />
                    </div>
                    <div class="col-md-4">
                        <div class="preview">
                            <img src="" id="cropped-base64" style="visibility: hidden;">
                        </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="crop" class="btn btn-primary crop-upload">Crop</button>
                {{-- <button type="button" id="saveBase64" class="btn btn-primary simple-upload" >Upload</button> --}}
              <button type="button" class="btn btn-secondary close-second-btn">Cancel</button>
            </div>
      </div>
    </div>
</div>	 
  <!-- product location modal end -->
@endsection
  
@push('scripts') 
{{-- <script src="https://jsuites.net/v4/jsuites.js"></script>
<script src="https://jsuites.net/v4/jsuites.layout.js"></script> --}}
  <link rel="stylesheet" href="{{URL::asset('assets/css/cropper.min.css')}}">
  {{-- <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.min.css"> --}}
  <script type="text/javascript" src="{{URL::asset('assets/js/caman.min.js') }}"></script>
  <script type="text/javascript" src="{{URL::asset('assets/js/cropper.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js" defer></script>

<script>
    $('.close-second-btn').on('click', function() {
        $('#cropImage').modal('toggle');
        $(document).find('#cropImage').on('hidden.bs.modal', function () {
            console.log('hiding child modal');
            $('body').addClass('modal-open');
        });
        $('#crop-filter').prop("checked", false);
        $('.crop-upload').attr('disabled', true);
    });
    $('#close-modal, .close').on('click', function() {
         $('#image_id').val('');
         var canvas = document.getElementById("canvas");
         const context = canvas.getContext('2d');
         context.clearRect(0, 0, canvas.width, canvas.height);
         context.beginPath();
         canvas.removeAttribute("data-caman-id");
         $('#croped_image').attr('src', '');
         $('#crop-filter').prop('checked', false);
        $('#modal').modal('hide');
    });
    $('.icon-apply-filter').on('click', function() {
        var image = $(this).data('src');
        $('#modal').modal('show');
        var canvas = document.getElementById("canvas");
        var context = canvas.getContext("2d");

        var imge = new Image();
        imge.onload = function() {
                    canvas.width = imge.width;
                    canvas.height = imge.height;
                    context.drawImage(imge, 0, 0, imge.width, imge.height);
                }
        imge.src = image;
        $('#image_id').val($(this).data('id'));
    });

    $(document).ready(function() {
        var $modal = $('#modal');
        var canvas = document.getElementById('canvas');
            var ctx = canvas.getContext('2d');
            var undoImg;
            var img = new Image();
            loadImage = function(src){

                img.crossOrigin = '';
                img.src = src;

                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                }
            }

      var $saveBase64 = $('#saveBase64');
      var $cropFilter = $('#crop-filter');
        const downloadBtn = document.getElementById("download-btn");
        const uploadFile = document.getElementById("upload-file");
        const revertBtn = document.getElementById("revert-btn");

        document.addEventListener("click", e => {
            console.log("Added");
            if (e.target.classList.contains("filter-btn")) {
              if (e.target.classList.contains("vintage-add")) {
                Caman("#canvas", img, function() {
                    this.vintage().render();
                });
                } else if (e.target.classList.contains("lomo-add")) {
                Caman("#canvas", img, function() {
                    this.lomo().render();
                });
                } else if (e.target.classList.contains("clarity-add")) {
                Caman("#canvas", img, function() {
                    this.clarity().render();
                });
                } else if (e.target.classList.contains("sincity-add")) {
                Caman("#canvas", img, function() {
                    this.sinCity().render();
                });
                } else if (e.target.classList.contains("crossprocess-add")) {
                Caman("#canvas", img, function() {
                    this.crossProcess().render();
                });
                } else if (e.target.classList.contains("pinhole-add")) {
                Caman("#canvas", img, function() {
                    this.pinhole().render();
                });
                } else if (e.target.classList.contains("nostalgia-add")) {
                Caman("#canvas", img, function() {
                    this.nostalgia().render();
                });
                } else if (e.target.classList.contains("hermajesty-add")) {
                Caman("#canvas", img, function() {
                    this.herMajesty().render();
                });
                }
            }
            });

            // Apply Effects 
            
            $(document).on('change', 'input[type=range]', function() {
                var brght = parseInt($('#bright').val());
                var contrast = parseInt($('#contrast').val());
                var gamma = parseInt($('#gamma').val());
                var saturation = parseInt($('#saturation').val());
                var vibrance = parseInt($('#vibrance').val());
                var hue = parseInt($('#hue').val());
                var blur = parseInt($('#blur').val());
                var exposure = parseInt($('#exposure').val());
                Caman('#canvas', img, function() {
                    this.revert(false);
                    this.brightness(brght).contrast(contrast).gamma(gamma).saturation(saturation).vibrance(vibrance).exposure(exposure).render();
                });
            });

            // Revert Filters
            revertBtn.addEventListener("click", e => {
                $('input[type=range]').val(0);
                Caman("#canvas", img, function() {
                    this.revert(false);
                    this.render();
                });
            });
            $saveBase64.on('click', function(e) {
                var id = $('#image_id').val()
                var can = document.getElementById('canvas');
                var uploadImg = uploadFilteredImage(can.toDataURL(), $modal, img, id);
                if(!id) {
                    $('#feature-image').attr('src', can.toDataURL());
                }
                
            });

            // $cropFilter.on("click", e => {
            //     var can = document.getElementById('canvas');
            //     $('#croped_image').attr('src', can.toDataURL());
            //     var croppedImage = startCropping($('#croped_image').attr('src', can.toDataURL()));
            // })

            // Upload File
            uploadFile.addEventListener("change", () => {
            // Get File
            const file = document.getElementById("upload-file").files[0];
            // Init FileReader API
            // var sizeInMB = bytesToSize(file.size);
            if(file.size > 2600000) {
                alert('You are trying to upload more than 2.5 mb file, please upload less than 2.5 mb.'); return false;
            } 
            const reader = new FileReader();
            $('#modal').modal('show');
            // Check for file
            if (file) {
                // Set file name
                fileName = file.name;
                // Read data as URL
                reader.readAsDataURL(file);
            }

            var can = document.getElementById('canvas');
            var _image = $('#croped_image').attr('src', can.toDataURL());

            // Add image to canvas
            reader.addEventListener(
                "load",
                () => {
                // Create image
                img = new Image();
                // Set image src
                img.src = reader.result;
                // On image load add to canvas
                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    canvas.removeAttribute("data-caman-id");
                };
                },
                false
            );
            });

            // Download Event
            downloadBtn.addEventListener("click", () => {
                // Get ext
                const fileExtension = fileName.slice(-4);

                // Init new filename
                let newFilename;

                // Check image type
                if (fileExtension === ".jpg" || fileExtension === ".png") {
                    // new filename
                    newFilename = fileName.substring(0, fileName.length - 4) + "-edited.jpg";
                }

                // Call download

                download(canvas, newFilename);
            });

            // Download
            function download(canvas, filename) {
            // Init event
            let e;
            // Create link
            const link = document.createElement("a");

            // Set props
            link.download = filename;
            link.href = canvas.toDataURL("image/jpeg", 0.8);
            console.log(canvas.toDataURL("image/jpeg", 0.8));
            // New mouse event
            e = new MouseEvent("click");
            // Dispatch event
            link.dispatchEvent(e);
            }


})


function bytesToSize(bytes) {
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  if (bytes === 0) return 'n/a';
  const i = parseInt(Math.floor(Math.log(Math.abs(bytes)) / Math.log(1024)), 10);
  if (i === 0) return `${bytes} ${sizes[i]}`;
  return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`;
}
// =============== 
    // var image = document.getElementById('sample_image');
	// 	var filterControls = document.querySelectorAll('input[type=range]');
	// 	function applyFilter() {
	// 		var computedFilters = '';
	// 		filterControls.forEach(function(item, index) {
    //             console.log(item.getAttribute('data-filter'));
    //             // if(item.value > 0) {
    //                 computedFilters += item.getAttribute('data-filter') + '(' + item.value + item.getAttribute('data-scale') + ') ';
    //             // }
				
	// 		});
	// 		image.style.filter = computedFilters;
	// 	};

$('#upload-gallery-file').on('change', function(e) {
    var gallery = document.getElementById("upload-gallery-file");
    var formData = new FormData();
    for(i=0; i < gallery.files.length; i++) {
        var urls = URL.createObjectURL(e.target.files[i]);
        // console.log(urls);
        formData.append("files[]", document.getElementById('upload-gallery-file').files[i]);
        // document.getElementById("new-gallery-imgs").innerHTML += '<div class="col-4 col-grid mt-2"><img src="'+urls+'" class="img-thumbnail featured-img" id="feature-image"></div>';
    }
    var product = $('#product_id').val();
    formData.append("product", product);
    UploadToServer(formData);
})

function UploadToServer(formData) {
    $.ajax({
        url:'{{route("upload.gallery.images")}}',
        method:'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        processData: false,
        contentType: false,
        cache: false,
        data: formData,
        enctype: 'multipart/form-data',
        success:function(respo)
            {
                console.log(respo)
                if(respo.status) {
                   respo.images.forEach(function(v, k) {
                       console.log(v);
                       document.getElementById("new-gallery-imgs").innerHTML += '<div class="col-4 col-grid mt-2"><img src="'+v.url+'" class="img-thumbnail featured-img" id="feature-image"> <a href="javascript:;"> <i class="icon icon-filter_b_and_w icon-apply-filter" style="font-size: 22px;background-color: white; color:green; position: absolute;  top: 10px;float: right;font-weight: 900;left: 55px;" data-src="'+v.url+'" data-id="'+v.id+'" id="data-src-'+v.id+'"></i></a><a href="javascript:;" class="remove-gallery-image" data-id="'+v.id+'"> <i class="icon icon-trash" style="font-size: 22px; color:red; position: absolute;top: 10px;float: right;font-weight: 900;background-color: white;left: 30px; "></i></a></div>';
                   })
                   location.reload();
                    // $('#close-modal').trigger('click');
                    // $modal.modal('hide');        
                }
                
                // $('#feature-image').attr('src', data);
            }
    });
}

function uploadFilteredImage(base64data, $modal, img, id) {
//    var filters = document.getElementById('sample_image').style.filter;
   var product = $('#product_id').val();
   $.ajax({
     url:'{{route("upload.cropped.image")}}',
     method:'POST',
     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
     data:{image:base64data, product:product, id:id},
     success:function(respo)
        {
            if(respo.status) {
                $('#modal').modal('hide');
                    $('input[type=range]').val(0);
                    Caman("#canvas", img, function() {
                        this.revert(false);
                        this.render();
                    });
                    var canvas = document.getElementById("canvas");
                    const context = canvas.getContext('2d');
                    context.clearRect(0, 0, canvas.width, canvas.height);
                    context.beginPath();
                    canvas.removeAttribute("data-caman-id");
                    $('#close-modal').trigger('click');
                    // $modal.modal('hide'); 
                    if(id) {
                        $('#'+id).attr('src', base64data);
                        $('#data-src-'+id).data('src', base64data);
                    }  
                $('#crop-filter').prop('checked', false);
                $('#croped_image').attr('src', '');
                $('#close-modal').trigger('click'); 
            }
            
            // $('#feature-image').attr('src', data);
        }
    });
}
$(document).ready(function(){

var $modal = $('#modal');

var cropper;
// var image = document.getElementById('sample_image');

// $('#selectedImage').click(function(event) {
//     $modal.modal('show');
//     var crop = jSuites.crop(document.getElementById('image-cropper'), {
//     area: [ 300, 300 ],
//     crop: [ 300, 300 ],
//     value: '/oms/public/uploads/63593db93889e.png',
// })
// });

// document.getElementById('brightness').onchange = function() {
//     document.getElementById('image-cropper').crop.brightness(this.value);
// }
 
// document.getElementById('contrast').onchange = function() {
//     document.getElementById('image-cropper').crop.contrast(this.value);
// }
 
// document.getElementById('image-getter').onclick = function() {
//     document.getElementById('image-cropper-result').children[0].src = document.getElementById('image-cropper').crop.getCroppedImage().src;  
// }

// $('#selectedImage').change(function(event){
//     var files = event.target.files;

//     var done = function(url){
//         image.src = url;
//         $modal.modal('show');
//     };

//     if(files && files.length > 0)
//     {
//         reader = new FileReader();
//         reader.onload = function(event)
//         {
//             done(reader.result);
//         };
//         reader.readAsDataURL(files[0]);
//     }
// });

// $('#crop-filter').on('change', function() {
//     var can = document.getElementById('canvas');
//     $('#croped_image').attr('src', can.toDataURL());
//     var croppedImage = startCropping($('#croped_image').attr('src', can.toDataURL()))
//     $('#cropImage').modal('show');
//     var imagee = document.getElementById('croped_image');
//         cropper = new Cropper(imagee, {
//             aspectRation: 1,
//             viewMode: 4,
//             preview: '.preview'
//         });
// })
$modal.on('shown.bs.modal', function() {
    // cropper = new Cropper(image, {
    //     aspectRation: 1,
    //     viewMode: 4,
    //     preview: '.preview'
    // });

    // $('.crop-upload').css('display', 'none');
    
    // $('.simple-upload').css('display', 'inline-block');
}).on('hidden.bs.model', function() {
    // cropper.destroy();
    // cropper = null;
});



$('.crop-filter').on('change', function() {
    
    var can = document.getElementById('canvas');
    $('#croped_image').attr('src', can.toDataURL());
    // var croppedImage = startCropping($('#croped_image').attr('src', can.toDataURL()))
    // $('#cropImage').modal('show');
    $('#orignal-image').attr('src', can.toDataURL());
    var image = document.getElementById('croped_image');
    if($(this).prop("checked")) {
        cropper = new Cropper(image, {
            aspectRatio: 12 / 12,
            viewMode: 3,
            // preview: '.preview',
            dragMode: 'move',
            data:null,
            responsive:true,
            guides:true,
            // Enable to move the image
            movable:true,

        });

        $('.crop-upload').attr('disabled', false);
        // $('.crop-upload').css('display', 'inline-block');
        // $('.simple-upload').css('display', 'none');
    }else {
        $('.crop-upload').attr('disabled', true);
        // $('.crop-upload').css('display', 'none');
        // $('.simple-upload').css('display', 'inline-block');
        cropper.destroy();
        cropper = null;
    }
    
});

$('#crop, #crop1').click(function() {
    canvas = cropper.getCroppedCanvas();
    canvas.toBlob(function(blob) {
        url = URL.createObjectURL(blob);
        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function() {
            var base64data = reader.result;
            var dimensions = getPngDimensions(base64data);
                var canvas = document.getElementById("canvas");
                var ctx = canvas.getContext("2d");

                var image = new Image();
                image.src = base64data;
                console.log(dimensions.width);
                // ctx.clearRect(0, 0, canvas.width, canvas.height); 
                image.onload = function() { ctx.drawImage(sprite, 0, 0); };
                canvas.width = dimensions.width;
                canvas.height = dimensions.height;
                ctx.clearRect(0, 0, canvas.width, canvas.height); 
                ctx.beginPath();
                image.onload = function() {
                canvas.removeAttribute("data-caman-id");
                ctx.drawImage(image, 0, 0, image.width, image.height);
                };
                image.src = base64data;

                // img.src = base64data;
            $('#sample_image').attr('src', base64data);
            cropper.destroy();
            cropper = null;
            $('#croped_image').attr('src', base64data);
            $('#crop-filter').prop("checked", false);
            // uploadFile(base64data,$modal);
        }
    });
});

});

function getPngDimensions(dataUri) {
    if (dataUri.substring(0, 22) !== 'data:image/png;base64,') {
        throw new Error('Unsupported data URI format');
    }

    // 32 base64 characters encode the necessary 24 bytes
    return getDimensions(base64Decode(dataUri.substr(22, 32)));
}

function toInt32(bytes) {
    return (bytes[0] << 24) | (bytes[1] << 16) | (bytes[2] << 8) | bytes[3];
}

function getDimensions(data) {
    return {
        width: toInt32(data.slice(16, 20)),
        height: toInt32(data.slice(20, 24))
    };
}

var base64Characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';


function base64Decode(data) {
    var result = [];
    var current = 0;

    for(var i = 0, c; c = data.charAt(i); i++) {
        if(c === '=') {
            if(i !== data.length - 1 && (i !== data.length - 2 || data.charAt(i + 1) !== '=')) {
                throw new SyntaxError('Unexpected padding character.');
            }

            break;
        }

        var index = base64Characters.indexOf(c);

        if(index === -1) {
            throw new SyntaxError('Invalid Base64 character.');
        }

        current = (current << 6) | index;

        if(i % 4 === 3) {
            result.push(current >> 16, (current & 0xff00) >> 8, current & 0xff);
            current = 0;
        }
    }

    if(i % 4 === 1) {
        throw new SyntaxError('Invalid length for a Base64 string.');
    }

    if(i % 4 === 2) {
        result.push(current >> 4);
    } else if(i % 4 === 3) {
        current <<= 6;
        result.push(current >> 16, (current & 0xff00) >> 8);
    }

    return result;
}

// End Filter Js 

 $(document).ready(function() {
          $('.summernote').summernote({
            height: 250,
          });
        });

$('.save-description').on('click', function () {
    var btnForm = $(this).val();
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
    var btnForm = $(this).val();
    console.log($(this).form().serialize());
    if(!$('#'+btnForm+'-sku').val()) {
        $('.sku-error').html('<strong>Product sku is required</strong>');
        $('html, body').animate({
            scrollTop: $('.sku-error').first().offset().top-200
        }, 500);
        return false;
    }
    $.ajax(
        {
            url: "{{route('save.listing.data.form')}}",
            type: 'POST',
            data: $('#'+btnForm).serialize(),
            catche: false,
            success: function(resp) {
                if(resp.status) {
                    console.log(resp);
                        $(".toast-action").data('title', 'Action Done!');
                        $(".toast-action").data('type', 'success');
                        $(".toast-action").data('message', 'Data saved successfully.');
                        $(".toast-action").trigger('click');
                    } else {
                        $(".toast-action").data('title', 'Went Wrong!');
                        $(".toast-action").data('type', 'error');
                        $(".toast-action").data('message', 'Opps! Somthing went wrong.');
                        $(".toast-action").trigger('click');
                    }
            }
        }
    )
});
$('.remove-gallery-image').on('click', function() {
    if(confirm('Are you sure want to delete?')) {
        var image = $(this).data('id');
        $.ajax({
            url: "{{route('remove.gallery.image')}}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {image:image},
            catche: false,
            success: function(resp) {
                if(resp.status) {
                    $('#imge-box'+image).remove();
                }
                
            }
        });
    }
});
$('.product-title').on('keyup', function() {
    var title = $(this).val();
    if(title) {
        $.ajax({
            url: "{{route('generate.product.seo.url')}}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {title: title},
            catche: false,
            success: function(resp) {
                if(resp.status) {
                    $('#seourl').val(resp.url);
                    $('#seo_url').val(resp.url);
                }
                
            }
        })
    }
});

$(document).ready(function() {
    var button = $('#add-more');
    var wrapper = $('.table-body');
    var x = 0;
    
    $(button).on('click', function() {
        var row = '<tr id="row-'+x+'"><td><input type="text" name="sort_order[]" value="" id="priority'+x+'" class="form-control" autocomplete="off"></td>'+'\n'+
                    '<td><input type="text" name="price[]" value="" id="special-price'+x+'" class="form-control" autocomplete="off"></td>'+'\n'+
                    ' <td><input type="text" name="start_date[]" value="" id="start_date'+x+'" class="date-time-picker'+x+' form-control" autocomplete="off"></td>'+'\n'+
                    '<td><input type="text" name="end_date[]" value="" id="start_date'+x+'" class="date-time-picker'+x+' form-control" autocomplete="off"></td>'+'\n'+
                    '<td><a href="javascript:;" class="remove-row" onclick="reomveRow('+x+')"><i class="icon-close2 text-danger-o text-danger" style="font-size:25px"></i></a></td></tr>';
      $(wrapper).append(row);
      $('.date-time-picker'+x).datetimepicker({format: "Y-m-d",'timepicker':false});
    });
    x++;
    
    
});

function reomveRow(row) {
    $('#row-'+row).remove();
}

function reomveOldRow(id) {
    console.log(id);
    if(id) {
        if(confirm('Are you sure want to reomve?')) {
            $.ajax({
                url: "{{route('remove.speacial.price')}}",
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:id},
                catche: false,
                success: function(resp) {
                    console.log(resp);
                    if(resp.status) {
                            $('#row-'+id).remove();
                            $(".toast-action").data('title', 'Action Done!');
                            $(".toast-action").data('type', 'success');
                            $(".toast-action").data('message', 'Speacial price removed successfully.');
                            $(".toast-action").trigger('click');
                            $('.price-error').html('');
                            $('.meta-title-error').html('');
                            $('.name-error').html('');
                        } else {
                            $(".toast-action").data('title', 'Went Wrong!');
                            $(".toast-action").data('type', 'error');
                            $(".toast-action").data('message', 'Opps! Somthing went wrong, Try again.');
                            $(".toast-action").trigger('click');
                        }
                }
            });
        }
        
    }
}

$('#special-price-form').submit(function(e) {
    e.preventDefault();
    console.log($(this).serialize());
    $.ajax({
        url: "{{route('save.special.price')}}",
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: $(this).serialize(),
        catche: false,
        success: function(resp) {
            if(resp.status) {
                $(".toast-action").data('title', 'Action Done!');
                $(".toast-action").data('type', 'success');
                $(".toast-action").data('message', resp.message);
                $(".toast-action").trigger('click');
            }else {
               $(".toast-action").data('title', 'Went Wrong!');
               $(".toast-action").data('type', 'error');
               $(".toast-action").data('message', resp.message);
               $(".toast-action").trigger('click');
             }
                
        } 
    });
})
</script>
@endpush