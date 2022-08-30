<table class="table option_row">
    <?php for ($i=0; $i < $count ; $i++) { ?>
    <?php $unique = uniqid(); ?>
    <tbody style="border-style:none;">
        <tr style="border-style:none;">
            <?php foreach ($options as $key => $value) { ?>
            <td class="col-sm-4" style="border-bottom-width: 0px;">
                <select name="purchase[product][<?= $data_product_id ?>][options][<?= $unique ?>][option][<?= $key ?>]" class="form-control" <?= 'data-option-id-'.$key ?> required>
                <?php foreach ($value as $k => $v) { ?>
                <option value="<?= $v['option_value_id'] ?>" <?php if($key != 19 && $i == $k) { ?> selected="selected" <?php } ?>><?= $v['name'] ?></option>
                <?php } ?>
                </select>
            </td>
            <?php } ?>
            <td class="col-sm-4" style="border-bottom-width: 0px;">
                <input type="text" pattern="^[1-9][0-9]*$" title="Enter greater than 0" name="purchase[product][<?= $data_product_id ?>][options][<?= $unique ?>][quantity]" placeholder="Quantity" class="form-control" required />
            </td>
            <td class="col-sm-4" style="border-bottom-width: 0px;">
                <button type="button" class="btn btn-danger" onclick="$(this).parents('tr').remove();"><i class="icon icon-close"></i></button>
            </td>
        </tr>
    </tbody>
    <?php } ?>
</table>