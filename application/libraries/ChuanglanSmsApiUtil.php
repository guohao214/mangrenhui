<?php

class ChuanglanSmsApiUtil
{

  //创蓝发送短信接口URL, 如无必要，该参数可不用修改
  const API_SEND_URL = 'http://222.73.117.158/msg/HttpBatchSendSM';

  //创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
  const API_BALANCE_QUERY_URL = 'http://222.73.117.158/msg/QueryBalance';

  const API_ACCOUNT = 'meilixw';//创蓝账号 替换成你自己的账号

  const API_PASSWORD = 'Admin888';//创蓝密码 替换成你自己的密码

  /**
   * 发送短信
   *
   * @param string $mobile 手机号码
   * @param string $msg 短信内容
   * @param string $needstatus 是否需要状态报告
   */
  public static function sendSMS($mobile, $msg, $needstatus = 'false')
  {

    //创蓝接口参数
    $postArr = array(
      'account' => self::API_ACCOUNT,
      'pswd' => self::API_PASSWORD,
      'msg' => $msg,
      'mobile' => $mobile,
      'needstatus' => $needstatus
    );

    $result = self::curlPost(self::API_SEND_URL, $postArr);
    return $result;
  }

  /**
   * 查询额度
   *
   *  查询地址
   */
  public static function queryBalance()
  {

    //查询参数
    $postArr = array(
      'account' => self::API_ACCOUNT,
      'pswd' => self::API_PASSWORD,
    );
    $result = self::curlPost(self::API_BALANCE_QUERY_URL, $postArr);
    return $result;
  }

  /**
   * 处理返回值
   *
   */
  public static function execResult($result)
  {
    $result = preg_split("/[,\r\n]/", $result);
    return $result;
  }

  /**
   * 通过CURL发送HTTP请求
   * @param string $url //请求URL
   * @param array $postFields //请求参数
   * @return mixed
   */
  public static function curlPost($url, $postFields)
  {
    $postFields = http_build_query($postFields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
