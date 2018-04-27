<?php
ini_set('session.name', 'mangrenhui-20180102-session');
ini_set('session.cookie_lifetime', 604800); //7天
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', ini_get('session.cookie_lifetime') - 1440);
date_default_timezone_set('Asia/shanghai');
set_time_limit(60);

if ((isset($_SERVER["HTTP_X_XCX"])
  && strtolower($_SERVER["HTTP_X_XCX"]) === "xcx")) {
  $token = $_SERVER['HTTP_TOKEN'];
  if ($token)
    session_id($token);
}

session_start();

define('DOCUMENT_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', __DIR__);
define('UPLOAD_FOLDER', 'upload');

if (file_exists('../env.php'))
  $env = include_once ('../env.php');
else
  $env = 'production';

$_SERVER['CI_ENV'] = $env;
include '../system/codeigniter.php';
