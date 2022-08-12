<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Model;

class OmsOptionValueDescriptionModel extends Model
{
    protected $table = 'oc_option_value_description';
    protected $primaryKey = "option_value_id";

    public function productOption()
    {
        return $this->hasOne(OmsInventoryProductOptionModel::class, 'option_value_id');
    }
}
