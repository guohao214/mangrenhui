<?php

class Order extends BackendController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('OrderModel', 'orderModel');
  }

  public function index($limit = '')
  {
    $shopId = $this->input->get('shop_id') + 0;
    if ($shopId == 0)
      unset($_GET['shop_id']);
    else
      $this->db->where_in('shop_id', array(0, $shopId));

    $orderStatus = array(OrderModel::ORDER_APPOINTMENT => '已预约',
      OrderModel::ORDER_COMPLETE => '已完成',
      OrderModel::ORDER_CANCEL => '已取消');

    $payTypes = ['scan' => '店内扫码', 'cash' => '店内现金', 'group' => '团购', 'online' => '在线支付'];
    // 获得查询参数， 查询参数都为like模糊查询
    $params = RequestUtil::getParams();
    $_params = $params;

    $appointmentStartDay = $params['appointment_start_day'];
    $appointmentEndDay = $params['appointment_end_day'];

    $createdStartDay = $params['created_start_day'];
    $createdEndDay = $params['created_end_day'];


    unset($_GET['appointment_start_day'], $_GET['appointment_end_day']);

    $appointmentDayQuery = ['1=1'];
    if ($appointmentStartDay) {
      array_push($appointmentDayQuery, "appointment_day >='" . $appointmentStartDay . "'");
    }

    if ($appointmentEndDay) {
      array_push($appointmentDayQuery, "appointment_day <='" . $appointmentEndDay . "'");
    }

    $appointmentDayQuery = implode(' and ', $appointmentDayQuery);


    //
    $createdDayQuery = ['1=1'];

    unset($_GET['created_start_day'], $_GET['created_end_day']);
    if ($createdStartDay) {
      array_push($createdDayQuery, "created_time >='" . $createdStartDay . "'");
    }

    if ($createdEndDay) {
      array_push($createdDayQuery, "created_time <='" . $createdEndDay . "'");
    }

    $createdDayQuery = implode(' and ', $createdDayQuery);

    $where = RequestUtil::buildLikeQueryParamsWithDisabled();
    $where = $where . ' and (' . $appointmentDayQuery . ')';
    $where = $where . ' and (' . $createdDayQuery . ')';


    $_GET = $_params;

    $this->db->select('*');
    //$this->db->select('order_status+0 as order_sign', false);
    $orders = (new CurdUtil($this->orderModel))->readLimit($where, $limit, 'order_id desc');
    $ordersCount = (new CurdUtil($this->orderModel))->count($where);
    $pages = (new PaginationUtil($ordersCount))->pagination();
    $shops = (new ShopModel())->getAllShops();
    $beauticians = (new BeauticianModel())->getAllFormatBeauticians();
    $this->view('order/index', array('orders' => $orders, 'pages' => $pages, 'beauticians' => $beauticians,
      'orderCount' => $ordersCount, 'payTypes' => $payTypes,
      'params' => RequestUtil::getParams(), 'shops' => $shops, 'limit' => $limit, 'orderStatus' => $orderStatus));
  }

  public function orderDetail($order_no = '', $limit = 0)
  {
    if (!$order_no)
      $this->message('订单ID不能为空！');

    $order = (new OrderModel())->getOrder(array('order_no' => $order_no));
    if (!$order)
      $this->message('订单信息获取失败，请重试！');

    $order = array_shift($order);
    // 获得订单商品
    $orderProjects = (new OrderProjectModel())->getOrderProject($order['order_id']);
    $shops = (new ShopModel())->getAllShops();
    $this->view('order/orderDetail', array('order' => $order, 'shops' => $shops,
      'orderProjects' => $orderProjects, 'limit' => $limit));
  }

  /**
   * 删除订单
   * @param string $order_id
   */
  public function deleteOrder($order_id = '')
  {
    if (!$order_id)
      $this->message('订单ID不能为空！');

    if ((new CurdUtil($this->orderModel))->update(array('order_id' => $order_id), array('disabled' => 1)))
      $this->message('订单删除成功！');
    else
      $this->message('订单删除失败！');
  }

  /**
   * 取消订单
   * @param string $order_id
   */
  public function CancelOrder($order_id = '')
  {
    if (!$order_id)
      $this->message('订单ID不能为空！');

    $orderModel = new OrderModel();
    // 获得订单
    $order = $orderModel->readOne($order_id);
    if (!$order)
      ResponseUtil::failure('取消订单失败!');

//    // 获得积分ID
//    $couponId = $order['use_coupon_id'];
//    if ($couponId)
//      (new CustomerCouponModel())->refundCoupon($couponId, $order['open_id']);

    if ((new CurdUtil($this->orderModel))->update(array('order_id' => $order_id),
      array('order_status' => OrderModel::ORDER_CANCEL, 'complete_time' => DateUtil::now()))
    )
      $this->message('订单已取消！');
    else
      $this->message('处理失败！');
  }


  public function orderComplete($order_id = '')
  {
    if (!$order_id)
      $this->message('订单ID不能为空！');

    $orderModel = new OrderModel();
    // 获得订单
    $order = $orderModel->readOne($order_id);
    if (!$order)
      ResponseUtil::failure('取消订单失败!');

//    // 获得积分ID
//    $couponId = $order['use_coupon_id'];
//    if ($couponId)
//      (new CustomerCouponModel())->refundCoupon($couponId, $order['open_id']);

    if ((new CurdUtil($this->orderModel))->update(array('order_id' => $order_id),
      array('order_status' => OrderModel::ORDER_COMPLETE, 'complete_time' => DateUtil::now()))
    )
      $this->message('订单已完成！');
    else
      $this->message('处理失败！');
  }

  /**
   * 下载订单信息
   */
  public function download()
  {

  }
} 