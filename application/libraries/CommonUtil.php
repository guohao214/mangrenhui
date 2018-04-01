<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2018/4/1
 * Time: 16:56
 */

class CommonUtil
{
  public static function  #lon为经度，lat为纬度，一定不要弄错了哦
  distance($lon1, $lat1, $lon2, $lat2)
  {
    return (2 * ATAN2(SQRT(SIN(($lat1 - $lat2) * PI() / 180 / 2)
          * SIN(($lat1 - $lat2) * PI() / 180 / 2) +
          COS($lat2 * PI() / 180) * COS($lat1 * PI() / 180)
          * SIN(($lon1 - $lon2) * PI() / 180 / 2)
          * SIN(($lon1 - $lon2) * PI() / 180 / 2)),
          SQRT(1 - SIN(($lat1 - $lat2) * PI() / 180 / 2)
            * SIN(($lat1 - $lat2) * PI() / 180 / 2)
            + COS($lat2 * PI() / 180) * COS($lat1 * PI() / 180)
            * SIN(($lon1 - $lon2) * PI() / 180 / 2)
            * SIN(($lon1 - $lon2) * PI() / 180 / 2)))) * 6378140;
  }
}