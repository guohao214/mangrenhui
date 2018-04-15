<?php

class SessionUtil
{

  const XCX = 'xcx_';
  public static function setOpenId($openId)
  {
    $_SESSION[self::XCX . 'open_id'] = $openId;
  }

  public static function getOpenId()
  {
    return $_SESSION[self::XCX . 'open_id'];
  }

  public static function setUnionId($unionId)
  {
    $_SESSION[self::XCX . 'union_id'] = $unionId;
  }

  public static function getUnionId()
  {
    return $_SESSION[self::XCX . 'union_id'];
  }

  public static function setSessionKey($sessionKey)
  {
    $_SESSION[self::XCX . 'session_key'] = $sessionKey;
  }

  public static function getSessionKey()
  {
    return $_SESSION[self::XCX . 'session_key'];
  }
}