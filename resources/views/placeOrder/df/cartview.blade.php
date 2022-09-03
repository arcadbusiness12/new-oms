<?php if($products) { ?>
<?php if($errors) { ?>
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
<?php } ?>
<form name="cart-product-form" id="cart-product-form">
    {{csrf_field()}}
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Model</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="response-products">
            <?php foreach ($products as $key => $product) { ?>
                <tr class="cart-product-row">
                    <td><img width="80" src="<?php echo $product['image'] ?>" /></td>
                    <td><strong><?php echo $product['name'] ?></strong><br/>
                        <?php foreach ($product['options'] as $option) { ?>
                        <span><b><?php echo $option['name'] ?> : </b></span>
                        <span><?php echo $option['value'] ?></span><br>
                        <?php } ?>
                        <?php if($product['stock'] == 'false') { ?>
                        <small class="text-danger">***</small>
                        <?php } ?>
                    </td>
                    <td><?php echo $product['model'] ?></td>
                    <td><input type="number" name="product[<?php echo $key ?>][quantity]" class="form-control" value="<?php echo $product['quantity'] ?>" /></td>
                    <td><?php echo $product['price'] ?></td>
                    <td><b><?php echo $product['total'] ?></b></td>
                    <td>
                        <input type="hidden" name="product[<?php echo $key ?>][product_id]" value="<?php echo $product['product_id'] ?>">
                        <?php foreach ($product['options'] as $option) { ?>
                        <input type="hidden" name="product[<?php echo $key ?>][option][<?php echo $option['product_option_id'] ?>]" value="<?php echo $option['product_option_value_id'] ?>">
                        <?php } ?>
                        <button type="button" class="btn btn-warning btn-cart-update" value="<?php echo $product['cart_id'] ?>"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-danger btn-cart-remove" value="<?php echo $product['cart_id'] ?>"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="cart-totals">
        <div class="row">
            <div class="col-xs-5 col-xs-offset-7">
                <table class="table">
                    <?php foreach ($totals as $total) { ?>
                        <tr>
                            <td><b><?php echo $total['title'] ?></b></td>
                            <td><?php echo $total['text'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="form-group">
                    @if(!$errors)
                      <button type="button" name="generate_exchange" id="continue-order" class="btn btn-primary btn-block">Continue</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
<?php } else { ?>
    <div class="text-danger text-center text-uppercase font-16"><b>Cart is Empty!</b></div>
<?php } ?>