<?php $unique = uniqid(); ?>
    <div class="product_list_row">
        <div class="product_row">
            <div class="row mb-4" style="border-bottom: 1px solid #9595953b;">
                <div class="col-xs-4 col-sm-2" style="padding: 0">
                    <input type="file" name="image" class="input-image" data-id="" style="position: absolute;height: 100%;width: 100%;opacity: 0;cursor: pointer;" />
                    <img id="uploadable" src="<?php echo $placeholder ?>" width="150px" style="float: right;" />
                </div>
                <div class="col-xs-10 col-sm-10">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4">
                                <select name="category" id="category" class="form-control select-category" >
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cate)
                                        
                                        <option value="{{$cate->id}}" data-code="{{$cate->code}}">
                                            {{$cate->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-4">
                                <select name="subCategory" id="sub-category" class="form-control sub-category">
                                    <option value="">Select Sub-category</option>
                                    {{-- @foreach($subcategories as $cate)
                                        <option value="{{$cate->id}}" data-code="{{$cate->code}}">{{$cate->name}}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        <div class="col-xs-4 col-sm-4">
                            <input type="hidden" class="new-code" >
                            <input type="hidden" name="newSku" class="new-sku" >
                            <input type="hidden" class="newCode" >
                            <input type="text" name="purchase[product][<?= $unique ?>][name]"id="sku" class="form-control" placeholder="Enter Product Name" required/>
                        </div>
                        
                    </div>
                </div>
                <div class="form-group">
                <div class="row mt-4 all_options_row">
                    <div class="col-lg-4">
                        <label class="control-label">Color</label>
                        <select name="purchase[product][<?= $unique ?>][manually_option_color]" id="manually_option_color" class="form-control">
                            <option value="">Select Color</option>
                        <?php foreach ($colors as $key => $value) { ?>
                            <option value="<?= $value ?>" data-id="<?= $value ?>"><?= $key ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="input-group mb-3">
                            <label class="control-label col-lg-12">Size</label>
                                <select name="purchase[product][<?= $unique ?>][manually_option_size]" id="manually_option_size" class="form-control manually_option_size">
                                <option value="0">None</option>
                                <?php $i=0; foreach ($sizes as $key => $value) { ?>
                                    <option class="<?= (!$i) ? 'preactive' : '' ?>" value="<?= $value ?>"><?= $key ?></option>
                                <?php $i++; } ?>
                                </select>
                        </div>
                        <div id="newRow" style="width: 210%!important;"></div>
                    </div>

                    
                    <div class="col-lg-4">
                        <div class="input-group mb-3">
                            <label class="control-label">&nbsp;</label>
                            <button type="button" name="add_selected_options" class="btn btn-primary btn-block add_selected_options" data-product-id="<?php echo $unique ?>">Add Options</button>
                        </div>
                    </div>

                    <div class="manually_option_row"></div>
            </div>
            <div class="row mt-4 text-right">
                <div class="col-xs-12 col-sm-12">
                    
                    <div class="col-xs-10 col-sm-10 col-grid" style="margin-top: 12px;">
                      <label for="add_to_inventory<?php echo $unique ?>">
                        <input type="checkbox" name="purchase[product][<?= $unique ?>][add_to_inventory]" id="add_to_inventory<?php echo $unique ?>" class="chk-col-green is-to-inventory">
                        <a href="javascript:;" style="color: #0e6ffd;font-size: 15px;"><strong> Add To Inventory </strong> </a></label>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-grid">
                        <button type="button" class="btn btn-danger pull-right" onclick="$(this).parents('.product_list_row').remove();"><i class="icon icon-close"></i></button>
                        </div>
                </div>
            </div>
           
        </div>
        </div>
    </div>
    <div id="htmlpart"></div>
    
    </div>
    </div>
@push('scripts')
    
<script type="text/javascript">
$(document).delegate('.input-image', 'change', function(){
    var input = $(this)[0];
    var data_id = $(this).attr('data-id');
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + data_id).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
});

</script>

@endpush