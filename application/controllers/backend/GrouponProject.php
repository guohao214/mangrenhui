<?php

class GrouponProject extends BackendController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('GrouponProjectModel', 'grouponProjectModel');
  }

  public function index($limit = 0)
  {
    $params = RequestUtil::getParams();
    $type = $params['type'];
    // $projectName = $params['projectName'];

    $projects = $this->grouponProjectModel->getList($type, $limit);

    $this->view('grouponProject/index', array('projects' => $projects, 'limit' => $limit + 0, 'params' => $params));
  }

  /**
   * 一个项目的所有开团 团长
   */
  public function detail($grouponProjectCode) {
    $orders = (new GrouponOrderModel())->getFirstOrderByGrouponProjectCode($grouponProjectCode);
    $this->view('grouponProject/detail', array('orders' => $orders));
  }

  /**
   * 分享二维码
   */
  public function share($grouponProjectCode) {
    $url = UrlUtil::getBaseUrl() . 'groupon/grouponIndex/' . $grouponProjectCode;
    $this->view('grouponProject/share', array('url' => $url));
  }

  /**
   * 获得一个开团下面的所有订单
   */
  public function getAllOrdersByGrouponOrderId($grouponOrderId) {
    $orders = (new GrouponOrderModel())->getOrdersByGrouponOrderId($grouponOrderId);
    $this->load->view('backend/grouponProject/order', array('orders' => $orders));
  }


  public function deleteProject($project_id, $limit = 0)
  {
    if (!$project_id)
      $this->message('项目ID不能为空！');

    if ((new CurdUtil($this->projectModel))->update(array('project_id' => $project_id), array('disabled' => 1)))
      $this->message('删除项目成功！', "project/index/{$limit}");
    else
      $this->message('删除项目失败！', "project/index/{$limit}");
  }




  public function updateProject($grouponProjectCode, $limit = 0)
  {
    if (RequestUtil::isPost()) {
      if ($this->grouponProjectModel->rules()->run()) {
        $params = RequestUtil::postParams();

        $upload = UploadUtil::commonUpload(array('upload/resize_200x200',
          'upload/resize_600x600', 'upload/resize_100x100'));

        if ($upload)
          $params['project_cover'] = $upload;


        unset($params['groupon_project_code']);
        

        $params['updated_time'] = DateUtil::now();
        if ((new CurdUtil($this->grouponProjectModel))->update(array('groupon_project_code' => $grouponProjectCode), $params))
          $this->message('修改项目成功!', 'grouponProject/updateProject/' . $grouponProjectCode . "/{$limit}");
        else
          $this->message('修改项目失败!', 'grouponProject/updateProject/' . $grouponProjectCode . "/{$limit}");
      }

    }

    $shops = (new ShopModel())->getAllShops();
    $project = $this->grouponProjectModel->readOne($grouponProjectCode);

    //var_dump($project);
    if (!$project)
      $this->message('项目不存在或者已被删除！', "grouponProject/index/{$limit}");

    $this->view('grouponProject/updateProject', array( 'project' => $project, 'shops' => $shops, 'limit' => $limit,));
  }

  /**
   * 
   */
  public function addProject()
  {
    if (RequestUtil::isPost()) {
      if ($this->grouponProjectModel->rules()->run()) {
        $params = RequestUtil::postParams();
        unset($params['main_project_id']);

        $params['groupon_project_code'] = md5(DateUtil::now() . mt_rand(1, 1000000));

        $params['project_cover'] = UploadUtil::commonUpload(array('upload/resize_200x200',
          'upload/resize_600x600', 'upload/resize_100x100'));
        $insertId = (new CurdUtil($this->grouponProjectModel))->
          create(array_merge($params, array('created_time' => DateUtil::now())));
        if ($insertId)
          $this->message('新增项目成功!', 'grouponProject/index');
        else
          $this->message('新增项目失败!', 'grouponProject/index');
      }

    }

    $shops = (new ShopModel())->getAllShops();
    $this->view('grouponProject/addProject', array('shops' => $shops));
  }
} 