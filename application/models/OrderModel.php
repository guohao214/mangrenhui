<?php

class OrderModel extends BaseModel
{
  const ORDER_APPOINTMENT = 1; //已预约
  const ORDER_CANCEL = 2; // 已取消
  const ORDER_COMPLETE = 100; //已完成

  public function setTable()
  {
    $this->table = 'order';
  }

  public function orders($where = array())
  {
    $this->db->select('*');
    $this->db->select('order_status+0 as order_sign', false);
    $this->db->from($this->table);
    $this->db->where($where);

    $query = $this->db->get();

    return $query->result_array();
  }

  /**
   * 设置订单为已支付
   * @param $orderNo
   * @param $openId
   * @param $wxOrderNo 微信订单号
   */
  public function payed($orderNo, $wxOrderNo)
  {
    $where = array('order_no' => $orderNo);

    $updateData = array(
      'pay_time' => DateUtil::now(),
      'order_status' => self::ORDER_PAYED,
      'transaction_id' => $wxOrderNo,
    );

    $this->db->where($where);
    $this->db->update($this->table, $updateData);

    return $this->db->affected_rows();
  }

  /**
   * @param $where
   * @param $orderStatus
   * @return mixed
   */
  public function getOrder($where = array(), $orderStatus = 0)
  {
    $this->db->from($this->table);
    $orderProject = (new OrderProjectModel())->table;
    $beautician = (new BeauticianModel())->table;
    $this->db->select("{$this->table}.*, {$orderProject}.*, {$beautician}.name as beautician_name");
    $this->db->join($orderProject, "{$this->table}.order_id={$orderProject}.order_id");
    $this->db->join($beautician, "{$this->table}.beautician_id={$beautician}.beautician_id");
    $this->db->where("{$this->table}.disabled=0");
    $this->db->order_by("{$this->table}.order_id desc");

    if ($where)
      $this->db->where($where);

    if ($orderStatus)
      $this->db->where("{$this->table}.order_status=" . $orderStatus);

    $sql = $this->db->get_compiled_select();
    return (new CurdUtil($this))->query($sql);
  }


  /**
   * 我的订单
   * @param $openId
   * @param int $orderStatus
   * @param int $offset
   * @return mixed
   */
  public function getOrders($openId, $unionId)
  {
    $paginationConfig = ConfigUtil::loadConfig('user_center');
    //$rows = $paginationConfig['per_page'];

    $sql = "select a.*, b.*, d.shop_name, c.name as beautician_name, a.order_status+0 as order_sign from `order` as a"
      . " left join order_project as b on a.order_id=b.order_id"
      . " left join beautician as c on a.beautician_id=c.beautician_id left join shop as d on a.shop_id = d.shop_id ";

    $orderStatusWhere = '';

    $sql .= " where a.disabled=0 and a.open_id='{$openId}' or union_id='{$unionId}'{$orderStatusWhere}"
      . " order by a.order_id desc";

    return (new CurdUtil($this))->query($sql);
  }

  /**
   * 计算我的订单总数
   * @param $openId
   * @param int $orderStatus
   * @return mixed
   */
  public function getUserOrderCounts($openId, $orderStatus = 0)
  {
    $sql = "select count(*) as rowCounts from `order` where open_id='{$openId}'";

    if ($orderStatus)
      $sql .= ' and `order`.order_status=' . $orderStatus;

    $sql .= ' and `order`.disabled=0';

    return (new CurdUtil($this))->query($sql);
  }

  /**
   * 根据美容师ID 与预约时间 获得订单
   * @param $beauticianId
   * @param $appointmentDay
   */
  public function getOrderByBeauticianIdAndAppointmentDay($beauticianId, $appointmentDay)
  {
    $where = array('beautician_id' => $beauticianId, 'appointment_day' => $appointmentDay,
      'disabled' => 0);

    $this->db->or_where_in('order_status', [OrderModel::ORDER_APPOINTMENT, OrderModel::ORDER_COMPLETE]);

    return (new CurdUtil($this))->readAll('order_id desc', $where);
  }

  /**
   * 通过美容师ID统计美容师的接单数量
   * @param string $beautician_id
   */
  public function getOrderCountsByBeauticianId()
  {
    $status = self::ORDER_COMPLETE;
    $sql = "select count(*) as rows_count, beautician_id from `{$this->table}` where disabled=0 and order_status={$status} group by beautician_id";

    $orderGroup = (new CurdUtil($this))->query($sql);
    if ($orderGroup) {
      $_orderGroup = array();
      foreach ($orderGroup as $group) {
        $_orderGroup[$group['beautician_id']] = $group['rows_count'];
      }
    }

    return $_orderGroup;
  }

  /**
   * 获得最后一次有效订单的信息
   * @param $openId
   * @return array
   */
  public function getLastOrder($openId)
  {
    $this->db->from($this->table);
    $this->db->where(array('open_id' => $openId, 'disabled' => 0));
    $this->db->order_by('order_id desc');
    $this->db->limit(1, 0);

    $query = $this->db->get();
    $result = $query->result_array();

    return array_pop($result);
  }

  public function readOne($orderId)
  {
    return (new CurdUtil($this))->
    readOne(array('order_id' => $orderId, 'disabled' => 0));
  }

  /**
   * 获得消费金额
   * @param $openId
   * @return float
   */
  public function calcAmountByOpenId($openId)
  {
    $payedStatus = self::ORDER_PAYED;
    $consumedStatus = self::ORDER_CONSUMED;

    $sql = "select sum(total_fee) as total_fee 
                  from `order` 
                  where open_id='{$openId}' 
                  and (order_status={$payedStatus} or order_status={$consumedStatus})  
                  and disabled=0;";

    $result = (new CurdUtil($this))->query($sql);

    if ($result && $result[0]['total_fee'])
      return $result[0]['total_fee'];
    else
      return 0;

  }

  /**
   * 判断时间是否已经被预约
   * @param $shopId
   * @param $beauticianId
   * @param $appointmentDay
   * @param $appointmentTime
   */
  public function isAppointment($shopId, $beauticianId, $appointmentDay, $appointmentStartTime, $appointmentEndTime) {
    $where = [
      'shop_id' => $shopId,
      'beautician_id' => $beauticianId,
      'appointment_day' => $appointmentDay,
      'appointment_start_time' => $appointmentStartTime,
      'appointment_end_time' => $appointmentEndTime,
      'order_status' => self::ORDER_APPOINTMENT,
      'disabled' => 0
    ];

    return (new CurdUtil($this))->readOne($where);
  }

}