<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of ShippingProvidersModel
 *
 * @author kamran
 */
class ShippingProvidersModel extends Model
{

  protected $table = 'shipping_providers';
  protected $primaryKey = 'shipping_provider_id';

  const FIELD_SHIPPING_PROVIDER_ID = 'shipping_provider_id';
  const FIELD_SHIPPING_PROVIDER_NAME = 'name';
  const FIELD_SHIPPING_PROVIDER_IS_ACTIVE = 'is_active';

}
