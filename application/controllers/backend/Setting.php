<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2018/4/16
 * Time: 22:15
 */

class Setting extends BackendController
{
  /**
   * 联系方式
   */
  public function contactPhone()
  {
    $path = '../application/config/phone.php';

    if (RequestUtil::isPost()) {
      $params = RequestUtil::postParams();
      $phone = $params['phone'];
      if ($phone) {
        $content = "<?php\nreturn " . var_export($params, true) . ";\n?>";
        file_put_contents($path , $content);
      }
    }

    $phone = include_once ($path);
    $this->view('setting/contactPhone', array('phone' => $phone));
  }
}