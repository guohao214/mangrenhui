<?php

class WxXcxUtil
{
  private $appId;
  private $appSecret;
  private $accessTokenSign = '_weixin_accesstoken';
  private $mchId;
  private $apiKey;
  private $noticeUrl;

  public function __construct($config = 'wxxcx')
  {
    $config = ConfigUtil::loadConfig($config);
    $this->appId = $config['appId'];
    $this->appSecret = $config['appSecret'];
    $this->mchId = $config['mchId'];
    $this->apiKey = $config['apiKey'];
    $this->noticeUrl = $config['noticeUrl'];
  }

  /**
   * @param $jsCode
   */
  public function jscode2session($jsCode)
  {
    $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appId}&secret={$this->appSecret}&js_code={$jsCode}&grant_type=authorization_code";
    LogUtil::xcx('换取openid：', $url);
    $data = RequestUtil::get($url);
    LogUtil::xcx('获得openId：', $data);

    if (isset($data['errcode']))
      return false;

    // 验证
    if (isset($data['openid']))
      SessionUtil::setOpenId($data['openid']);

    if (isset($data['session_key']))
      SessionUtil::setSessionKey($data['session_key']);
    if (isset($data['unionid']))
      SessionUtil::setUnionId($data['unionid']);

    return true;
  }

  public function getOpenId() {
    return SessionUtil::getOpenId();
  }

  public function getUnionId() {
    return SessionUtil::getUnionId();
  }

  public function getToken() {
    return '';
  }

  public function order() {

  }

  public function cancelOrder() {

  }

  public function authorize($url) {

  }
}