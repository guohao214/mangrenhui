<?php

/**
 * 小程序
 */
class XcxLogin extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 小程序授权
   */
  public function authorize()
  {
    $params = RequestUtil::getParams();
    $code = $params['code'];
    if (!$code)
      ResponseUtil::failure('code不存在');

    $wxXcxUtil = new WxXcxUtil();
    $status = $wxXcxUtil->jscode2session($code);
    $unionId = $wxXcxUtil->getUnionId();
    $xcxOpenId = $wxXcxUtil->getOpenId();

    // 用户信息
    $customerModel = new CustomerModel();
    $customer = $customerModel->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
    if (!$customer) {
      $customerModel->insertXcx($unionId, $xcxOpenId);
    } else if (!$customer['xcx_open_id']) {
      $customerModel->updateXcx($unionId, $xcxOpenId);
    } else {}

    $sessionId = session_id();
    return $status ? ResponseUtil::executeSuccess('换取信息成功', $sessionId) : ResponseUtil::failure();
  }
}