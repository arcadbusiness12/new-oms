<form name="payment_address" method="post">
    <div class="row">
        <label class="col-sm-2 control-label">Full Name</label>
        <div class="col-sm-10 m-b-10">
            <input type="hidden" name="payment_address" value="<?php echo $customer['payment_address']; ?>"/>
            <input type="text" name="firstname" class="form-control" value="<?php echo $customer['payment_firstname']; ?> <?php if($customer['payment_lastname']) { echo $customer['payment_lastname']; } ?>" placeholder="Full Name" />
        </div>
        <!-- <label class="col-sm-2 control-label">Last Name</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="lastname" class="form-control" value="<?php echo $customer['payment_lastname']; ?>" placeholder="Last Name" />
        </div> -->
        <label class="col-sm-2 control-label">Company</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="company" class="form-control" value="<?php echo $customer['payment_company']; ?>" placeholder="Company" />
        </div>
        <label class="col-sm-2 control-label">Address 1</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="address_1" class="form-control" value="<?php echo $customer['payment_address_1']; ?>" placeholder="Address 1" />
        </div>
        <label class="col-sm-2 control-label">Address 2</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="address_2" class="form-control" value="<?php echo $customer['payment_address_2']; ?>" placeholder="Address 2" />
        </div>
        <!-- <label class="col-sm-2 control-label">City</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="city" class="form-control" value="<?php echo $customer['payment_city']; ?>" placeholder="City" />
        </div> -->
        <label class="col-sm-2 control-label">Postcode</label>
        <div class="col-sm-10 m-b-10">
            <input type="text" name="postcode" class="form-control" value="<?php echo $customer['payment_postcode']; ?>" placeholder="Postcode" />
        </div>
        <label class="col-sm-2 control-label">Country</label>
        <div class="col-sm-10 m-b-10">
            <select name="country_id" class="form-control" id="payment_country_id" data-zone-id="<?php echo $customer['payment_zone_id'] ?>">
                <?php foreach ($countries as $value) { ?>
                <option value="<?php echo $value['country_id'] ?>" <?php if($customer['payment_country_id'] == $value['country_id']) { ?> selected="selected" <?php } ?> ><?php echo $value['name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">City</label>
        <div class="col-sm-10 m-b-10">
            <select name="zone_id" id="payment_zone_id" class="form-control" data-area="<?php echo $customer['payment_area'] ?>">
                <option value="">-- Select City --</option>
            </select>
        </div>
        <label class="col-sm-2 control-label">Area</label>
        <div class="col-sm-10 m-b-10 payment_area_div">
            <?php if($customer['payment_country_id'] == '221') { ?>
            <select name="area" class="form-control" id="payment_city">
                <option value="">-- Select Area --</option>
            </select>
            <?php } else { ?>
            <input type="text" name="area" class="form-control" value="<?php echo $customer['payment_area'] ?>" placeholder="Area" />
            <?php } ?>
        </div>
        <div class="col-sm-12">
            <button type="button" name="back_cutomer_payment_address" id="back_cutomer_payment_address" class="btn btn-success pull-left">Back</button>
            <button type="button" name="cutomer_payment_address" id="cutomer_payment_address" class="btn btn-success pull-right">Save Address</button>
        </div>       
    </div>
</form>