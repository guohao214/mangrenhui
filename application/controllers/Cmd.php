<?php

  class Cmd extends CI_Controller {

  /**
   * 清除无效的， 5分钟过期的订单
   */
  public function clearInvalidOrders() {
    $max = 5 * 60; //5分钟

    $grouponOrderListModel = new GrouponOrderListModel();
    $orders = $grouponOrderListModel->getInvalidOrders($max);
    foreach($orders as $order) {
      LogUtil::weixinLog('取消过期的拼团订单', $order);

      (new CurdUtil($grouponOrderListModel))
        ->update(array('groupon_order_list_no' => $order['groupon_order_list_no']), array('disabled' => 1));
    }
  }
  }