<?php

class Appointment extends FrontendController
{
  /**
   * 预约界面
   */
  public function index()
  {
    // 绑定账号
    $unionId = (new WechatUtil())->getUnionId();
    $customer = (new CustomerModel())->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
    if (!$customer || !$customer['phone'])
      ResponseUtil::redirect(UrlUtil::createUrl('bind/index'));

    $this->view('appointment/index');
  }

  public function getBeauticianAndProject($shop_id) {
    $shop_id = $shop_id + 0;

    try {
      $days = DateUtil::buildDays();
      $beauticians = (new BeauticianModel())->getAllBeauticians(array('shop_id' => $shop_id));
      foreach ($beauticians as &$beautician) {
        $beautician['avatar'] = UploadUtil::buildUploadDocPath($beautician['avatar'], '200x200');
      }
      $projects = (new CurdUtil(new ProjectModel()))->readLimit(array('disabled' => 0, 'shop_id' => $shop_id));
      foreach ($projects as &$project) {
        $project['project_cover'] = UploadUtil::buildUploadDocPath($project['project_cover'], '200x200');
      }

      ResponseUtil::QuerySuccess(array('projects' => $projects, 'beauticians' => $beauticians, 'days' => $days ));
    } catch (Exception $e) {
      ResponseUtil::failure();
    }

  }

  /**
   * 获得有效的预约时间
   * 首先查询 指定日期 技师休息表， 获得休息时间
   * 再查询 指定日期 技师 已接受预定的 时间
   *
   * @param $beautician_id 技师ID
   * @param $day 查询日期
   */
  public function getAppointmentTime($beautician_id, $day)
  {
    if (!$beautician_id || !$day)
      ResponseUtil::failure('参数错误!');

    $today = date('Y-m-d');
    if ($day < $today)
      ResponseUtil::failure('错误的预约时间！');

    // 查询技师
    $beautician = (new BeauticianModel())->readOne($beautician_id);
    if (!$beautician)
      ResponseUtil::failure('技师不存在！');

    // 查询休息时间
    $beauticianRest = (new CurdUtil(new BeauticianRestModel()))
      ->readAll(
        'beautician_rest_id desc',
        array('beautician_id' => $beautician_id,
          'disabled' => 0,
          'rest_day' => $day));

    // 获得工作时间
    $workTime = new WorkTimeUtil();
    list($dayStart, $dayEnd) = $workTime->explode($workTime->getAllDay());

    // 指定日期的所有预约时间段
    $appointmentTimes = DateUtil::generateAppointmentTime($day, $dayStart, $dayEnd);

    // 技师制定日期休息时间段
    // 当值为0时， 说明不能预约
    if ($beauticianRest) {
      foreach ($beauticianRest as $_beauticianRest) {
        $beauticianRestAppointmentTimes = DateUtil::generateAppointmentTime($day,
          $_beauticianRest['start_time'], $_beauticianRest['end_time']);

        foreach ($appointmentTimes as $k => $time) {
          if (array_key_exists($k, $beauticianRestAppointmentTimes))
            $appointmentTimes[$k] = 0;
        }
      }
    }

    // 获得制定日期已经预约的时间段，订单状态为已支付
    $payedOrders = (new OrderModel())->getOrderByBeauticianIdAndAppointmentDay($beautician_id, $day);
    if ($payedOrders) {
      foreach ($payedOrders as $payedOrder) {
        $orderAppointmentTime = DateUtil::generateAppointmentTime($payedOrder['appointment_day'],
          $payedOrder['appointment_start_time'], $payedOrder['appointment_end_time']);
        foreach ($appointmentTimes as $k => $time) {
          if (array_key_exists($k, $orderAppointmentTime))
            $appointmentTimes[$k] = 0;
        }
      }
    }

    // 小于当前时间不能预约
    if ($today == $day) {
      $now = date('H:i');
      foreach ($appointmentTimes as $k => $time) {
        if ($k < $now)
          $appointmentTimes[$k] = 0;
      }
    }

    $beauticianWorkTime = (new WorkTimeUtil())->beauticianWorkTime;
    $week = DateUtil::calcDayInWeek($day);
    $workTimeType = $beauticianWorkTime[$beautician_id][$week];
    // 判断早班，晚班
    if ($workTimeType == BeauticianModel::ALL_DAY) {
      ;
    } elseif ($workTimeType == BeauticianModel::MORNING_SHIFT) {
      $morningShiftTimes = $workTime->explode($workTime->getMorningShift());
      $workAppointmentTime = DateUtil::generateAppointmentTime($day, $morningShiftTimes[0], $morningShiftTimes[1]);
      foreach ($appointmentTimes as $k => $time) {
        if (!array_key_exists($k, $workAppointmentTime))
          $appointmentTimes[$k] = 0;
      }

    } elseif ($workTimeType == BeauticianModel::NIGHT_SHIFT) {
      $nightShiftTimes = $workTime->explode($workTime->getNightShift());
      $workAppointmentTime = DateUtil::generateAppointmentTime($day, $nightShiftTimes[0], $nightShiftTimes[1]);
      foreach ($appointmentTimes as $k => $time) {
        if (!array_key_exists($k, $workAppointmentTime))
          $appointmentTimes[$k] = 0;
      }
    } elseif ($workTimeType == BeauticianModel::MIDDAY_SHIFT) {
      $middayShiftTimes = $workTime->explode($workTime->getMiddayShift());
      $workAppointmentTime = DateUtil::generateAppointmentTime($day, $middayShiftTimes[0], $middayShiftTimes[1]);
      foreach ($appointmentTimes as $k => $time) {
        if (!array_key_exists($k, $workAppointmentTime))
          $appointmentTimes[$k] = 0;
      }
    } elseif ($workTimeType == BeauticianModel::REST_SHIFT) {
      foreach ($appointmentTimes as $k => $time) {
        $appointmentTimes[$k] = 0;
      }
    } else {
      ;
    }

    $format = [];
    foreach ($appointmentTimes as $time => $k) {
      $format[] = array('time' => $time, 'valid' => $k);
    }

    ResponseUtil::QuerySuccess($format);
  }

}