<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/29
 * Time: 13:22
 */
class LogUtil
{
  public static $weinxi = 'wechat_';

  public static function log($mainTitle, $message, $file = 'log')
  {
    $saveFile = APPPATH . DIRECTORY_SEPARATOR . 'logs';
    $saveFile = $saveFile . DIRECTORY_SEPARATOR . $file . date('Y-m-d');

    if (!is_string($message))
      $message = json_encode($message);
    $mainTitle = DateUtil::now() . ' ' . $mainTitle;
    file_put_contents($saveFile, $mainTitle . $message . "\n\n" . str_repeat('-', 80) . "\n", FILE_APPEND);
  }

  public static function weixinLog($mainTitle, $message)
  {
    self::log($mainTitle, $message, self::$weinxi);
  }


  public static function xcx($mainTitle, $message)
  {
    self::log('小程序：' . $mainTitle, $message, self::$weinxi);
  }

}