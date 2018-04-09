<?php

class Center extends FrontendController
{
  public function index()
  {
    $openId = (new WeixinUtil())->getOpenId();
    $customer = (new CustomerModel())->readOne($openId, CustomerModel::IS_CUSTOMER);
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
    $weixinUtil = new WeixinUtil();
    $openId = $weixinUtil->getOpenId();

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

    $appointmentDay = $order['appointment_day'];
    $startTime = $order['appointment_start_time'];
    $endTime = $order['appointment_end_time'];
    $shopId = $order['shop_id'];
    $beauticianId = $order['beautician_id'];
    $customerModel = (new CustomerModel());
    $orderProjectModel = (new OrderProjectModel());

    // 发送取消订单通知
    try {
      $customer = $customerModel->readOne($openId, CustomerModel::IS_CUSTOMER);
      $project = $orderProjectModel->readOneByOrderId($orderId);
      $toBeautician = $customerModel->getBeautician($beauticianId);
      $toFront = $customerModel->getFront();
      // 发送到自己
      $accessToken = $weixinUtil->getToken();
      $realEndTime = date('H:i', strtotime($appointmentDay . ' ' . $endTime) + 30 * 60);
      $appointmentDate = $appointmentDay . ' ' . $startTime . '~' . $realEndTime;

      $shops = (new ShopModel())->getAllShopAddress();
      $shop = $shops[$shopId];
      $beautician = (new BeauticianModel())->readOne($beauticianId);
      $beautician = $beautician['name'];
      $projectName = $project['project_name'];

      $now = DateUtil::now();
      // 发送给客户
      $weixinUtil->cancelOrder('您', $now, $appointmentDate, $shop, $beautician, $projectName, $openId, $accessToken);


      // 测试环境不发送给技师 和 前台
      if ($_SERVER['CI_ENV'] === 'production') {

        // 发送给技师
        if ($toBeautician)
          $weixinUtil->order($customer['nick_name'], $now, $appointmentDate, $shop, $beautician,
            $projectName, $toBeautician['open_id'], $accessToken);

        // 发送给前台
        if ($toFront && count($toFront) > 0) {
          foreach ($toFront as $front) {
            $weixinUtil->cancelOrder($customer['nick_name'], $now, $appointmentDate, $shop, $beautician,
              $projectName, $front['open_id'], $accessToken);
          }
        }
      }
    } catch (Exception $exception) {
      LogUtil::weixinLog('发送通知', '通知发送失败' . $exception->getMessage());
    }

    $status ? ResponseUtil::executeSuccess('订单取消成功！') : ResponseUtil::failure('取消订单失败!');
  }
}