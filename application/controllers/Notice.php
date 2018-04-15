<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2018/4/15
 * Time: 13:38
 */

class Notice extends BaseController
{
  /**
   * 微信支付后的异步回调
   */
  public function callme()
  {
    $weixin = new WeixinPayUtil();

    //通知微信
    $notice = $weixin->notifyData();
    LogUtil::xcx('微信支付后的通知参数', $notice);
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

    $orderNo = $notice['out_trade_no'];
    $wxOrderNo = $notice['transaction_id'];
    $openId = $notice['openid'];

    $orderModel = new OrderModel();
    // 获得订单
    $orders = $orderModel->orders(array('order_no' => $orderNo));
    if (!$orders || !$orders[0])
      exit($weixin->notifyFailure());

    // 判断是否已经支付
    $order = $orders[0];
    if ($order['order_sign'] == OrderModel::ORDER_COMPLETE)
      exit($weixin->notifyPayed());

    // 更新订单信息
    $this->db->trans_start();

    $orderModel->payed($orderNo, $wxOrderNo);

    // 事物完成
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