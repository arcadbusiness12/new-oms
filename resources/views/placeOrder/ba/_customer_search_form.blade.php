<form method="post" name="customer_save" id="customer_save">
    {{csrf_field()}}
    <div class="row">
        <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id'] ?>" />
        <input type="hidden" name="customer_group_id" value="<?php echo $customer['customer_group_id'] ?>" />
        <div class="col-sm-4 p-b-15">
            <label>Full Name <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="firstname" class="form-control" value="<?php echo $customer['firstname'] ?><?php if($customer['lastname']) { echo " " . $customer['lastname']; } ?>" required />
            </div>
        </div>
        <!-- <div class="col-sm-4 p-b-15">
            <label>Last Name <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="lastname" class="form-control" value="<?php echo $customer['lastname'] ?>" required />
            </div>
        </div> -->
        <div class="col-sm-4 p-b-15">
            <label>Email <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="email" class="form-control" value="<?php echo $customer['email'] ?>" required />
            </div>
        </div>
        <div class="col-sm-4 p-b-15 telephone-box">
            <label>Telephone <span class="text-danger">*</span></label>
            <div class="inner-block">
                <select name="telephone_code" class="form-control" required >
                    <?php foreach ($login_countries as $key => $country) { ?>
                    <option value="<?php echo $country['phonecode'] ?>" <?php if($country['phonecode'] == 971) { ?> selected="selected" <?php } ?> ><?php echo $country['phonecode'] . ' ' . $country['nicename'] ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="telephone" class="form-control" value="<?php echo $customer['telephone'] ?>" required />
            </div>
        </div>
        <!-- <div class="col-sm-4 p-b-15">
            <label>Fax</label>
            <div>
                <input type="text" name="fax" class="form-control" value="<?php echo $customer['fax'] ?>" />
            </div>
        </div> -->
        <div class="col-sm-4 p-b-15">
            <label>Country <span class="text-danger">*</span></label>
            <div>
                <select name="country_id" class="form-control" required >
                    <?php foreach ($countries as $key => $country) { ?>
                    <option value="<?php echo $country['country_id'] ?>" <?php if($country['country_id'] == $customer['country_id'] || $country['country_id'] == 221) { ?> selected="selected" <?php } ?> ><?php echo $country['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-4 p-b-15">
            <label>City <span class="text-danger">*</span></label>
            <div>
                <select name="zone_id" id="zone_id" class="form-control" data-area="<?php echo $customer['area'] ?>" data-zone-id="<?php echo $customer['zone_id'] ?>" required ></select>
            </div>
        </div>
        <!-- <div class="col-sm-4 p-b-15">
            <label>City <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="city" class="form-control" value="<?php echo $customer['city'] ?>" required />
            </div>
        </div> -->
        <div class="col-sm-4 p-b-15 area-box">
            <label>Area <span class="text-danger">*</span></label>
            <div>
                <select name="area" class="form-control" required ></select>
            </div>
        </div>
        <div class="col-sm-4 p-b-15">
            <label>Address <span class="text-danger">*</span></label>
            <div>
                <textarea name="address_1" class="form-control" required ><?php echo $customer['address_1'] ?></textarea>
            </div>
        </div>
        <div class="col-sm-8 text-right">
            <label>&nbsp;</label>
            <div>
                <button type="submit" name="save_customer" id="save_customer" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
    
    <?php if($orders) { ?>
    <div class="row">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">User</th>
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $key => $order) { ?>
                    <tr>
                        <td class="text-center"><?php echo $order['user'] ?></td>
                        <td class="text-center">#<?php echo $order['order_id'] ?></td>
                        <td class="text-center"><?php echo $order['name'] ?></td>
                        <td class="text-center"><?php echo $order['products'] ?></td>
                        <td class="text-center"><?php echo $order['status'] ? $order['status']->name : '' ?></td>
                        <td class="text-center"><?php echo $order['total'] ?></td>
                        <td class="text-center"><?php echo $order['date_added'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</form>