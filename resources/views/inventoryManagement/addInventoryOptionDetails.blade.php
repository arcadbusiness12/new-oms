<table class="table" id="option_table">
    <tbody>
        <?php 
        foreach($option_value_detail as $optionsvalue){ ?>
            <tr id="at_remove_{{$optionsvalue->id}}">
                <td></td>
                <td>
                    <select class="form-control" name="value[]">
                       <?php 
                        foreach($option_value_detail as $kl){
                            if ($optionsvalue->id == $kl->id) { ?>
                             <option value='{{$optionsvalue->id}}' selected="selected">{{$kl->value}}</option>;
                          <?php }
                           else{ ?>
                             <option value='{{$optionsvalue->id}}'>{{$kl->value}}</option>;
                         <?php  }
                         }
                         ?>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="removeAttri('{{$optionsvalue->id}}')"><i class="fas fa-trash-alt"></i>Remove</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
</table>
{{-- @push('scripts')
    <script>
        
    </script>
@endpush  --}}