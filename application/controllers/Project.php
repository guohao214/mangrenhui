<?php

class Project extends FrontendController
{
  public function getList($shop_id)
  {
    $shop_id = $shop_id + 0;

    try {
      $projects = (new CurdUtil(new ProjectModel()))->readLimit(array('disabled' => 0, 'shop_id' => $shop_id), 10);
      ResponseUtil::QuerySuccess($projects);
    } catch (Exception $e) {
      ResponseUtil::failure();
    }

  }

}