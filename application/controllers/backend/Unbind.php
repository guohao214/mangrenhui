<?php


class Unbind extends BackendController
{
  public function index()
  {
    $customers = (new CustomerModel())->getBindCustomer();
    $this->view('unbind/index', array('customers' => $customers));
  }

  public function delete() {
    $params = RequestUtil::getParams();
    $customerId = $params['customer_id'];

    $status = (new CurdUtil(new CustomerModel()))->update(['customer_id' => $customerId], ['disabled' => 1]);

    $this->message('解绑成功!', 'unbind/index');
  }
}