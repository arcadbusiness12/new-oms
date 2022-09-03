<form name="address" method="post">
    <div class="row">
        <label class="col-sm-2 control-label">Full Name</label>
        <div class="col-sm-10 m-b-10">
            <input type="hidden" name="payment_address" value="<?php echo $customer['shipping_address']; ?>"/>
            <input type="hidden" name="shipping_address" value="<?php echo $customer['shipping_address']; ?>"/>
            <input type="text" name="firstname" class="form-control" value="<?php echo $customer['shipping_firstname']; ?> <?php if($customer['shipping_lastname']) { echo $customer['shipping_lastname']; } ?>" placeholder="Full Name" />
        </div>
        <label class="col-sm-2 control-label">Company</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="company" class="form-control" value="<?php echo $customer['shipping_company']; ?>" placeholder="Company" />
        </div>
        <label class="col-sm-2 control-label">Address 1</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="address_1" class="form-control" value="<?php echo $customer['shipping_address_1']; ?>" placeholder="Address 1" />
        </div>
        <label class="col-sm-2 control-label">Address 2</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="address_2" class="form-control" value="<?php echo $customer['shipping_address_2']; ?>" placeholder="Address 2" />
        </div>
        <label class="col-sm-2 control-label">Postcode</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="postcode" class="form-control" value="<?php echo $customer['shipping_postcode']; ?>" placeholder="Postcode" />
        </div>
        <label class="col-sm-2 control-label">Country</label>
        <div class="col-sm-10 m-b-10">
            <select name="country_id" class="form-control" id="shipping_country_id" data-zone-id="<?php echo $customer['shipping_zone_id'] ?>">
                <?php foreach ($countries as $value) { ?>
                <option value="<?php echo $value['country_id'] ?>" <?php if($customer['shipping_country_id'] == $value['country_id']) { ?> selected="selected" <?php } ?> ><?php echo $value['name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">City</label>
        <div class="col-sm-10 m-b-10">
            <select name="zone_id" class="form-control" id="shipping_zone_id" data-area="<?php echo $customer['shipping_area'] ?>">
                <option value="">-- Select City --</option>
            </select>
        </div>
        <label class="col-sm-2 control-label">Area</label>
        <div class="col-sm-10 m-b-10 shipping_area_div">
            <?php if($customer['shipping_country_id'] == '221') { ?>
            <select name="area" class="form-control" id="shipping_city">
                <option value="">-- Select Area --</option>
            </select>
            <?php } else { ?>
            <input type="text" name="area" class="form-control" value="<?php echo $customer['shipping_area'] ?>" placeholder="Area" />
            <?php } ?>
        </div>
        <div class="col-sm-12">
            <button type="button" name="cutomer_shipping_address" id="cutomer_shipping_address" class="btn btn-success pull-right">Save Address</button>
        </div>       
    </div>
</form>