<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 2015/12/28
 * Time: 22:06
 */
class WeixinUtil
{
  private $appId;
  private $appSecret;
  private $accessTokenSign = '_weixin_accesstoken';
  private $mchId;
  private $apiKey;
  private $noticeUrl;

  public function __construct($config = 'weixin')
  {
    $weixinConfig = ConfigUtil::loadConfig($config);
    $this->appId = $weixinConfig['appId'];
    $this->appSecret = $weixinConfig['appSecret'];
    $this->mchId = $weixinConfig['mchId'];
    $this->apiKey = $weixinConfig['apiKey'];
    $this->noticeUrl = $weixinConfig['noticeUrl'];
  }

  public function saveAuthorize($accessToken)
  {
    // 保存授权信息的时间
    $accessToken['saveTime'] = time();
    $_SESSION[$this->accessTokenSign] = $accessToken;
  }

  public function getAuthorize()
  {
    if (isset($_SESSION[$this->accessTokenSign]))
      return $_SESSION[$this->accessTokenSign];
    else
      return [];
  }

  public function getOpenId()
  {
    $accessToken = $this->getAuthorize();
    if (isset($accessToken['openid']))
      return $accessToken['openid'];
    else
      return '';
  }

  /**
   * @return mixed|string
   */
  public function getUnionId()
  {
    $accessToken = $this->getAuthorize();
    if (isset($accessToken['unionid']))
      return $accessToken['unionid'];
    else
      return '';
  }

  public function getRefreshToken()
  {
    $accessToken = $this->getAuthorize();
    return $accessToken['refresh_token'];
  }

  public function getAccessToken()
  {
    $accessToken = $this->getAuthorize();
    return $accessToken['access_token'];
  }

  public function getSaveTime()
  {
    $accessToken = $this->getAuthorize();
    return $accessToken['saveTime'];
  }

  /**
   * 是否需要刷新fresh 5400秒之内不用刷新。
   */
  public function isNeedRefreshAccessToken()
  {
    $saveTime = $this->getSaveTime();
    // expires_in为7200
    return time() > $saveTime + 5400;
  }

  /**
   * 刷新accessToken
   */
  public function refreshAccessToken()
  {
    $refreshToken = $this->getRefreshToken();
    $refreshUrl = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$this->appId}&grant_type=refresh_token&refresh_token={$refreshToken}";
    LogUtil::weixinLog('刷新token链接：', $refreshUrl);
    $newAuthorize = RequestUtil::get($refreshUrl);
    LogUtil::weixinLog('刷新accessToken：', $newAuthorize);

    if (!$newAuthorize || $newAuthorize['errcode'])
      return false;

