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
    $unionId = (new WechatUtil())->getUnionId();
    $params = RequestUtil::getParams();
    $code = $params['code'];
    $phone = $params['phone'];
    if (!$code || !$phone)
      ResponseUtil::failure();

    $customerModel = new CustomerModel();
    // 是否已绑定
    $customer = $customerModel->readOneByUnionId($unionId, CustomerModel::IS_CUSTOMER);
    if ($customer['phone'])
      ResponseUtil::failure('此手机号已绑定');

    // 查询code
    $sms = (new SmsCodeModel())->getOne($phone, SmsCodeModel::BIND_WECHAT_TYPE);
    if ($code !== $sms['code'])
      ResponseUtil::failure('手机验证码错误');

    // 绑定
    $status = (new CurdUtil($customerModel))->update(array('union_id' => $unionId, 'type' => CustomerModel::IS_CUSTOMER),
      array('phone' => $phone));

    //echo $this->db->last_query();
    $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();

  }

  /**
   * 技师绑定， 前台绑定
   */
  public function other()
  {
    if (RequestUtil::isPost() && RequestUtil::isAjax()) {
      $unionId = (new WechatUtil())->getUnionId();
      $openId = (new WechatUtil())->getOpenId();

      $params = RequestUtil::postParams();
      $type = $params['type'] + 0;
      $phone = $params['phone'];
      $shopId = $params['shopId'] + 0;
      $beauticianId = 0;

      if (!$type || !$phone)
        ResponseUtil::failure();

      $customerModel = new CustomerModel();
      // 是否已绑定
      $customer = $customerModel->readOneByUnionId($unionId, $type);
      if ($customer['phone'])
        ResponseUtil::failure('微信与手机号已绑定');

      // 如果是技师， 则查询手机号， 然后与open_id 绑定
      if ($type == CustomerModel::IS_BEAUTICIAN) {
        $beautician = (new BeauticianModel())->readByPhone($phone);
        if (!$beautician)
          ResponseUtil::failure('绑定的技师不存在');

        $beauticianId = $beautician['beautician_id'];
      }

      // 检查店铺是否存在
      $shops = (new ShopModel())->getAllShops();
      if (!isset($shops[$shopId]))
        ResponseUtil::failure('店铺不存在');

      $status = (new CurdUtil(new CustomerModel()))->create(array('open_id' => $openId, 'union_id' => $unionId, 'credits' => 0,
        'phone' => $phone, 'type' => $type, 'beautician_id' => $beauticianId, 'shop_id' => $shopId));
      $status ? ResponseUtil::executeSuccess() : ResponseUtil::failure();

    }

    $this->view('bind/other');
  }
}