<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2018/4/12
 * Time: 22:20
 */

class WechatUtil
{
  private $wechat;
  public function __construct()
  {
    if (RequestUtil::isXcx())
      $this->wechat = (new WxXcxUtil());
    else
      $this->wechat = (new WeixinUtil());
  }

  public function getOpenId() {
    return $this->wechat->getOpenId();
  }

  public function getUnionId() {
    return $this->wechat->getUnionId();
  }

  public function getToken() {
    return $this->wechat->getToken();
  }

  public function order($type, $nickName, $phone, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken) {
    return $this->wechat->order($type, $nickName, $phone, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken);
  }

  public function cancelOrder($to, $cancelOrderTime, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken) {
    return $this->wechat->cancelOrder($to, $cancelOrderTime, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken);
  }

  public function authorize($url) {
    return $this->wechat->authorize($url);
  }
}