    $this->saveAuthorize($newAuthorize);
    return true;
  }

  /**
   * 跳转微信授权
   */
  public function toAuthorize($callback)
  {
    $callback = urlencode($callback);
    return "https://open.weixin.qq.com/connect/oauth2/authorize?" .
      "appid={$this->appId}&redirect_uri={$callback}&response_type=code&" .
      "scope=snsapi_userinfo&state=STATE#wechat_redirect";
  }

  /**
   * 检测微信登录
   */
  public function loginCallback($code)
  {
    if (!$code)
      return false;

    $accessTokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?" .
      "appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";

    $accessToken = RequestUtil::get($accessTokenUrl);
    LogUtil::weixinLog('授权登录：', $accessToken);
    if (!$accessToken || $accessToken['error'])
      return false;

    $this->saveAuthorize($accessToken);

    // 判断是否已经获取了微信用户信息
    $customerModel = new CustomerModel();
    $customer = $customerModel->readOneByUnionId($this->getUnionId(), CustomerModel::IS_CUSTOMER);

    // 没有保存客户信息
    if (!$customer) {
      $userInfo = $this->getWeixinUserInfo($this->getToken(), $this->getOpenId());
      if ($userInfo) {
        $openIdCustomer = $customerModel->readOne($this->getOpenId(), CustomerModel::IS_CUSTOMER);
        if (!$openIdCustomer)
          $customerModel->insert($this->getUnionId(), $this->getOpenId(), 0, $userInfo['nickname'],
            $userInfo['headimgurl'], $userInfo['city'], $userInfo['province'], $userInfo['sex']);
        else
          $customerModel->update($this->getOpenId(), $userInfo['nickname'],
            $userInfo['headimgurl'], $userInfo['city'], $userInfo['province'], $userInfo['sex'], $this->getUnionId());
      }
    } else if (!$customer['nick_name'] || !$customer['avatar']) {
      $userInfo = $this->getWeixinUserInfo($this->getToken(), $this->getOpenId());
      $customerModel->update($this->getOpenId(), $userInfo['nickname'],
        $userInfo['headimgurl'], $userInfo['city'], $userInfo['province'], $userInfo['sex'], $this->getUnionId());
    } else {
    }

    return true;
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
    LogUtil::weixinLog('获取普通access token：', $token);

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
    $templateUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;

    LogUtil::weixinLog('模版数据', ['message' => $message, 'accesstoken' => $accessToken]);
    $response = RequestUtil::post($templateUrl, $message);
    LogUtil::weixinLog('发送模版消息：', $response);

    return $response;
  }

  /**
   * 发送预约成功信息
   * @param $orderNo
   * @param $appointmentDay
   * @param $shop
   * @param $beautician
   * @param $projectName
   * @param $openId
   * @param $projectName
   * @param $accessToken
   * @param $credits
   * @return mixed
   */
  public function sendOrderMessage($orderNo, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken, $credits)
  {
    $message = array(
      "touser" => $openId,
      "template_id" => "l62F-ewHevL8esn_9jRsJsrDLwAGly32Y-8w5DkFHJM",
      "url" => UrlUtil::createUrl('userCenter/order'),
      "topcolor" => "#FF0000",
      "data" => array(
        "first" => array( //描述
          "value" => "您好，谢谢购买不期而遇美容商品，您的订单号为：{$orderNo}",
          "color" => "#FF8CB3"
        ),

        "keyword1" => array(
          "value" => "尊敬的顾客",
          "color" => "#173177"
        ),

        "keyword2" => array(
          "value" => $appointmentDay,
          'color' => "#173177"
        ),

        "keyword3" => array(
          "value" => $shop,
          'color' => "#173177"
        ),

        "keyword4" => array(
          "value" => $beautician,
          'color' => "#173177"
        ),

        "keyword5" => array(
          "value" => $projectName,
          'color' => "#173177"
        ),

        "remark" => array( //备注
          "value" => "谢谢购买，您的当前积分为：{$credits}, 有疑问请联系： 021-50809608",
          "color" => "#c9151b"
        )
      )
    );

    return $this->templateMessage($message, $accessToken);
  }


  /**
   * @param int $type
   * @param $orderNo
   * @param $appointmentDay
   * @param $shop
   * @param $beautician
   * @param $projectName
   * @param $openId
   * @param $accessToken
   * @return mixed|string
   */
  public function order($type = 1, $nickName, $phone, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken)
  {
    $first = $type === 1 ? '您好，您已成功预约' : '有新的预约';

    $message = array(
      "touser" => $openId,
      "template_id" => "pXuq842AmgF2-qVTmCBT4b8xKzmKoR5ws7L3pArxSZM",
      "url" => UrlUtil::createUrl('center/index'),
      "topcolor" => "#FF0000",
      "data" => array(
        "first" => array( //描述
          "value" => $first,
          "color" => "#FF8CB3"
        ),

        "examuser" => array(
          "value" => $nickName ? $nickName : $phone,
          "color" => "#173177"
        ),

        "regdate" => array(
          "value" => $appointmentDay,
          'color' => "#173177"
        ),

        "address" => array(
          "value" => $shop,
          'color' => "#173177"
        ),

        "hosptel" => array(
          "value" => $phone,
          'color' => "#173177"
        ),

        "remark" => array( //备注
          "value" => "预约项目为：{$projectName}, 预约技师为:{ $beautician }",
          "color" => "#c9151b"
        )
      )
    );

    return $this->templateMessage($message, $accessToken);
  }

  /**
   * 取消订单
   * @param $cancelOrderTime
   * @param $appointmentDay
   * @param $shop
   * @param $beautician
   * @param $projectName
   * @param $openId
   * @param $accessToken
   * @return mixed|string
   */
  public function cancelOrder($to, $cancelOrderTime, $appointmentDay, $shop, $beautician, $projectName, $openId, $accessToken, $fromId = '')
  {
    $message = array(
      "touser" => $openId,
      "template_id" => "LRnmSBh-MU2YGwBVQtP1ce2-nIsIdIBSaEDw4Xtg4Gc",
      "url" => UrlUtil::createUrl('center/index'),
      "topcolor" => "#FF0000",
      "data" => array(
        "first" => array( //描述
          "value" => $to . '预约的项目【' . $projectName . '】已取消',
          "color" => "#FF8CB3"
        ),

        "keyword1" => array(
          "value" => $shop,
          'color' => "#173177"
        ),

        "keyword2" => array(
          "value" => $beautician,
          'color' => "#173177"
        ),

        "keyword3" => array(
          "value" => $appointmentDay,
          'color' => "#173177"
        ),

        "keyword4" => array(
          "value" => $cancelOrderTime,
          'color' => "#173177"
        ),

        "remark" => array( //备注
          "value" => "欢迎下次光临",
          "color" => "#c9151b"
        )
      )
    );

    return $this->templateMessage($message, $accessToken);
  }


  /**
   * 授权
   * @param $returnUrl
   * @return bool
   */
  public function authorize($returnUrl)
  {
    if (!$returnUrl)
      get_instance()->message('授权回调地址为空！');

    // 如果是微信授权后返回
    if (isset($_GET['code'])) {
      // 获得accessToken
      $callback = $this->loginCallback($_GET['code']);
      if (!$callback)
        get_instance()->message('获得微信授权失败，请重试！');
    }

    // 检测是否已经授权
    $openId = $this->getOpenId();
    if ($openId) {
      // 刷新token过期
      if ($this->isNeedRefreshAccessToken())
        if (!$this->refreshAccessToken()) {
          ResponseUtil::redirect($this->toAuthorize($returnUrl));
        }
    } else {
      // 去微信授权
      $url = $this->toAuthorize($returnUrl);
      ResponseUtil::redirect($url);
    }

    return true;
  }

  /**
   * 获得微信用户信息‚
   * @param $accessToken
   * @param $openId
   */
  public function getWeixinUserInfo($accessToken, $openId)
  {
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openId}&lang=zh_CN";

    $userInfo = RequestUtil::get($url);
    LogUtil::weixinLog('获得用户信息：', $url);
    if ($userInfo['errcode']) {
      LogUtil::weixinLog('获取用户信息失败：', $userInfo['errcode'] . '--' . $userInfo['errmsg']);
      return null;
    } else {
      LogUtil::weixinLog('获取用户信息成功', $userInfo);
      return $userInfo;
    }
  }
}