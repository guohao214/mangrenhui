<?php

class Bind extends FrontendController
{
  /**
   * 绑定手机号
   */
  public function index()
  {
    $this->view('bind/index');
  }


  /**
   * 客户绑定
   */
  public function bindMe()
  {
    $openId = (new WeixinUtil())->getOpenId();
    $params = RequestUtil::getParams();
    $code = $params['code'];
    $phone = $params['phone'];
    if (!$code || !$phone)
      ResponseUtil::failure();

    $customerModel = new CustomerModel();
    // 是否已绑定
    $customer = $customerModel->readOne($openId, CustomerModel::IS_CUSTOMER);
    if ($customer['phone'])
      ResponseUtil::failure('此手机号已绑定');

    // 查询code
    $sms = (new SmsCodeModel())->getOne($phone, SmsCodeModel::BIND_WECHAT_TYPE);
    if ($code !== $sms['code'])
      ResponseUtil::failure('手机验证码错误');

    // 绑定
    $status = (new CurdUtil($customerModel))->update(array('open_id' => $openId),
      array('phone' => $phone, 'type' => CustomerModel::IS_CUSTOMER));

    //echo $this->db->last_query();
    $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();

  }

  /**
   * 技师绑定， 前台绑定
   */
  public function other()
  {
    if (RequestUtil::isPost() && RequestUtil::isAjax()) {
      $openId = (new WeixinUtil())->getOpenId();

      $params = RequestUtil::postParams();
      $type = $params['type'] + 0;
      $phone = $params['phone'];
      $beauticianId = 0;

      if (!$type || !$phone)
        ResponseUtil::failure();

      $customerModel = new CustomerModel();
      // 是否已绑定
      $customer = $customerModel->readOne($openId, $type);
      if ($customer['phone'])
        ResponseUtil::failure('此手机号已绑定');

      // 如果是技师， 则查询手机号， 然后与open_id 绑定
      if ($type == CustomerModel::IS_BEAUTICIAN) {
          $beautician = (new BeauticianModel())->readByPhone($phone);
          if (!$beautician)
            ResponseUtil::failure('绑定的技师不存在');

          $beauticianId = $beautician['beautician_id'];
      }

      $status = (new CurdUtil(new CustomerModel()))->create(array('open_id' => $openId,'credits' => 0,
        'phone' => $phone, 'type' => $type, 'beautician_id' => $beauticianId));
      $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();

    }

    $this->view('bind/other');
  }
}