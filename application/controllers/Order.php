<?php

class Order extends FrontendController
{
  /**
   * 订单支付
   * @param $orderNo
   */
  public function pay($orderNo)
  {
    // 是否授权
    $wechatUtil = new WechatUtil();
    $openId = $wechatUtil->getOpenId();
    $unionId = $wechatUtil->getUnionId();

    if (!$openId)
      ResponseUtil::failure('小程序未授权');

    // 获得订单信息
    $where = array('order_no' => $orderNo, 'union_id' => $unionId);
    $orders = (new OrderModel())->getOrder($where, OrderModel::ORDER_APPOINTMENT);

    if (!$orders)
      ResponseUtil::failure('订单不存在！');

    if (!isset($orders[0]))
      ResponseUtil::failure('订单不存在！');

    // 如果有多条， 获得第一条的订单记录
    $order = array_shift($orders);
    if ($order['order_status'] == OrderModel::ORDER_COMPLETE)
      ResponseUtil::failure('订单已经支付！');

    // 订单时间, 2个小时过期
    // if (!DateUtil::orderIsValidDate($order['create_time']))
    //   $this->message('订单已经过期!');

    // 判断相同的时间是否已经被预约
    $findHasPayedAppointTimeWhere = array('appointment_day' => $order['appointment_day'],
      'appointment_start_time' => $order['appointment_start_time'],
      'order_status' => OrderModel::ORDER_COMPLETE, 'beautician_id' => $order['beautician_id']);
    $findOrder = (new CurdUtil(new OrderModel()))->readOne($findHasPayedAppointTimeWhere);
    if ($findOrder)
      ResponseUtil::failure('由于您未能及时付款，此时间段已被预约!');

    // 获得预付款ID
    $config = RequestUtil::isXcx()  ? 'wxxcx' : 'weixin';
    $weixinPay = new WeixinPayUtil($config);
    $prePayId = $weixinPay->fetchPrepayId($openId, '盲人荟按摩', $orderNo, $order['total_fee']);
    LogUtil::xcx('预付款ID：', $prePayId);
    if (!$prePayId)
      ResponseUtil::failure('获得微信预付款ID失败，请重试！');

    //生成支付参数
    $payParams = $weixinPay->getParameters($prePayId);
    LogUtil::xcx('支付参数：', $payParams);

    try {
      $payParams = json_decode($payParams);
      ResponseUtil::QuerySuccess($payParams);
    } catch (Exception $exception) {
      ResponseUtil::failure('获得支付参数失败');
    }
  }

}