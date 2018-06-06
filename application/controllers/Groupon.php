<?php

/**
 * 拼团
 */
class Groupon extends FrontendController
{

  public function grouponIndex($grouponProjectCode, $grouponOrderCode = '')
  {
    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);

    $sharePage = $this->load->view('frontend/groupon/sharePage', array('grouponProject' => $grouponProject), true);

    $this->load->view('frontend/groupon/index',
      array('groupon_project_code' => $grouponProjectCode, 'groupon_order_code' => $grouponOrderCode, 'sharePage' => $sharePage));
  }

  /**
   * 发送验证码
   */
  public function sendSmsCode($phone, $listNo)
  {
    if (!$phone || !$listNo)
      ResponseUtil::failure();

    $openId = (new WechatUtil())->getOpenId();
    try {

      // 获得订单
      $findOrder = (new GrouponOrderListModel())->getOrder($openId, $listNo);
      if (!$findOrder)
        ResponseUtil::failure('订单不存在');
      // 判断 手机号是否参与了团
      $grouponProjectCode = $findOrder['ggroupon_project_code'];
      $projectId = $findOrder['project_id'];
      if (!(new GrouponOrderListModel())->phoneNumberIsNotJoin($grouponProjectCode, $projectId, $phone))
        ResponseUtil::failure('此手机号已经参与了此团');

      $status = (new SmsCodeModel())->sendCode($phone, SmsCodeModel::GROUPON_ORDER, '您的拼团手机验证码为:');
      $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();
    } catch (Exception $e) {
      ResponseUtil::failure();
    }
  }

  /**
   * 拼团项目信息
   */
  public function getGrouponProject($grouponProjectCode)
  {
    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);

    if (!$grouponProject)
      ResponseUtil::failure('拼团项目不存在');

    // 是否已过期
    // 未开始
    $now = DateUtil::now();
    if ($now < $grouponProject['start_time'])
      $grouponProject['__type'] = 'wait';
    else if ($now > $grouponProject['start_time'] && $now < $grouponProject['end_time'])
      $grouponProject['__type'] = 'ing';
    else
      $grouponProject['__type'] = 'end';


    $grouponProject['start_time'] = date('Y-m-d', strtotime($grouponProject['start_time']));
    $grouponProject['end_time'] = date('Y-m-d', strtotime($grouponProject['end_time']));
    $grouponProject['notice'] = nl2br($grouponProject['notice']);

    ResponseUtil::QuerySuccess($grouponProject);
  }

  /**
   * 进行中的团
   * $grouponOrderCode 筛选的拼团订单
   */
  public function getGrouponIngOrders($grouponProjectCode, $grouponOrderCode = '')
  {
    try {
      $wechat = new WechatUtil();
      $openId = $wechat->getOpenId();
      $unionId = $wechat->getUnionId();

      $ingOrders = (new GrouponOrderModel())->getIngOrderByGrouponProjectCode($grouponProjectCode);
      ResponseUtil::QuerySuccess($ingOrders);
    } catch (Exception $e) {
      ResponseUtil::fail();
    }
  }

  /**
   * 支付页面
   */
  public function pay($listNo)
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();
    $grouponOrderList = new GrouponOrderListModel();

    $order = $grouponOrderList->getOrder($openId, $listNo);
    if (!$order)
      show_error('订单不存在');

    if ($order['order_status'] == 20)
      show_error('订单已支付');

    //
    $grouponProjectCode = $order['groupon_project_code'];
    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);
    if (!$grouponProject)
      show_error('拼团项目不存在');

    $sharePage = $this->load->view('frontend/groupon/sharePage', array('grouponProject' => $grouponProject), true);

    // 获取拼团项目信息
    $this->load->view('frontend/groupon/pay',
      array('listNo' => $listNo, 'grouponProject' => $grouponProject, 'sharePage' => $sharePage));
  }

  /**
   * 支付成功跳转页面
   */
  public function success($listNo)
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();
    $grouponOrderList = new GrouponOrderListModel();

    $order = $grouponOrderList->getOrder($openId, $listNo);
    if (!$order)
      show_error('订单不存在');

    if ($order['order_status'] != 20)
      show_error('订单未支付成功');

    //
    $grouponProjectCode = $order['groupon_project_code'];
    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);
    if (!$grouponProject)
      show_error('拼团项目不存在');

    //$grouponProject['project_cover'] = UploadUtil::buildUploadDocPath($grouponProject['project_cover'], '600x600');

    // 渲染分享页面
    $sharePage = $this->load->view('frontend/groupon/sharePage', array('grouponProject' => $grouponProject), true);

    // 获取拼团项目信息
    $this->load->view('frontend/groupon/share',
      array('listNo' => $listNo, 'order' => $order, 'grouponProject' => $grouponProject, 'sharePage' => $sharePage));
  }

  /**
   * 分享参数
   */
  public function shareParams()
  {
    $params = RequestUtil::postParams();
    $currentUrl = urldecode($params['url']);
    $shareJsParams = (new WxShareUtil())->getShareParams($currentUrl);
    ResponseUtil::QuerySuccess($shareJsParams);
  }

  /**
   * 开团
   */
  public function newGrouponOrder($grouponProjectCode)
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    $grouponOrder = new GrouponOrderModel();
    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);

    // 判断是否有未支付的，在有效期内的订单
    $findOrder = $grouponOrder->getFirstOrderList($grouponProjectCode, $openId);
    if ($findOrder && $findOrder['order_status'] != 20)
      ResponseUtil::failure($findOrder['groupon_order_list_no'], -10);

    if ($findOrder && $findOrder['order_status'] == 20)
      ResponseUtil::failure('您已经参加了此团');
    // 是否还有未拼团完成的

    // 是否参加了别人开的团
    $existsJoin = (new GrouponOrderListModel())->getOne($openId, $findOrder['groupon_order_id']);
    if ($existsJoin)
      ResponseUtil::failure('您已经参加了此团');

    // 判断
    HelperUtil::verifyGrouponProject($grouponProject);
    if ($grouponProject['groupon_count'] === $grouponProject['created'])
      ResponseUtil::failure('团长总数已达到最大');


    $params = RequestUtil::postParams();

    $data['groupon_project_code'] = $grouponProjectCode;
    $data['open_id'] = $openId;
    $data['union_id'] = $unionId;
    $data['groupon_order_code'] = md5(DateUtil::now() . $openId);

    $this->db->trans_start();
    try {
      // 开团
      $grouponOrderId = (new CurdUtil($grouponOrder))->create($data);
      if (!$grouponOrderId)
        throw new Exception('');

      // 创建支付订单
      $grouponOrderListNo = StringUtil::generateGrouponOrderNo();
      unset($data['groupon_project_code'], $data['groupon_order_code']);
      $data['groupon_order_list_no'] = $grouponOrderListNo;
      $data['groupon_order_id'] = $grouponOrderId;
      $data['total_fee'] = $grouponProject['groupon_price'];
      $data['in_counts'] = $grouponProject['in_counts'];
      $data['project_id'] = $grouponProject['project_id'];
      $data['ggroupon_project_code'] = $grouponProject['groupon_project_code'];
      $data['is_first'] = 1;

      $grouponOrderList = new GrouponOrderListModel();
      if (!(new CurdUtil($grouponOrderList))->create($data))
        throw new Exception('');

      $this->db->trans_complete();
      $status = $this->db->trans_status();
      if ($status === FALSE) {
        throw new Exception('');
      } else {
        $this->db->trans_commit();
        ResponseUtil::QuerySuccess([$grouponOrderListNo]);
      }
    } catch (Exception $ex) {
      $this->db->trans_rollback();
      ResponseUtil::failure('开团失败，请重试!');
    }

  }

  /**
   * 参团
   * 判断是否已经拼团
   * 是否已经预约过项目
   */
  public function join($grouponOrderCode)
  {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    // 判断是否已经预约过了
    $orders = (new OrderModel())->getOrders($openId, $unionId, OrderModel::ORDER_COMPLETE);
    if (count($orders) > 0)
      ResponseUtil::failure('“参团”仅限新顾客 <br>老顾客请选择“我要开团”');

    $grouponOrder = new GrouponOrderModel();
    $findGroupOrder = $grouponOrder->getOne($grouponOrderCode);
    if (!$findGroupOrder)
      ResponseUtil::failure('此团不存在');

    $grouponProjectCode = $findGroupOrder['groupon_project_code'];

    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);

    // 判断
    HelperUtil::verifyGrouponProject($grouponProject);

    // 判断是否已经参加了团
    $existsJoin = (new GrouponOrderListModel())->getOne($openId, $findGroupOrder['groupon_order_id']);
    if ($existsJoin) {
      if ($existsJoin['order_status'] == 10)
        ResponseUtil::failure($existsJoin['groupon_order_list_no'], -10);
      else
        ResponseUtil::failure('您已经参加了此团');
    }

    // 判断团长是否支付了此订单
    $findOrder = $grouponOrder->getFirstOrderList($grouponProjectCode, '', $grouponOrderCode);
    if (!$findOrder || ($findOrder && $findOrder['order_status'] != 20))
      ResponseUtil::failure('此团不能参加');

    // 判断人数是否满了
    $ingGrouponOrder = (new GrouponOrderModel())->getIngOrderByGrouponProjectCode($grouponProjectCode, $grouponOrderCode);
    if (count($ingGrouponOrder) === 0)
      ResponseUtil::failure('参团失败，此团关闭或者人数已满');

    $this->db->trans_start();
    try {
      $grouponOrderListNo = StringUtil::generateGrouponOrderNo();

      $data['open_id'] = $openId;
      $data['union_id'] = $unionId;
      $data['groupon_order_list_no'] = $grouponOrderListNo;
      $data['groupon_order_id'] = $findGroupOrder['groupon_order_id'];
      $data['total_fee'] = $grouponProject['groupon_price'];
      $data['in_counts'] = $grouponProject['in_counts'];
      $data['project_id'] = $grouponProject['project_id'];
      $data['ggroupon_project_code'] = $grouponProject['groupon_project_code'];
      $data['is_first'] = 0;

      $grouponOrderList = new GrouponOrderListModel();
      if (!(new CurdUtil($grouponOrderList))->create($data))
        throw new Exception('');

      $this->db->trans_complete();
      $status = $this->db->trans_status();
      if ($status === FALSE) {
        throw new Exception('');
      } else {
        $this->db->trans_commit();
        ResponseUtil::QuerySuccess([$grouponOrderListNo]);
      }
    } catch (Exception $ex) {
      $this->db->trans_rollback();
      ResponseUtil::failure('参团失败，请重试!');
    }
  }

  /**
   * 支付
   */
  public function payParams($listNo)
  {
    // 是否授权
    $wechatUtil = new WechatUtil();
    $openId = $wechatUtil->getOpenId();
    $unionId = $wechatUtil->getUnionId();

    $params = RequestUtil::postParams();
    $phoneNumber = $params['phone'];

    $findOrder = (new GrouponOrderListModel())->getOrder($openId, $listNo);
    if (!$findOrder)
      ResponseUtil::failure('拼团不存在');

    $grouponProjectCode = $findOrder['ggroupon_project_code'];
    $projectId = $findOrder['project_id'];
    if (!(new GrouponOrderListModel())->phoneNumberIsNotJoin($grouponProjectCode, $projectId, $phoneNumber))
      ResponseUtil::failure('此手机号已经参与了此团');

    if ($findOrder['order_status'] != 10)
      ResponseUtil::failure('此订单不可操作');


    if (!$phoneNumber)
      ResponseUtil::failure('手机号不能为空');

    $code = $params['smsCode'];
    if (!$code)
      ResponseUtil::failure('验证码不能为空');


    // // 验证验证码
    $smsModel = new SmsCodeModel();
    $sms = $smsModel->getOne($phoneNumber, SmsCodeModel::GROUPON_ORDER);
    if (!$sms || $sms['code'] !== $params['smsCode'])
      ResponseUtil::failure('验证码错误');


    // 修改订单信息
    (new CurdUtil(new GrouponOrderListModel()))
      ->update(array('open_id' => $openId, 'groupon_order_list_no' => $listNo), array('phone_number' => $phoneNumber));

    // 获得预付款ID
    $config = 'groupon_weixin';
    $weixinPay = new WeixinPayUtil($config);
    $prePayId = $weixinPay->fetchPrepayId($openId, '盲人荟按摩拼团', $listNo, $findOrder['total_fee']);
    LogUtil::weixinLog('拼团预付款ID：', $prePayId);
    if (!$prePayId)
      ResponseUtil::failure('获得微信预付款ID失败，请重试！');

    //生成支付参数
    $payParams = $weixinPay->getParameters($prePayId);
    LogUtil::weixinLog('支付参数：', $payParams);

    try {
      $payParams = json_decode($payParams);
      ResponseUtil::QuerySuccess($payParams);
    } catch (Exception $exception) {
      ResponseUtil::failure('获得支付参数失败');
    }
  }
}