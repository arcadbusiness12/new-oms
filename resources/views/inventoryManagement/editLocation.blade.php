<div id="id_msg"></div>
                            <?php if($product) { ?>
                            <form action="<?php echo URL::to('/inventory_manage/update_add_stock') ?>" method="post" name="update_stock" id="form_update_stock">
                                {{ csrf_field() }}
                                <table class="table">
                                    <tr class="product_list_title_row">
                                        <td class="col-xs-4 text-center product_list_title">Image</td>
                                        <td class="col-xs-4 text-center product_list_title">Color</td>
                                        <td class="col-xs-4 text-center product_list_title">SKU</td>
                                    </tr>
                                    <tr class="product_list">
                                        <td class="col-xs-4 text-center"><img src="{{URL::asset('uploads/inventory_products/'.$product[0]->image)}}" width="100"></td>
                                        <td class="col-xs-4 text-center">{{ $product[0]->option_name }}</td>
                                        <td class="col-xs-4 text-center">{{ $product[0]->sku }}</td>
                                    </tr>
                                    <tr class="option_list">
                                        <td colspan="3">
                                            <table style="width: 100%">

                                                    <td class="table-responsive">
                                                        <table class="table">
                                                            <tr class="">
                                                                <td class="col-xs-4 text-center"><b>Option</b></td>
                                                                <td class="col-xs-4 text-center"><b>Rack</b></td>
                                                                <td class="col-xs-4 text-center"><b>Shelf</b></td>
                                                            </tr>
                                                            <input type="hidden" name="product_id" value="{{ $product[0]->product_id }}">
                                                            <input type="hidden" name="option_id" value="{{ $product[0]->ProductsSizes[0]->option_id }}">

                                                            @foreach($product as $key=>$prodd)
                                                              @foreach($prodd->ProductsSizes as $key=>$prod)
                                                                <tr class=" update_fields">
                                                                    <input type="hidden" name="product_option_id[]" value="{{ $prod->product_option_id }}">
                                                                    <input type="hidden" name="option_value_id[]" value="{{ $prod->option_value_id }}">
                                                                    <td class="col-xs-4 text-center">{{-- {{ $prod->option_name }} - --}} {{ $prod->value }}</td>
                                                                    <td class="col-xs-4 text-center"><input type="text" name="product_rakk[]" class="form-control" value="{{ $prod->rack }}" /></td>
                                                                    <td class="col-xs-4 text-center"><input type="text" name="product_shelf[]" class="form-control" value="<?php echo $prod->shelf ?>" /></td>
                                                                </tr>
                                                              @endforeach
                                                            @endforeach
                                                            
                                                        </table>
                                                    </td>
                                                    <td class="col-xs-2" style="vertical-align: initial">
                                                        <label>Row</label>
                                                        <input type="text" name="row" class="form-control" placeholder="Row" value="{{ $product[0]->row }}" />
                                                        <input type="hidden" name="submit" value="" >
                                                        <a onclick="updateStockLocation(1)" name="btn_submit" value="update_location" class="btn btn-success form-control btn-submit-update-stock">Update Location</a>
                                                        <br><br>
                                                    </td>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <?php }else{ ?>
                            <div class="row">
                               <div class="alert alert-danger text-center">Product Not Found!</div> 
                           </div>
                           <?php } ?>
@push('scripts')
<script type="text/javascript">
    $('#form_update_stock').on('submit', function(){
        $('button[name="btn_submit"]').prop('disabled', true);
    });
    $('button[name="btn_submit"]').on('click', function(){
        $('input[name="submit"]').val($(this).val());
    });
</script>
@endpush
<script type="text/javascript">
    function updateStockLocation(product_id){
    var url = "{{route('inventory.edit.product.location', ':id')}}";
    url = url.replace(':id', product_id);
        $('#id_msg').html('<center><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></center>').removeClass('alert alert-success');
        var data = $('#form_update_stock').serialize();
        console.log(data);
        $.ajax({
          url: url,
          type: 'POST',
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          data: data,
          success: function (respo) {
            $('#id_msg').addClass('alert alert-success').html(respo.mesge);
        }
    });
    }
    
</script>