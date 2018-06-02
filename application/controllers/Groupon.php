<?php

/**
 * 拼团
 */
class Groupon extends FrontendController
{

  public function grouponIndex($grouponProjectCode, $grouponOrderCode = '') 
  {
    $this->load->view('frontend/groupon/index', array('groupon_project_code' => $grouponProjectCode, 'groupon_order_code' => $grouponOrderCode));
  }

  /**
   * 发送验证码
   */
  public function sendSmsCode($phone) {
    if (!$phone)
    ResponseUtil::failure();

    try {
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
      ResponseUtil::failureure('拼团项目不存在');
    else {
      $grouponProject['project_cover'] = UploadUtil::buildUploadDocPath($grouponProject['project_cover'], '600x600');
      

      // 是否已过期
      // 未开始
      $now  = DateUtil::now();
      if ($now < $grouponProject['start_time'])
        $grouponProject['__type'] = 'wait';
      else if ($now > $grouponProject['start_time'] && $now < $grouponProject['end_time'])
        $grouponProject['__type'] = 'ing';
      else
        $grouponProject['__type'] = 'end';

      ResponseUtil::QuerySuccess($grouponProject);
    }
  }

  /**
   * 进行中的团
   * $grouponOrderCode 筛选的拼团订单
   */
  public function getGrouponIngOrders($grouponProjectCode, $grouponOrderCode = '') {
    try  {
      $wechat = new WechatUtil();
      $openId = $wechat->getOpenId();
      $unionId = $wechat->getUnionId();

      $ingOrders = (new GrouponOrderModel())->getIngOrderByGrouponProjectCode($grouponProjectCode, $grouponOrderCode, $openId);
      ResponseUtil::QuerySuccess($ingOrders);
    } catch( Exception $e) {
      ResponseUtil::fail();
    }

  }

  /**
   * 支付页面
   */
  public function pay($listNo) {
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

    $grouponProject['project_cover'] = UploadUtil::buildUploadDocPath($grouponProject['project_cover'], '600x600');
    // 获取拼团项目信息
    $this->load->view('frontend/groupon/pay', array('listNo' => $listNo, 'grouponProject' => $grouponProject));
  }

  /**
   * 支付成功跳转页面
   */
  public function success($listNo) {
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

    $grouponProject['project_cover'] = UploadUtil::buildUploadDocPath($grouponProject['project_cover'], '600x600');
    // 获取拼团项目信息
    $this->load->view('frontend/groupon/share', array('listNo' => $listNo, 'order' => $order, 'grouponProject' => $grouponProject));
  }

  /**
   * 分享参数
   */
  public function shareParams() {
    $params = RequestUtil::postParams();
    $currentUrl = urldecode($params['url']);
    $shareJsParams = (new WxShareUtil())->getShareParams($currentUrl);
    ResponseUtil::QuerySuccess($shareJsParams);
  }

  /**
   * 开团
   */
  public function newGrouponOrder($grouponProjectCode) {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

    $grouponProjetModel = new GrouponProjectModel();
    $grouponProject = $grouponProjetModel->readOne($grouponProjectCode);

    // 判断
    HelperUtil::verifyGrouponProject($grouponProject);
    if ($grouponProject['groupon_count'] === $grouponProject['created'])
      ResponseUtil::failure('团长总数已达到最大');


    $grouponOrder = new GrouponOrderModel();

    // 判断是否有未支付的，在有效期内的订单
    $findOrder = $grouponOrder->getFirstOrderList($grouponProjectCode, $openId);
    if ($findOrder && $findOrder['order_status'] != 20)
      ResponseUtil::failure('您还有未支付的拼团订单');

    if ($findOrder && $findOrder['order_status'] == 20)
      ResponseUtil::failure('您已经参加了此团');
    // 是否还有未拼团完成的

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
      $grouponOrderListNo = StringUtil::generateOrderNo();
      unset($data['groupon_project_code'], $data['groupon_order_code']);
      $data['groupon_order_list_no'] = $grouponOrderListNo;
      $data['groupon_order_id'] = $grouponOrderId;
      $data['total_fee'] = $grouponProject['groupon_price'];
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
   */
  public function join($grouponOrderCode) {
    $wechat = new WechatUtil();
    $openId = $wechat->getOpenId();
    $unionId = $wechat->getUnionId();

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
    if ($existsJoin)
      ResponseUtil::failure('您已经参加了此团');

    // 判断团长是否支付了此订单
    $findOrder = $grouponOrder->getFirstOrderList($grouponProjectCode, '', $grouponOrderCode);
    if (!$findOrder || ($findOrder && $findOrder['order_status'] != 20))
      ResponseUtil::failure('此团不能参加');

    // 判断人数是否满了

    $this->db->trans_start();
    try {
      $grouponOrderListNo = StringUtil::generateOrderNo();

      $data['open_id'] = $openId;
      $data['union_id'] = $unionId;
      $data['groupon_order_list_no'] = $grouponOrderListNo;
      $data['groupon_order_id'] = $findGroupOrder['groupon_order_id'];
      $data['total_fee'] = $grouponProject['groupon_price'];
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
        ResponseUtil::executeSuccess($grouponOrderListNo);
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

    $findOrder = (new GrouponOrderListModel())->getOrder($openId, $listNo);
    if (!$findOrder)
      ResponseUtil::failure('拼团不存在');

    if ($findOrder['order_status'] != 10)
      ResponseUtil::failure('此订单不可操作');

      $params = RequestUtil::postParams();
      $phoneNumber = $params['phone'];
      if (!$phoneNumber)
        ResponseUtil::failure('手机号不能为空');
  
      $code = $params['smsCode'];
      if (!$code)
        ResponseUtil::failure('验证码不能为空');

  
      // // 验证验证码
      $smsModel = new SmsCodeModel();
      $sms = $smsModel->getOne($phoneNumber, SmsCodeModel::GROUPON_ORDER);
      if (!$sms)
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

  /**
   * 异步通知
   */
  public function callMe() {
    $weixin = new WeixinPayUtil();

    //通知微信
    $notice = $weixin->notifyData();
    LogUtil::weixinLog('拼团微信支付后的通知参数', $notice);
    // 签名成功， 返回数组， 否则返回xml数据
    if (!is_array($notice) || !isset($notice['transaction_id']))
      exit($notice);

    //签名成功，处理数据

    /**
     * 返回的数据
     * 'appid' => string 'wxf5b5e87a6a0fde94' (length=18)
     * 'bank_type' => string 'CFT' (length=3)
     * 'fee_type' => string 'CNY' (length=3)
     * 'is_subscribe' => string 'N' (length=1)
     * 'mch_id' => string '10000097' (length=8)
     * 'nonce_str' => string 'dz8nirk7gmxhhxn38zgib28yx14ul2gf' (length=32)
     * 'openid' => string 'ozoKAt-MmA74zs7MBafCix6Dg8o0' (length=28)
     * 'out_trade_no' => string 'wxf5b5e87a6a0fde941409708791' (length=28)
     * 'result_code' => string 'SUCCESS' (length=7)
     * 'return_code' => string 'SUCCESS' (length=7)
     * 'sign' => string 'EDACA525F6C675337B2DAC25B7145028' (length=32)
     * 'sub_mch_id' => string '10000097' (length=8)
     * 'time_end' => string '20140903094659' (length=14)
     * 'total_fee' => string '1' (length=1)
     * 'trade_type' => string 'NATIVE' (length=6)
     * 'transaction_id' => string '1004400737201409030005091526' (length=28)  //微信支付单号
     */

//        $notice  = array(
//            'out_trade_no' => '201512271710391206225994',
//            'transaction_id' => '1004400737201409030005091526'
//        );

    $listNo = $notice['out_trade_no'];
    $wxOrderNo = $notice['transaction_id'];
    $openId = $notice['openid'];

    $grouponOrderList = new GrouponOrderListModel();
    // 获得订单
    $order = $grouponOrderList->getOrder($openId, $listNo);
    if (!$order)
      exit($weixin->notifyFailure());

    // 判断是否已经支付
    if ($order['order_status'] == 20)
      exit($weixin->notifyPayed());

    // 更新订单信息
    $this->db->trans_start();

    (new CurdUtil($grouponOrderList))->update(array('open_id' => $openId, 'groupon_order_list_no' => $listNo), 
      array('order_status' => 20, 'transaction_id' => $wxOrderNo, 'payment_time' => DateUtil::now()));

    // 事务完成
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      exit($weixin->notifyFailure());
    } else {
      $this->db->trans_commit();
      exit($weixin->notifySuccess());
    }
  }
}