<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 2015/12/24
 * Time: 23:29
 */
class StringUtil
{

  public static function substr($string, $length = 10)
  {
    $string = strip_tags($string);
    $suffix = '';
    if (mb_strlen($string) > $length)
      $suffix = '...';

    return mb_substr($string, 0, $length) . $suffix;
  }

  /**
   * 生成订单号
   * @return string
   */
  public static function generateOrderNo()
  {
    return date('YmdHismw') . mt_rand(10000, 10000000);
  }

  /**
   * @param int $length
   * @return bool|string
   */
  public static function generateCode($length = 6)
  {
    $string = date('YmdHisw') . mt_rand(10000, 10000000);
    $string = str_shuffle($string);

    return substr($string, 0, $length);

  }
}