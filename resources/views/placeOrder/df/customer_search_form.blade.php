<form method="post" name="customer_save" id="customer_save">
    {{csrf_field()}}
    <div class="row">
        <input type="hidden" name="customer_id" value="{{ @$customer->id }}" />
        <div class="col-sm-4 p-10">
            <label>Full Name <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="firstname" class="form-control" value="{{ @$customer->firstname }}" required />
            </div>
        </div>
        <!-- <div class="col-sm-4 p-10">
            <label>Last Name <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="lastname" class="form-control" value="{{ @$customer->lastname }}" required />
            </div>
        </div> -->
        <div class="col-sm-4 p-10">
            <label>Email <span class="text-danger">*</span></label>
            <div>
                <input type="text" name="email" class="form-control" value="{{ @$customer->email }}" required />
            </div>
        </div>
        <div class="col-sm-4 p-10">
            <label>Telephone <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-sm-3 pr-0">
                    <select name="telephone_code" class="form-control" required >
                        @foreach ($countries as $key => $country)
                            @if( $country->phonecode != "" )
                                <option value="{{ $country->phonecode }}" {{  ($country->phonecode == 971) ?  'selected="selected"' : '' }} >{{ $country->phonecode . ' ' . $country->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-9 pl-0">
                  @if($country->phonecode == 971)
                    <input type="text" name="telephone" class="form-control" value="{{ @$customer->mobile }}" required />
                  @else
                    <input type="text" name="telephone" class="form-control" value="{{ @$customer->mobile }}" required />
                  @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4 p-10">
            <label>Country <span class="text-danger">*</span></label>
            <div>
                <select name="country_id" class="form-control" required >
                    @foreach ($countries as $key => $country)
                        <option value="{{ $country->id }}" {{ ( $country->id == $default_country ) ? 'selected="selected"' : '' }} >{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4 p-10">
            <label>City <span class="text-danger">*</span></label>
            <select name="city_id" id="city_id" class="form-control city_id" onchange="loadAreas()" required >
                <option>--Select--</option>
                @forelse ( $cities as $key => $val )
                    <option value="{{ $val->id }}" @selected( $val->id == @$customer->defaultAddress->city_id )>{{ $val->name }}</option>
                @empty
                @endforelse
            </select>
        </div>
        <div class="col-sm-4 p-10 area-box">
            <label>Area <span class="text-danger">*</span></label>
            <div>
                <select name="area_id" id="area" class="form-control" required readonly="readonly">
                    <option>--Select--</option>
                    @forelse($areas as $key => $area)
                        <option value="{{ $area->id }}" @selected( $area->id == @$customer->defaultAddress->area_id )>{{ $area->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-sm-4 p-10">
            <label>Address <span class="text-danger">*</span></label>
            <div>
                <textarea name="address" class="form-control" required >{{ @$customer->defaultAddress->address }}</textarea>
            </div>
        </div>
        <div class="col-sm-4 p-10">
          <label>Street,Building <span class="text-danger">*</span></label>
          <div>
              <input type="text" name="address_street_building" id="address_street_building" placeholder="street#, building name" value="{{ @$customer->defaultAddress->street_building }}" class="form-control" required>
          </div>
      </div>
      <div class="col-sm-4 p-10">
          <label>Villa, Flat <span class="text-danger">*</span></label>
          <div>
              <input type="text" name="address_villa_flate" id="address_villa_flate" placeholder="Villa, flat" value="{{ @$customer->defaultAddress->villa_flat }}" class="form-control" required>
          </div>
      </div>

    </div>
    <div class="row">
        <div class="col-sm-2 p-10 text-left">
          <label>Alternate number </label>
          <div>
              <input type="text" name="alternate_number" id="alternate_number" placeholder="Alternate Number" value="" class="form-control">
          </div>
       </div>
        <div class="col-sm-9 text-left">
          <label>Google Map Link</label>
          <div>
            <input type="text" name="gmap_link" id="gmap_link" placeholder="Google Map Link" value="" class="form-control">
          </div>
        </div>
        <div class="col-sm-1 text-right">
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
                        <th class="text-center">Store</th>
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
                        <td class="text-center"><?php echo $order['store_name'] ?></td>
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
<script>
    $(document).ready(function() {
        $('#area').select2();
        $('.city_id').select2();
    });

</script>
