<?php if($products) { ?>
<thead>
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Amount</th>
        <th>Options</th>
    </tr>
</thead>
<tbody>        
@foreach ($products as $product)
<?php
$unique = uniqid();
if(isset($product['special'])){
    $price = $product['special'];
}else{
    $price = $product['price'];
}
?>
<tr class="cart-product-row">
    <td><img width="80" src="{{env('DF_OPEN_CART_IMAGE_URL')}}{{$product['image']}}" /></td>
    <td><strong>{{$product['name']}}</strong><br/>
        <i>{{$product['model']}}</i><br/>
        @foreach ($product['options'] as $option)
        <span> {{$option['name']}}</span>
        @if($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox')
        <select class="form-control" name='product[option][{{$option["product_option_id"]}}]'>
            @foreach ($option['option_values'] as $values)
            @if($values['quantity'])
            <option value="{{$values['product_option_value_id']}}">{{$values['name']}}</option>
            @endif
            @endforeach
        </select>
        @endif
        @endforeach
    </td>
    <td style="vertical-align: middle;">
        <input class="form-control" type="text" name="product[qty]" value="1" required="required" placeholder="Quantity" />
        <input type="hidden" name="product[product_id]" value="{{$product['product_id']}}" />
        <input type="hidden" name="product[name]" value="{{$product['name']}}" />
        <input type="hidden" name="product[model]" value="{{$product['model']}}" />
        <input type="hidden" name="product[price]" value="{{$price}}" />
    </td>
    <td style="vertical-align: middle;"><b>{{$price}}</b></td>
    <td style="vertical-align: middle;"><button type="button" class="btn btn-danger" id="btn-add-cart-product">Add To Cart</button></td>
</tr>
@endforeach
</tbody>
<?php } else { ?>
<thead>
    <tr>
        <th class="text-center text-danger">No Match Found!</th>
    </tr>
</thead>
<?php } ?>