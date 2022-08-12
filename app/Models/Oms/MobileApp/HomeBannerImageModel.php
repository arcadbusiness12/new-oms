<?php

namespace App\Models\Oms\MobileApp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class HomeBannerImageModel extends Model
{

  protected $table = 'map_home_banner_images';
 
  public function getImageAttribute($value)
  {
    return Storage::url($value);
  }

}
