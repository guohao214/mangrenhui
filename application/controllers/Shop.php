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
      $shops = (new ShopModel())->allShops();
      foreach ($shops as &$shop) {
        $shop['shop_logo'] = UploadUtil::buildUploadDocPath($shop['shop_logo'], '200x200');
      }
      ResponseUtil::QuerySuccess($shops);
    } catch (Exception $e) {
      ResponseUtil::failure();
    }
  }
}