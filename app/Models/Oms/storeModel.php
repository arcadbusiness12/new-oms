<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class storeModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_store';
    protected $fillable = ['name','status'];
}
