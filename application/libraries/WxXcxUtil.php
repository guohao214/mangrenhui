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

  /**
   * 获取token信息， 不同于登录验证返回的token
   * @return mixed
   */
  public function getToken()
  {
    $tokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid="
      . $this->appId . "&secret=" . $this->appSecret;

    $token = RequestUtil::get($tokenUrl);
    LogUtil::xcx('获取普通access token：', $token);

    $access_token = '';
    if (isset($token['access_token']))
      $access_token = $token['access_token'];

    return $access_token;
  }

  /**
   * 发送模板消息
   * @param array $message
   * @param $accessToken
   */
  public function templateMessage(array $message, $accessToken)
  {
    $message = json_encode($message);
    $templateUrl = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $accessToken;

    LogUtil::xcx('模版数据', ['message' =>$message, 'accessToken' => $accessToken]);
    $response = RequestUtil::post($templateUrl, $message);
    LogUtil::xcx('发送模版消息：', $response);

    return $response;
  }

  public function order($type =1, $nickName, $phone, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken, $formId)
  {
    $first = $type === 1 ? '您好，您已成功预约' : '有新的预约';

    $message = array(
      "touser" => $openId,
      "template_id" => "ejkMGLYkX05WQiaEz5sed3HdJsBA5O22H9Ddaie1bNw",
      "page" => "order/index",
      'form_id' => $formId,
      "topcolor" => "#FF0000",
      "data" => array(
        "keyword1" => array( //描述
          "value" => $nickName ? $nickName : $phone,
          "color" => "#FF8CB3"
        ),

        "keyword2" => array(
          "value" => $appointmentDay,
          "color" => "#173177"
        ),

        "keyword3" => array(
          "value" => $shop,
          'color' => "#173177"
        ),

        "keyword4" => array(
          "value" => $projectName,
          'color' => "#173177"
        ),

        "keyword5" => array(
          "value" => "{$first}, 预约技师为: {$beautician}",
          'color' => "#173177"
        ),

        "keyword6" => array(
          "value" => $phone,
          'color' => "#173177"
        ),
      )
    );

    return $this->templateMessage($message, $accessToken);
  }

  public function cancelOrder($to, $cancelOrderTime, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken, $formId = '') {
    $message = array(
      "touser" => $openId,
      "template_id" => "eV7IlG_cRgnslGvag656xDG1qwEa_vkIUq8YpYdqqs4",
      "page" => 'order/index',
      'form_id' => $formId,
      "topcolor" => "#FF0000",
      "data" => array(
        "keyword1" => array(
          "value" => $to,
          'color' => "#173177"
        ),

        "keyword2" => array(
          "value" => $shop,
          'color' => "#173177"
        ),

        "keyword3" => array(
          "value" => $projectName,
          'color' => "#173177"
        ),

        "keyword4" => array(
          "value" => $appointmentDay,
          'color' => "#173177"
        ),

        "keyword5" => array(
          "value" => $cancelOrderTime,
          'color' => "#173177"
        ),

        "keyword6" => array(
          "value" => $beautician,
          'color' => "#173177"
        ),

        "keyword7" => array( //备注
          "value" => "欢迎下次光临",
          "color" => "#c9151b"
        )
      )
    );

    return $this->templateMessage($message, $accessToken);
  }

  public function authorize($url) {

  }
}