<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 15-12-14
 * Time: 下午10:52
 */
class Project extends BackendController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('ProjectModel', 'projectModel');
    $this->projectModel->deleteProjectsCache();
  }

  public function readAllProjectByCategory($categoryId)
  {
    $projects = (new CurdUtil($this->projectModel))
      ->readAll('project_id desc', array('category_id' => $categoryId));

    $html = '';
    foreach ($projects as $project) {
      $html .= "<option value={$project['project_id']}>{$project['project_name']}</option>";
    }

    ResponseUtil::json($html);
  }


  public function index($limit = 0)
  {
    $where = RequestUtil::buildLikeQueryParamsWithDisabled();

    $projects = (new CurdUtil($this->projectModel))->readLimit($where, $limit, 'project_id desc');
    $projectsCount = (new CurdUtil($this->projectModel))->count($where);
    $pages = (new PaginationUtil($projectsCount))->pagination();
    $categories = (new CategoryModel())->getAllCategories();
    $shops = (new ShopModel())->getAllShops();

    $categoryId = $this->input->get('category_id') + 0;

    $this->view('project/index', array('projects' => $projects, 'shops' => $shops, 'limit' => $limit + 0,
      'pages' => $pages, 'categories' => $categories, 'params' => RequestUtil::getParams(),
      'categoryId' => $categoryId));
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

  /**
   * 首页展示
   * @param $projectId
   * @param $limit
   * @param $onIndex
   */
  public function onIndex($projectId, $limit, $onIndex)
  {
    $onIndex = ($onIndex) ? 0 : 1;

    if (!$projectId)
      $this->message('项目ID不能为空！');

    if ((new CurdUtil($this->projectModel))->update(
      array('project_id' => $projectId), array('on_index' => $onIndex))
    )
      $this->message('修改成功！', "project/index/{$limit}");
    else
      $this->message('修改失败！', "project/index/{$limit}");
  }

  public function updateProject($project_id, $limit = 0)
  {
    if (RequestUtil::isPost()) {
      if ($this->projectModel->rules()->run()) {
        $params = RequestUtil::postParams();

        $upload = UploadUtil::commonUpload(array('upload/resize_200x200',
          'upload/resize_600x600', 'upload/resize_100x100'));

        if ($upload)
          $params['project_cover'] = $upload;

        $params['updated_time'] = DateUtil::now();
        if ((new CurdUtil($this->projectModel))->update(array('project_id' => $project_id), $params))
          $this->message('修改项目成功!', 'project/updateProject/' . $project_id . "/{$limit}");
        else
          $this->message('修改项目失败!', 'project/updateProject/' . $project_id . "/{$limit}");
      }

    }

    $shops = (new ShopModel())->getAllShops();
    $project = $this->projectModel->readOne($project_id);
    if (!$project)
      $this->message('项目不存在或者已被删除！', "project/index/{$limit}");

    $this->view('project/updateProject', array( 'project' => $project, 'shops' => $shops, 'limit' => $limit,));
  }

  public function addProject()
  {
    if (RequestUtil::isPost()) {
      if ($this->projectModel->rules()->run()) {
        $params = RequestUtil::postParams();
        unset($params['main_project_id']);

        $params['project_cover'] = UploadUtil::commonUpload(array('upload/resize_200x200',
          'upload/resize_600x600', 'upload/resize_100x100'));
        $insertId = (new CurdUtil($this->projectModel))->
        create(array_merge($params, array('created_time' => DateUtil::now())));
        if ($insertId)
          $this->message('新增项目成功!', 'project/index');
        else
          $this->message('新增项目失败!', 'project/index');
      }

    }

//    $categories = (new CategoryModel())->getAllCategories();
    $shops = (new ShopModel())->getAllShops();
    $this->view('project/addProject', array('shops' => $shops));
  }
} 