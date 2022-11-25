<table class="table" style="border:1px solid #3f51b5">
@if( $product )
<thead>
    <tr style="background-color: #3f51b5;color:white">
        <th><strong>Image</strong></th>
        <th><strong>Name</strong></th>
        <th style="width: 14%"><strong><center>Size</strong></center></th>
        <th style="width: 5%"><strong><center>Quantity</strong></center></th>
        <th><strong><center>Price</strong></center></th>
        <th><strong><center>Action</strong></center></th>
    </tr>
</thead>
<tbody>
@php
$unique = uniqid();
if( $product->productSpecials && $product->productSpecials->count() > 0 ){
    $price = $product->productSpecials[0]->price;
}else{
    $price = $product->productDescriptions[0]->price;
}
@endphp
<tr class="cart-product-row">
    <td><img width="80" src="{{ URL::asset('uploads/inventory_products/'.$product->image) }}" /></td>
    <td><strong>{{ $product->productDescriptions[0]->name }}</strong><br/>
        <span>{{ $product->sku }} : {{ $product->option_name  }}</span>
    </td>
    <td>
        <select class="form-control" name='product_option_id' required>
            <option value="">-Select Size/Color-</option>
            @foreach ($product->ProductsSizes as $key => $size)
                @if( $size->available_quantity > 0 )
                    <option value="{{ $size->product_option_id  }}">{{ $size->value  }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        <input type="hidden" name="product_id" value="{{$product->product_id}}" />
        <input type="hidden" name="product_color" value="{{$product->option_name}}" />
        <input type="hidden" name="product_image" value="{{$product->image}}" />
        <input type="hidden" name="product_name" value="{{ $product->productDescriptions[0]->name }}" />
        <input type="hidden" name="product_sku" value="{{ $product->sku }}" />
        <input type="hidden" name="product_price" value="{{ $price }}" />
        <input class="form-control" type="text" name="product_quantity" value="1" required="required" placeholder="Quantity" size="1" />
    </td>
    <td style="vertical-align: middle;"><center>{{$price}}</center></td>
    <td style="vertical-align: middle;"><center><button type="submit" class="btn btn-success active" id="btn-add-cart-product">Add To Cart</button></center></td>
</tr>
</tbody>
@else
    <thead>
        <tr>
            <th class="text-center text-danger">No Match Found!</th>
        </tr>
    </thead>
@endif
</table>
