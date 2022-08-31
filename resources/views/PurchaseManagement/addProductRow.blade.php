<?php if($product) {
    $quantity = $average_quantity = $duration = $min = 0;
    ?>
    <div class="product_list_row" style="position: relative;">
        <div class="row product_row">
            <div class="col-xs-4 col-sm-2">
                <img width="100" src="<?php echo $product['image'] ?>" />
            </div>
            <div class="col-xs-8 col-sm-8">
                <i><?php echo $product['sku'] ?></i>
            </div>
        </div>
        <div class="table-responsive option_list_row">
            <table class="table">
                <?php
                if(isset($product['options']) && is_array($product['options'])) {
                $colspan = 2; 
                $option_name = array();
                $static_option_array = array();
                foreach ($product['options'] as $option) { 
                    if($option['type'] == 'select' || $option['type'] == 'radio') {
                        // echo $option['option_id'] ." = ". $option['static_option_id']. "<br>";
                        if($option['static_option_id'] != $option['option_id']) {
                            foreach($option['option_values'] as $okey => $values) {
                                $values['static_option_id'] = $option['static_option_id'];
                                $option_name[$option['name']][] = $values;    
                            }
                            //ksort($option_name[$option['name']]);
                            //$option_name[$option['name']] = array_values($option_name[$option['name']]);
                            //array_multisort($option_name[$option['name']], SORT_ASC, $option['option_values']);
                            $colspan++; 
                        }else if($option['static_option_id'] == $option['option_id'] && count($product['options']) == 1){
                            foreach($option['option_values'] as $okey => $values) {
                                $option_name[$option['name']][] = $values;    
                            }
                            $colspan++; 
                        }else{
                            $static_option_array = $option;
                            $colspan++; 
                        }
                    }
    
                }
                foreach ($option_name as $key => $options) {
                    $current_i = 0;
                    // print_r($options);
                    foreach ($options as $option) { $unique = uniqid(); ?>
                    <tr class="options_row">
                        <?php if($static_option_array && $option['option_id'] != $option['static_option_id']) { ?>
                        <td class="col-xs-2">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label"><?php echo $static_option_array['name'] ?> </label>
                            <?php } ?>
                            <select name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][option][<?php echo $static_option_array['option_id'] ?>]" class="form-control product_option_dropdown" data-product-id="<?php echo $product['product_id'] ?>" data-option-id="<?php echo $static_option_array['option_id'] ?>">
                                <?php foreach($static_option_array['option_values'] as $values) {?>
                                    <option value="<?php echo $values['option_value_id'] ?>"><?php echo $values['name'] ?> </option>
                                <?php } ?>
                            </select>
                        </td>
                        <?php } ?>
                        <td class="col-xs-2">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label"><?php echo $key ?></label>
                            <?php } ?>
                            <select name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][option][<?php echo $option['option_id'] ?>]" class="form-control product_option_dropdown" data-product-id="<?php echo $product['product_id'] ?>" data-option-id="<?php echo $option['option_id'] ?>">
                                <?php foreach($option_name[$key] as $values) {?>
                                    <option value="<?php echo $values['option_value_id'] ?>" <?php if($option['name'] == $values['name']) { ?> selected="selected" <?php } ?> ><?php echo $values['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="col-xs-2">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label">In Stock</label>
                            <?php } ?>
                            <div class="quantity"><b class="fa-2x"><?php echo $option['quantity'] ?></b> Available</div>
                        </td>
                        <td class="col-xs-2">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label">Last Period Sale</label>
                            <?php } ?>
                            <div class="average"><?php echo (int)$option['average_quantity'] .' pcs / '. (int)$option['duration'] .' Days'; ?></div>
                        </td>
                        <td class="col-xs-2">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label">Quantity</label>
                            <?php } ?>
                            <div>
                                <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" class="form-control input_quantity" required/>
                                <!-- <input type="number" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" min="<?php echo max(1, $min) ?>" class="form-control input_quantity" required/> -->
                            </div>
                        </td>
                        <td class="col-xs-2 text-right">
                            <?php if($current_i == 0) { ?>
                            <label class="control-label">Remove</label>
                            <?php } ?>
                            <div>
                                <button type="button" class="btn btn-danger" onclick="$(this).parents('tr.options_row').remove();"><i class="icon icon-close"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php $current_i++; }
                } ?>
                <?php } else { 
                $colspan = 0;
                $unique = uniqid();
                $quantity = (int)$product['quantity']['quantity']; 
                $average_quantity = (int)$product['quantity']['average_quantity']; 
                $duration = $product['quantity']['duration']; 
                $min = (int)$product['quantity']['minimum_quantity']; ?>
                <tr>
                    <td class="col-xs-2">
                        <label class="control-label">In Stock</label>
                        <div class="quantity"><b class="fa-2x"><?php echo $quantity ?></b> Available</div>
                    </td>
                    <td class="col-xs-2">
                        <label class="control-label">Last Period Sale</label>
                        <div class="average"><?php echo (int)$average_quantity .' pcs / '. (int)$duration .' Days'; ?></div>
                    </td>
                    <td class="col-xs-2">
                        <label class="control-label">Quantity</label>
                        <div>
                            <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" class="form-control input_quantity" required/>
                            <!-- <input type="number" name="purchase[<?php echo $product['product_id'] ?>][<?php echo $unique ?>][quantity]" min="<?php echo max(1, $min) ?>" class="form-control input_quantity" required/> -->
                        </div>
                    </td>
                    <td class="col-xs-2"></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <?php for ($i=0; $i < $colspan; $i++) { ?>
                    <td class="col-xs-2"></td>
                    <?php } ?>
                    <td class="col-xs-2">
                        <?php if(isset($product['options']) && is_array($product['options']) && count($product['options']) > 1) { ?>
                        <div>
                            <button type="button" name="add_option" class="btn btn-suceess btn-block add_more_option" value="<?php echo $product['product_id'] ?>">Add More</button>
                        </div>
                        <?php } ?>
                    </td>
                    <td class="col-xs-2"></td>
                </tr>
            </table>
        </div>
        <div style="position: absolute;top: 0;right: 0;">
            <button type="button" class="btn btn-danger btn-delete-order-product"><i class="icon icon-close"></i></button>
        </div>
    </div>
    <?php } ?>