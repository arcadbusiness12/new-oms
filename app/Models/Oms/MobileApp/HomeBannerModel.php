<?php

namespace App\Models\Oms\MobileApp;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class HomeBannerModel extends Model
{

  protected $table = 'map_home_banners';
 

  public function images()
  {
    return $this->hasMany(__NAMESPACE__ . '\HomeBannerImageModel', "home_banner_id","id");
  }

}
