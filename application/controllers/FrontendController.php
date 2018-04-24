<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 15-12-13
 * Time: 下午1:01
 */
class FrontendController extends BaseController
{
  public $pageTitle = '盲人荟按摩';
  public $cacheTime = 600;

  public function __construct()
  {
    parent::__construct();

    $weChat = new WechatUtil();
    $unionId = $weChat->getUnionId();

    if (!$unionId) {
      if (RequestUtil::isAjax())
        ResponseUtil::failure('未微信授权');
      else
        $weChat->authorize(RequestUtil::currentUrl());
    }
  }

  /**
   * 获得分享js
   * @param $currentUrl
   */
  public function getJsTicket()
  {
//    $scheme = $_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http';
//    $scheme .= '://';
//
//    $httpHost = $_SERVER['HTTP_HOST']
//      ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']
//        ? $_SERVER['SERVER_NAME'] : 'www.xinyameirong.com';
//
//    $currentUrl = $scheme . $httpHost . $_SERVER['REQUEST_URI'];

    $params = RequestUtil::getParams();
    $currentUrl =urldecode($params['url']);

    $shareJsParams = (new WxShareUtil())->getShareParams($currentUrl);
    ResponseUtil::QuerySuccess($shareJsParams);
  }

  public function noContent($message)
  {
    $this->load->view('frontend/noContent', array('message' => $message));
  }

  /**
   * 用于前台 layout布局的view操作
   * @param $view
   * @param array $vars
   */
  public function view($view, $vars = array())
  {
    parent::see('frontend', $view, $vars);
  }

  /**
   * 页面缓存
   * @param string $cacheTime
   * @return bool
   */
  public function outputCache($cacheTime = '')
  {
    return true;

    if (!$cacheTime)
      $cacheTime = $this->cacheTime;

    $this->output->cache($cacheTime);
  }

}