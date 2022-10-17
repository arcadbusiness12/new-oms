<?php

namespace App\Models\Oms\InventoryManagement\Attribute;
use Illuminate\Database\Eloquent\Model;

class AttributeGroupModel extends Model
{
    protected $table = "attribute_groups";

    public function attributes(){
        return $this->hasMany(AttributeModel::class,"attribute_group_id");
    }

}
