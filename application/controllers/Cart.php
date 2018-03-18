<?php

class Cart extends FrontendController
{
  /**
   * 下单
   * @param $shopId
   * @param $beauticianId
   * @param $appointmentDay
   * @param $appointmentTime
   * @param $userName
   * @param $phoneNumber
   */
  public function appointment()
  {
    $params = RequestUtil::postParams();

    $shopId = $params['shop_id'] + 0;
    $projectId = $params['project_id'] + 0;
    $beauticianId = $params['beautician_id'] + 0;
    $appointmentDay = $params['appointment_day'];
    $appointmentTime = $params['appointment_time'];

    $openId = (new WeixinUtil())->getOpenId();


    //$openId = (new WeixinUtil())->getOpenId();
//    if (!$openId)
//      ResponseUtil::failure('错误的授权');

    if (!(new ShopModel())->isValidShopId($shopId))
       ResponseUtil::failure('门店信息错误，请检查！');

    // 检查技师
    if (!(new BeauticianModel())->isValidBeautician($beauticianId))
      ResponseUtil::failure('技师信息错误，请检查！');

    // 检查预约日期
    $today = date('Y-m-d');
    if ($appointmentDay < $today)
      ResponseUtil::failure('错误的预约日期！');

    // 检查时间
    $appointmentTime = explode(',', urldecode($appointmentTime));
    if (!$appointmentTime || count($appointmentTime) < 1)
      ResponseUtil::failure('错误的预约时间！');

    // 只有30分钟的项目
    if (count($appointmentTime) == 1)
      array_push($appointmentTime, $appointmentTime[0]);

    // 只保留头和尾的两个数据
    $startTime = array_shift($appointmentTime);
    $endTime = array_pop($appointmentTime);
    if ($endTime < $startTime)
      ResponseUtil::failure('错误的预约时间！');

    // 预约时间是否小于当前时间
    $now = date('Y-m-d H:i');
    if (DateUtil::buildDateTime($appointmentDay, $startTime) < $now)
      ResponseUtil::failure('错误的预约开始时间！');
    if (DateUtil::buildDateTime($appointmentDay, $endTime) < $now)
      ResponseUtil::failure('错误的预约结束时间！');


    //**********处理下单************//
    if (empty($projectId) || $projectId <= 0)
      ResponseUtil::failure('预约项目为空！');


    // 判断是否已经有下单的

    $orderProjectModel = new OrderProjectModel();
    // 获得购物车的项目
    $project = (new ProjectModel())->readOne($projectId);
    // 生成订单号
    $orderNo = StringUtil::generateOrderNo();
    $orderModel = new OrderModel();

    // 订单数据
    $orderData = array(
      'order_no' => $orderNo,
      'shop_id' => $shopId,
      'created_time' => DateUtil::now(),
      'open_id' => $openId,
      'beautician_id' => $beauticianId,
      'appointment_day' => $appointmentDay,
      'appointment_start_time' => $startTime,
      'appointment_end_time' => $endTime,
      'phone_number' => '135',
      'total_fee' => $project['price']
    );

    // 事务开始
    $this->db->trans_start();
    $insertOrderNo = (new CurdUtil($orderModel))->create($orderData);
    if ($insertOrderNo) {
      $orderProjectData = array(
        'order_id' => $insertOrderNo,
        'project_id' => $project['project_id'],
        'use_time' => $project['use_time'],
        'created_time' => DateUtil::now(),
        'project_name' => $project['project_name'],
        'project_cover' => $project['project_cover'],
        'price' => $project['price']
      );
    } else {
      ResponseUtil::failure('提交订单失败，请重试！');
    }

    (new CurdUtil($orderProjectModel))->create($orderProjectData);
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      ResponseUtil::failure('提交订单失败，请重试!');
    } else {
      $this->db->trans_commit();
      ResponseUtil::executeSuccess('提交订单成功', $orderNo);
    }
  }
}