<table class="table" id="option_table">
    <tbody>
        <?php 
        foreach($option_value_detail as $optionsvalue){ ?>
        <tr id="at_remove_'{{$optionsvalue->id}}'">
            <td></td>
            <td>
                <select class="form-control" name="value[]">
                    <?php foreach($option_value_detail as $kl){
                        if ($optionsvalue->id == $kl->id) { ?>
                          <option value='{{$optionsvalue->id}}' selected="selected">{{$kl->value}}</option>
                       <?php }
                       else{ ?>
                         <option value='{{$optionsvalue->id}}'>{{$kl->value}}</option>
                       <?php }
                     } ?>
                </select>
                
            </td>
            <td>
                <input type="hidden" name="idxx[]" value="{{$optionsvalue->product_option_id}}" placeholder="Enter Quantity" class="form-control">
                <input type="text" value="{{$optionsvalue->available_quantity}}" placeholder="Enter Quantity" class="form-control" readonly size="1">
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="removeAttri('{{$optionsvalue->id}}','{{$optionsvalue->product_id}}','{{$optionsvalue->product_option_id}}')"><i class="fas fa-trash-alt"></i>Remove</a>
            </td>
        </tr>
    <?php } ?>
        <?php if(!empty($remaining_option_data)){
                foreach($remaining_option_data as $optionsvalue){ ?>
                    <tr id="at_remove_{{$optionsvalue->id}}" class="show_more_optons" style="display:none">
                        <td></td>
                        <td>
                            <select class="form-control" name="value[]">
                                <option value=''>-select-</option>
                               <?php foreach($remaining_option_data as $kl){ ?>
                                    <option value='{{$kl->id}}'>{{$kl->value}}</option>
                                <?php  } ?>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="idxx[]"  placeholder="Enter Quantity" class="form-control">
                            <input type="text" value="" placeholder="Enter Quantity" class="form-control" readonly size="1">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="removeAttri('{{$optionsvalue->id}}')"><i class="fas fa-trash-alt"></i>Remove</a>
                        </td>
                    </tr>
                   
  
               <?php } ?>
               <tr><td colspan="3"><a onclick="showRemainingOption()" class="btn btn-info btn-sm pull-right"> More </a></td></tr>
           <?php } ?>
        </tbody>
</table>