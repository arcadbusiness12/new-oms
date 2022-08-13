<?php 

/*
* Abstract Class for dressfair opencart db connection
*/

namespace App\Models\DressFairOpenCart;
use Illuminate\Database\Eloquent\Model;

/** 
* Description of AbstractOpenCartModel
*
* @author Siraj Ali
*/

abstract class AbstractDressFairOpenCartModel extends Model {
    public $timestamps = false;
    protected $connection = 'dressfair_opencart'; 
}