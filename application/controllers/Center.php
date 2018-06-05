<?php

class Center extends FrontendController
{
  public function index()
  {
    $unionId = (new WechatUtil())->getUnionId();
    $customer = (new CustomerModel())->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
    $phone = ConfigUtil::loadConfig('phone');
    $this->view('center/index', array('customer' => $customer, 'phone' => $phone['phone']));
  }

  /**
   * @param $orderId
   */
  public function completeOrder($orderId)
  {
    $wechatUtil = new WechatUtil();
    $openId = $wechatUtil->getOpenId();
    $unionId = $wechatUtil->getUnionId();

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


      $grouponOrderListId = '';
      // 检测是否已经有团购订单， 根据手机号判断
      if (strlen($payContent) === 11 && preg_match('/^1\d{10}$/', $payContent)) {
        // 查询项目ID
        // 查询团购订单

        $appointmentOrder = (new CurdUtil(new OrderProjectModel()))->readOne(array(
          'order_id' => $orderId
        ));

        if (!$appointmentOrder)
          ResponseUtil::failure('订单不存在');

        $appointmentProjectId = $appointmentOrder['project_id'];

        $grouponOrderList = (new GrouponOrderListModel())->orderUseCounts($payContent, $appointmentProjectId);
        if (!$grouponOrderList)
          ResponseUtil::failure('券号错误');

        // 判断是否开团成功
        $grouponOrder = (new GrouponOrderModel())->getOneById($grouponOrderList['groupon_order_id']);
        if (!$grouponOrder)
          ResponseUtil::failure('拼团订单不存在');

        $grouponProjectCode = $grouponOrder['groupon_project_code'];
        $grouponOrderCode = $grouponOrder['groupon_order_code'];

        $ingOrder = (new GrouponOrderModel())->getNotFilterIngOrderByGrouponProjectCode($grouponProjectCode, $grouponOrderCode);
        if (!$ingOrder || count($ingOrder))
          ResponseUtil::failure('拼团订单不存在');

        $ingOrder = array_pop($ingOrder);
        if ($ingOrder['order_list_counts'] < $ingOrder['in_peoples'])
          ResponseUtil::failure('拼团尚未成功!');

        if ($grouponOrderList['use_counts'] >= $grouponOrderList['in_counts'])
          ResponseUtil::failure('团购使用次数已到最大');

        $grouponOrderListId = $grouponOrderList['groupon_order_list_id'];
      }
    }


    // 处理数据
    $this->db->trans_start();

    $now = DateUtil::now();
    $where = ['order_id' => $orderId, 'open_id' => $openId, 'disabled' => 0, 'order_status' => OrderModel::ORDER_APPOINTMENT];
    $data = ['pay_time' => $now, 'pay_type' => $type, 'pay_content' => $payContent, 'order_status' => OrderModel::ORDER_COMPLETE];

    $status = (new CurdUtil(new OrderModel()))->update($where, $data);
    if (!$status)
      ResponseUtil::failure();

    if ($grouponOrderListId) {
      $last = (new CurdUtil(new GrouponOrderUseModel()))->create(array(
        'order_id' => $orderId,
        'groupon_order_list_id' => $grouponOrderListId,
        'union_id' => $unionId
      ));

      if (!$last)
        ResponseUtil::fail();
    }

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      ResponseUtil::failure();
    } else {
      $this->db->trans_commit();
      ResponseUtil::executeSuccess();
    }
  }

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

    $this->view('center/order');

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
      $to = $customer['nick_name'];
      if (!$to)
        $to = $customer['phone'];
      // 发送给客户
      $wechat->cancelOrder($to, $now, $appointmentDate, $shop, $beautician, $projectName, $openId, $accessToken, $formId);


      // 测试环境不发送给技师 和 前台
      if ($_SERVER['CI_ENV'] === 'production') {
        $weixinUtil = new WeixinUtil();
        $accessToken = $weixinUtil->getToken();
        // 发送给技师
        if ($toBeautician) {
          $toOpenId = $toBeautician['open_id'];
          LogUtil::weixinLog("给技师：${toOpenId}发通知 ", $accessToken);
          if ($toOpenId)
            $weixinUtil->cancelOrder($to, $now, $appointmentDate, $shop, $beautician,
              $projectName, $toOpenId, $accessToken);
        }

        // 发送给前台
        if ($toFront && count($toFront) > 0) {
          foreach ($toFront as $front) {
            $toOpenId = $front['open_id'];
            LogUtil::weixinLog("给前台：${toOpenId}发通知 ", $accessToken);
            if ($toOpenId)
              $weixinUtil->cancelOrder($to, $now, $appointmentDate, $shop, $beautician,
                $projectName, $front['open_id'], $accessToken);
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

  public function getPhone()
  {
    $phone = ConfigUtil::loadConfig('phone');
    ResponseUtil::QuerySuccess($phone);
  }
}