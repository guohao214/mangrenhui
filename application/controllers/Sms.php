<?php

class Sms extends FrontendController
{
  public function send($phone)
  {
    if (!$phone)
      ResponseUtil::failure();

    try {
      $status = (new SmsCodeModel())->sendCode($phone, SmsCodeModel::BIND_WECHAT_TYPE);
      $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();
    } catch (Exception $e) {
      ResponseUtil::failure();
    }
  }
}