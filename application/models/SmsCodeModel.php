<?php

class SmsCodeModel extends BaseModel
{
  const BIND_WECHAT_TYPE = 1; //绑定微信
  const GROUPON_ORDER = 2;
  const EXPIRED_TIME = 5 * 60;

  public function setTable()
  {
    $this->table = 'sms_code';
  }

  /**
   * @param $phone
   * @param $type
   * @param $message
   */
  public function sendCode($phone, $type, $message = '您的手机验证码为:')
  {
    $code = StringUtil::generateCode();
    $message .= $code;
    $data = array(
      'phone' => $phone,
      'code' => $code,
      'sms_type' => $type,
      'expired_time' => DateUtil::unix() + self::EXPIRED_TIME,
      'message' => $message,
      'created_time' => DateUtil::now()
    );

    $smsID = (new CurdUtil($this))->create($data);

    if ($smsID)
      // 发送手机验证码
      ChuanglanSmsApiUtil::sendSMS($phone, $message);

    return $smsID;
  }

  /**
   * @param $phone
   * @param $type
   */
  public function getOne($phone, $type)
  {
    $this->db->where('expired_time >=', DateUtil::unix());
    return (new CurdUtil($this))->readOne(array('phone' => $phone, 'sms_type' => $type), 'sms_id desc');
  }
}