<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class SocialModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_promotion_socials';
    protected $fillable = ['name','status'];
}
