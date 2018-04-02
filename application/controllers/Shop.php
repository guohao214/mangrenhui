<?php

/**
 * 店铺
 * User: GuoHao
 * Date: 2015/12/24
 * Time: 23:36
 */
class Shop extends FrontendController
{
  /**
   * 获得店铺
   */
  public function getList()
  {
    try {
     // $weChat = new WeixinUtil();
     // $openId = $weChat->getOpenId();

      $params = RequestUtil::getParams();
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $shops = (new ShopModel())->allShops();

      // 获得上一次下单的店铺
//      $lastShopId = '';
//      $order = (new OrderModel())->getLastOrder($openId);
//      if ($order)
//        $lastShopId = $order['shop_id'];

      $_shops = [];
      foreach ($shops as $key=>&$shop) {
        $distance = CommonUtil::distance($longitude, $latitude, $shop['longitude'], $shop['latitude']);
        $distance = round($distance);
        $_distance = $distance;
        if ($distance > 1000)
          $distance = round(($distance / 1000)) . 'km';
        else
          $distance = $distance . 'm';
        $shop['distance'] = $distance;
        $shop['_distance'] = $_distance;
        $_shops[$key] = $_distance;
        $shop['shop_logo'] = UploadUtil::buildUploadDocPath($shop['shop_logo'], '200x200');
      }

      unset($shop);

      // 排序
//      $_shop = [];
//      foreach ($shops as $key=>$shop) {
//        if ($shop['shop_id'] == $lastShopId) {
//          $_shop = $shop;
//          break;
//        }
//      }
//
//      array_unshift($shops, $_shop);
//      array_splice($shops, $key+1, 1);

      //sort($shops);

      // 根据距离排序
      unset($shop);
      asort($_shops);
      $cpShops = [];
      foreach ($_shops  as $key=>$value) {
        array_push($cpShops, $shops[$key]);
      }


      ResponseUtil::QuerySuccess($cpShops);
    } catch (Exception $e) {
      ResponseUtil::failure();
    }
  }
}