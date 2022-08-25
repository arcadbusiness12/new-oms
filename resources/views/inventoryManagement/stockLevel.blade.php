@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <?php if(Session::has('message')) { ?>
                            <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo Session::get('message') ?>
                            </div>
                            <?php } ?>
                        <div class="card-header white">
                            <form name="form_stock_level" id="form_stock_level" action="{{route('get.inventory.stock.level.product')}}" method="get">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="control-label">Product SKU</label>
                                        <input type="text" name="product_sku" id="product_sku" list="product_skus" class="form-control" value="<?php isset($old_input['product_sku']) ? $old_input['product_sku'] : '' ?>" autocomplete="off" placeholder="Product SKU">
                                        <datalist id="product_skus"></datalist>
                                    </div>  
                                    <br>
                                    <div class="col-md-3 mt-4">
                                        <input type="button" name="search_inv_dashboard" id="search_stock_level" class="btn btn-sm btn-primary" value="Search">
                                    </div>
                            </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="stock_level_row">
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@include('inventoryManagement.dashboardModals')
@endsection

@push('scripts')
    <script>
        var xhr = {};
        $(document).delegate('#product_sku','keyup',function(){
        console.log("Inventory Model Called");
        _this = $(this);
        if(typeof xhr['get_product_sku_keyup'] != 'undefined' && xhr['get_product_sku_keyup'].readyState != 4){
            xhr['get_product_sku_keyup'].abort();
        }
        xhr['get_product_sku_keyup'] = $.ajax({
            method: "POST",
            url: "{{route('inventory.get.product.sku')}}",
            data: {
                product_sku : $(this).val()
            },
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
        }).done(function (data){
            html = '';
            if(data.skus){
                $.each(data.skus, function(k,v){
                    html +='<option value="'+v+'">';
                });
                $('#product_skus').html(html);
            }
        });
    });

    $(document).delegate('#search_stock_level','click',function(){
        _this = $(this);
        $.ajax({
            method: "POST",
            url: "{{route('get.inventory.stock.level.product')}}",
            data: $('#form_stock_level').serialize(),
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            beforeSend: function(){
                $(_this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
                $(_this).prop('disabled',true);
            },complete:function(){
                $(_this).html('Search');
                $(_this).prop('disabled',false);
            },
        }).done(function (html){
            if(html){
                $('#stock_level_row').html(html);
            }
        });
    });

    $(document).delegate('.average_duration','change',function(){
        console.log("Duration");
        _this = $(this);
        _this_parent = $(this).parents('.update_fields');
        $.ajax({
            method: "POST",
            url: "{{route('check.stock.level.duration.quantity')}}",
            data: {
                duration : $(this).val(),
                product_sku : $(this).attr('data-product-sku'),
                options : $(this).attr('data-option'),
            },
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            beforeSend: function(){
                $('.loader-row').css('display', 'table-row');
                $('.option_list').css('display', 'none');
            },complete:function(){
                $('.loader-row').css('display', 'none');
                $('.option_list').css('display', 'table-row');
            },
        }).done(function (json){
            if(json.success){
                console.log(json);
                if(Array.isArray(json.quantity)){
                    $.each(json.quantity, function(i,v){
                        if(v.quantity){
                            $('[data-option-value-id="'+v.option_value_id+'"]').val(v.quantity);
                        }else{
                            $('[data-option-value-id="'+v.option_value_id+'"]').val(0);
                        }
                    });
                }else{
                    if(json.quantity){
                        $('#stock_level .average_quantity').val(json.quantity);
                    }else{
                        $('#stock_level .average_quantity').val(0);
                    }
                }
            }
        });
    });
    </script>
@endpush