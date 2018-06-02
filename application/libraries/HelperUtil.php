<?php
class HelperUtil {
  public static function verifyGrouponProject($grouponProject) {
    $now = DateUtil::now();
    if ($now <= $grouponProject['start_time'])
      ResponseUtil::failure('开团时间未开始');

    if ($now >= $grouponProject['end_time'])
      ResponseUtil::failure('开团时间已结束');
  }
}