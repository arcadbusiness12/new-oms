@if($data)
{{--  <?php if($errors) { ?>
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php echo $errors['stock'] ?>
</div>
<script>
  $('#search_customer').hide();
</script>
<?php }else{ ?>
  <script>
    $('#search_customer').show();
  </script>
<?php } ?>  --}}
<form name="cart-product-form" id="cart-product-form">
    {{csrf_field()}}
    <table class="table" >
        <thead>
            <tr style="background-color: #3f51b5;color:white">
                <th><center><strong>Image</strong></center></th>
                <th><center><strong>Name</strong></center></th>
                <th><center><strong>SKU/Color</strong></center></th>
                <th style="width:7%"><center><strong>Quantity</strong></center></th>
                <th><center><strong>Price</strong></center></th>
                <th><center><strong>Total</strong></center></th>
                <th><center><strong>Action</strong></center></th>
            </tr>
        </thead>
        <tbody style="border:1px solid #3f51b5">
            @php
                $total = 0;
            @endphp
            @foreach ($data as $key => $val)
                @php
                    $sub_total = $val->product_price * $val->product_quantity;
                    $total    += $sub_total
                @endphp
                <tr style="border-bottom:1px solid lightgray">
                    <td><center><img width="80" src="{{ URL::asset('uploads/inventory_products/'.$val->product_image) }}" /></center></td>
                    <td><center>{{ $val->product_name }}<br>
                            <strong>Size</strong>: {{ $val->cartProductSize->value }} <strong>Color</strong>: {{ $val->product_color }} <br>
                    </center></td>
                    <td><center>{{ $val->product_sku }}</center></td>
                    <td><center><input type="number" name="product_quantity[]" id="product_quantity{{ $val->id }}" size="1" class="form-control" value="{{ $val->product_quantity }}" /></center></td>
                    <td><center>{{ $val->product_price }}</center></td>
                    <td><center><strong>{{ $sub_total }}</strong></center></td>
                    <td>
                        <input type="hidden" name="product_id[]" value="{{ $val->product_id }}">
                        <center>
                        <button type="button" class="btn btn-info active btn-sm btn-cart-update" cart-id="{{ $val->id }}">Update</button>
                        <button type="button" class="btn btn-danger active btn-sm btn-cart-remove" cart-id="{{ $val->id }}">Delete</button>
                        </center>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td><center><strong>Total:</strong></center></td>
                <td><center><strong>{{ $total }}</strong><center></td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-bottom:20px">
        <div class="col-sm-10">

        </div>
        <div class="col-sm-2">
        <button type="button"  id="continue-order" class="btn active btn-success">Continue</button>
        </div>
    </div>
</form>
@else
    <div class="text-danger text-center text-uppercase font-16"><b>Cart is Empty!</b></div>
@endif
