<?php

/**
 * Abstract class for open cart db connection
 */

 namespace App\Models\OpenCart;
 use Illuminate\Database\Eloquent\Model;

 /**
  * Description of AbstractOpenCartModel
  * @author Siraj Ali
  */

  abstract class AbstractOpenCartModel extends Model {
      public $timestamps = false;
      protected $connection = 'opencart';
  }