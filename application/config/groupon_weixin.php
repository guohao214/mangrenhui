<?php
$config = include './weixin.php';
$config['noticeUrl'] = UrlUtil::createUrl('groupon/callMe');

return $config;