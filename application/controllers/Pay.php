<?php

class Pay extends FrontendController
{
  public function order()
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    $params = RequestUtil::getParams();
    $orderStatus = $params['order_status'];

    if (RequestUtil::isAjax()) {
      $orderModel = new OrderModel();
      $orders = $orderModel->getOrders($openId, $unionId, $orderStatus);
      ResponseUtil::QuerySuccess($orders);
    }

    $this->view('pay/order');
  }
}