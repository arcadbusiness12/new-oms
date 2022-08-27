<?php $unique = uniqid(); 
$quantity = $average_quantity = $duration = $min = 0;
?>
<tr class="options_row">
    <?php if(isset($product['options']) && is_array($product['options'])) { ?>
    <?php foreach ($product['options'] as $option) { ?>
    <td class="col-xs-2">
        <?php if($option['type'] == 'select' || $option['type'] == 'radio') { 
        if($option['static_option_id'] != $option['option_id']) {
            foreach($option['option_values'] as $okey => $values) {
                $option_name[$okey] = $values['name'];    
            }
            array_multisort($option_name, SORT_ASC, $option['option_values']);
                        
            $quantity = $option['option_values'][0]['quantity']; 
            $average_quantity = $option['option_values'][0]['average_quantity']; 
            $duration = $option['option_values'][0]['duration']; 
            $min = $option['option_values'][0]['minimum_quantity']; 
        }
        ?>
        <select name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][option][<?php echo $option['option_id'] ?>]" class="form-control product_option_dropdown" data-product-id="<?php echo $product['product_id'] ?>" data-option-id="<?php echo $option['option_id'] ?>">
            <?php foreach($option['option_values'] as $values) {?>
                <option value="<?php echo $values['option_value_id'] ?>"><?php echo $values['name'] ?></option>
            <?php } ?>
        </select>
        <?php } ?>
    </td>
    <?php } ?>
    <?php }  ?>
    <td class="col-xs-2">
        <div class="quantity"><b class="fa-2x"><?php echo $quantity ?></b> Available</div>
    </td>
    <td class="col-xs-2">
        <div class="average"><?php echo (int)$average_quantity .' pcs / '. (int)$duration .' Days'; ?></div>
    </td>
    <td class="col-xs-2">
        <div>
            <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" class="form-control input_quantity" required/>
            <!-- <input type="number" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" min="<?php echo max(1, $min) ?>" class="form-control input_quantity" required/> -->
        </div>
    </td>
    <td class="col-xs-2 text-right">
        <div>
            <button type="button" class="btn btn-danger" onclick="$(this).parents('tr.options_row').remove();"><i class="icon icon-minus-circle"></i></button>
        </div>
    </td>
</tr>