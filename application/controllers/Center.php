<?php

class Center extends FrontendController
{
  public function index()
  {
    $openId = (new WeixinUtil())->getOpenId();
    $customer = (new CustomerModel())->readOne($openId);
    $this->view('center/index', array('customer' => $customer));
  }

  public function order()
  {
    $openId = (new WeixinUtil())->getOpenId();
    if (RequestUtil::isAjax()) {
      $orderModel = new OrderModel();
      $orders = $orderModel->getOrders($openId);
      ResponseUtil::QuerySuccess($orders);
    }
    $this->view('center/order');
  }

  /**
   * 取消订单
   * @param $orderId
   */
  public function cancelOrder($orderId)
  {
    $openId = (new WeixinUtil())->getOpenId();
    if (!$openId)
      ResponseUtil::failure('未授权访问！');

    if (!$orderId)
      ResponseUtil::failure('没有订单');

    $orderId += 0;

    $orderModel = new OrderModel();
    // 获得订单
    $order = $orderModel->readOne($orderId);
    if (!$order)
      ResponseUtil::failure('取消订单失败!');

    //取消订单
    $status = (new CurdUtil(new OrderModel()))->update(array('order_id' => $orderId, 'open_id' => $openId),
      array('order_status' => OrderModel::ORDER_CANCEL));

    $status ? ResponseUtil::executeSuccess('订单取消成功！') : ResponseUtil::failure('取消订单失败!');
  }
}