<?php

class Center extends FrontendController
{
  public function index()
  {
    $unionId = (new WechatUtil())->getUnionId();
    $customer = (new CustomerModel())->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
    $this->view('center/index', array('customer' => $customer));
  }

  /**
   * @param $orderId
   */
  public function completeOrder($orderId)
  {
    $types = ['cash', 'scan', 'group'];

    $params = RequestUtil::postParams();
    $type = $params['type'];
    if (!in_array($type, $types))
      ResponseUtil::failure('支付类型不存在');

    $beauticianCode = $params['beautician_code'];
    $couponCode = $params['coupon_code'];
    $payContent = '';

    if ($type === 'cash') {
      if (!$beauticianCode)
        ResponseUtil::failure('请输入技师工号');
      else
        $payContent = $beauticianCode;
    }

    if ($type === 'group') {
      if (!$couponCode)
        ResponseUtil::failure('请输入券号');
      else
        $payContent = $couponCode;
    }

    $now = DateUtil::now();
    $where = ['order_id' => $orderId, 'disabled' => 0, 'order_status' => OrderModel::ORDER_APPOINTMENT];
    $data = ['pay_time' => $now, 'pay_type' => $type, 'pay_content' => $payContent, 'order_status' => OrderModel::ORDER_COMPLETE];

    $status = (new CurdUtil(new OrderModel()))->update($where, $data);

    //var_dump($this->db->last_query());
    $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();

  }

  public function order()
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    if (RequestUtil::isAjax()) {
      $orderModel = new OrderModel();
      $orders = $orderModel->getOrders($openId, $unionId);
      ResponseUtil::QuerySuccess($orders);
    }

  }

  /**
   * 取消订单
   * @param $orderId
   */
  public function cancelOrder($orderId)
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    $params = RequestUtil::getParams();
    $formId = $params['formId'];

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
    $status = (new CurdUtil(new OrderModel()))->update(array('order_id' => $orderId, 'union_id' => $unionId),
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
      $customer = $customerModel->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
      $project = $orderProjectModel->readOneByOrderId($orderId);
      $toBeautician = $customerModel->getBeautician($beauticianId);
      $toFront = $customerModel->getFront();
      // 发送到自己
      $accessToken = $wechat->getToken();
      $realEndTime = date('H:i', strtotime($appointmentDay . ' ' . $endTime) + 30 * 60);
      $appointmentDate = $appointmentDay . ' ' . $startTime . '~' . $realEndTime;

      $shops = (new ShopModel())->getAllShopAddress();
      $shop = $shops[$shopId];
      $beautician = (new BeauticianModel())->readOne($beauticianId);
      $beautician = $beautician['name'];
      $projectName = $project['project_name'];

      $now = DateUtil::now();
      // 发送给客户
      $wechat->cancelOrder($customer['nick_name'], $now, $appointmentDate, $shop, $beautician, $projectName, $openId, $accessToken, $formId);


      // 测试环境不发送给技师 和 前台
      if ($_SERVER['CI_ENV'] === 'production') {

        // 发送给技师
        if ($toBeautician) {
          $toOpenId = RequestUtil::isXcx() ? $toBeautician['xcx_open_id'] : $toBeautician['open_id'];
          if ($toOpenId)
            $wechat->cancelOrder($customer['nick_name'], $now, $appointmentDate, $shop, $beautician,
              $projectName, $toOpenId, $accessToken, $formId);
        }

        // 发送给前台
        if ($toFront && count($toFront) > 0) {
          foreach ($toFront as $front) {
            $toOpenId = RequestUtil::isXcx() ? $front['xcx_open_id'] : $front['open_id'];
            if ($toOpenId)
              $wechat->cancelOrder($customer['nick_name'], $now, $appointmentDate, $shop, $beautician,
                $projectName, $front['open_id'], $accessToken, $formId);
          }
        }
      }
    } catch (Exception $exception) {
      if (RequestUtil::isXcx())
        LogUtil::xcx('发送通知', '通知发送失败' . $exception->getMessage());
      else
        LogUtil::weixinLog('发送通知', '通知发送失败' . $exception->getMessage());
    }

    $status ? ResponseUtil::executeSuccess('订单取消成功！') : ResponseUtil::failure('取消订单失败!');
  }
}