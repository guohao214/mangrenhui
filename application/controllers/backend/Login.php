<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 15-12-14
 * Time: 下午10:35
 */
class Login extends BaseController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('form'));
  }

  public function index()
  {
    $error = '';

    if (RequestUtil::isPost()) {
      $validate = new ValidateUtil();
      $validate->required('user_name');
      $validate->required('password');
      $params = RequestUtil::postParams();


      if ($validate->run()) {
        $userModel = new UserModel();
        $params['password'] = $userModel->encodePassword($params['password']);

        $where = array('user_name' => $params['user_name'], 'password' => $params['password']);
        $user = (new CurdUtil($userModel))->readOne($where, 'user_id desc', '*, user_type+0 as type');
        if (!$user) {
          $error = '登录失败，账号或者密码错误，请重试！';
        } else {
          (new CurdUtil($userModel))->update($where, array('last_login_time' => DateUtil::now()));
          UserUtil::saveUser($user);

          if (UserUtil::isAdmin())
            ResponseUtil::redirect(UrlUtil::createBackendUrl('project/index'));
          else
            ResponseUtil::redirect(UrlUtil::createBackendUrl('beautician/index'));

        }
      }
    }

    $this->load->view('backend/login', array('error' => $error));
  }

  /**
   * 退出
   */
  public function logout()
  {
    session_destroy();
    ResponseUtil::redirect(UrlUtil::createBackendUrl('login'));
  }

  public function code()
  {
    ResponseUtil::createImageVerifyCode();
  }
} 