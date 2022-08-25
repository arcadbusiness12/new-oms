
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Stock Level
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                           <?php if($product) { ?>
                            <form action="<?php echo route('update.stock.level') ?>" method="post" name="update_quantity">
                            {{ csrf_field() }}
                                <table class="table" width="100%" style="border: 1px solid #3f51b5">
                        
                                <thead >
                        
                                <tr 
                                style="background-color: #3f51b5;color:white"
                                >
                                <th scope="col"><center>Image</center></th>
                                <th scope="col"><center>Sku</center></th>
                                </tr>
                                
                                <tr class="product_list">
                                    <td class="col-xs-6 text-center"><img src="<?php echo $product['image'] ?>" width="100"></td>
                                    <td class="col-xs-6 text-center"><?php echo $product['sku'] ? $product['sku'] : '-' ?></td>
                                </tr>
                                <tr class="loader-row text-center" style="display: none;">
                                    <td colspan="2" class="col-xs-12 text-center">
                                        <div class="preloader-wrapper small active">
                                            <div class="spinner-layer spinner-green-only">
                                                <div class="circle-clipper left">
                                                    <div class="circle"></div>
                                                </div><div class="gap-patch">
                                                <div class="circle"></div>
                                            </div><div class="circle-clipper right">
                                                <div class="circle"></div>
                                            </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                </tr>
                                <tr class="option_list">
                                    
                                    <td colspan="2">
                                        <table style="width: 100%">
                                            
                                            <tr>
                                                <td class="col-xs-2 text-center" style="vertical-align: initial;">
                                                    <?php if(count($product['options']) > 1 && isset($product['options']['static'])) { ?>
                                                    <br>
                                                    <label><?php echo $product['options']['static']['name'] ?> : <?php echo $product['options']['static']['value'] ?></label>
                                                    <?php unset($product['options']['static']); ?>
                                                    <?php $colspan = '5'; } else { $colspan = '6'; }?>
                                                    <select name="duration" class="form-control average_duration" data-product-id="<?php echo $product['product_id'] ?>" data-product-sku="<?php echo $product['sku'] ?>" data-option='<?php echo json_encode($product['options']) ?>'>
                                                        <option value="">Select Duration</option>
                                                        <?php foreach ($duration as $value) { ?>
                                                        <option value="<?php echo $value ?>" <?php if($value == 30){echo 'selected'; } ?>><?php echo $value ?> Days</option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td class="table-responsive" colspan="<?php echo $colspan ?>">
                                                    <table class="table">
                                                        <tr>
                                                            <td class="col-xs-4 text-center"><b>Option</b></td>
                                                            <td class="col-xs-2 text-center"><b>Available Quantity</b></td>
                                                            <td class="col-xs-2 text-center"><b>Min. Quantity</b></td>
                                                            <td class="col-xs-2 text-center"><b>Average Quantity</b></td>
                                                        </tr>
                                                        <?php foreach ($product['options'] as $key => $option) { ?>
                                                        <tr class="update_fields">
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][<?php echo $key ?>][option_id]" value="<?php echo $option['option_id'] ?>"/>
                                                            <input type="hidden" name="product[<?php echo $product['product_id'] ?>][<?php echo $key ?>][option_value_id]" value="<?php echo $option['option_value_id'] ?>"/>
                                                            <td class="col-xs-2 text-center">
                                                                <?php $gt = DB::table('oms_options_details')->where('id',$option['option_value_id'])->select('value as vl')->first(); ?>
                                                                {{$gt->vl}}
                                                            </td>
                                                            <td class="col-xs-2 text-center"><b><?php echo $option['quantity'] ?></b></td>
                                                            <td class="col-xs-2"><input type="text" pattern="^[0-9]+" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][<?php echo $key ?>][min_quantity]" value="<?php echo $option['minimum_quantity'] ?>" class="form-control min_quantity" required/></td>
                                                            <td class="col-xs-2"><input type="text" pattern="^[0-9]+" title="Enter greater than or equal to 0" name="product[<?php echo $product['product_id'] ?>][<?php echo $key ?>][average_quantity]" value="<?php echo $option['average_quantity'] ?>" class="form-control" data-option-value-id="<?php echo $option['option_value_id'] ?>" required readonly/></td>
                                                        </tr>
                                                        
                                                        
                                                        <?php } ?>
                                                    </table>
                                                </td>       
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td colspan="5">
                                        <button type="submit" name="submit" value="update_quantity" class="btn btn-success pull-right">Submit</button>
                                    </td>
                                </tr>

                                </thead>
                        
                                <tbody>
                                </tbody>
                                
                                </table>
                            </form>
                            <?php }else{ ?>
                            <div class="row">
                               <div class="alert alert-danger text-center">Product Not Found!</div> 
                            </div>
                            <?php } ?>
                    </div>

                    </div>
                </div>