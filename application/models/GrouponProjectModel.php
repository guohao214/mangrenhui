<?php

class GrouponProjectModel extends BaseModel
{
  public $cacheName = 'projects';
  public $formatCacheName = 'formatProjects';

  public function setTable()
  {
    $this->table = 'groupon_project';
  }

  public function rules()
  {
    // 添加验证
    $validate = new ValidateUtil();

    $validate->required('groupon_name');
//    $validate->required('category_id');
//    $validate->numeric('category_id');

    // $validate->required('use_time');
    // $validate->numeric('use_time');

    // $validate->required('price');
    // $validate->numeric('price');

//    $validate->required('order_sort');
//    $validate->numeric('order_sort');

//        $validate->required('suitable_skin');
//        $validate->minLength('suitable_skin', 1);
//        $validate->maxLength('suitable_skin', 500);

//    $validate->required('effects');
//        $validate->minLength('effects', 1);
//        $validate->maxLength('effects', 500);

    return $validate;
  }

  /**
   * 查询拼团项目
   */
  public function readOne($code)
  {
    $sql = "select
        a.*,
        b.project_name,
        c.shop_name,
        c.address,
        c.contact_number,
        (
            select
                count(*) as created
            from
                groupon_order as a
            left join groupon_order_list as d on a.groupon_order_id=d.groupon_order_id
            where
                a.groupon_project_code = '{$code}'
                and d.is_first=1
                and a.disabled = 0
                and d.disabled = 0
        ) as created
    from
        groupon_project as a
        left join project as b on a.groupon_project_id = b.project_id
        left join shop as c on a.shop_id = c.shop_id
    where
        a.disabled = 0
        and a.groupon_project_code = '{$code}'";

    $result = (new CurdUtil($this))->query($sql);
    if ($result) {
      $result = array_pop($result);
      $result['project_cover'] = UploadUtil::buildUploadDocPath($result['project_cover'], '600x600');
      return $result;
    }

    return false;
  }

  /**
   * 拼团项目列表
   */
  public function getList($type = 'ing', $limit) {
    $where = '';

    switch($type) {
      default:
        $where = "a.start_time < now() and now() < a.end_time";
        break;
      case 'wait':
        $where = "a.start_time > now()";
        break;
      case 'end':
        $where = "now() > a.end_time";
        break;
    }


    $sql = "select
        a.*,
        b.project_name,
        c.shop_name,
        c.address,
        c.contact_number,
        (
            select
                count(*)
            from
                groupon_order as e
                left join groupon_order_list as f on e.groupon_order_id = f.groupon_order_id
            where
                e.groupon_project_code = a.groupon_project_code
                and f.order_status = 20
                and e.disabled = 0
                and f.disabled = 0
            group by
                a.groupon_project_code
        ) as pay_counts,
        (
            select
                count(*)
            from
                groupon_order as j
            left join groupon_order_list as k on j.groupon_order_id = k.groupon_order_id
            where
                j.groupon_project_code = a.groupon_project_code
                and j.disabled = 0
                and k.disabled=0
                and k.is_first=1
        ) as open_counts
    from
        groupon_project as a
        left join project as b on a.groupon_project_id = b.project_id
        left join shop as c on a.shop_id = c.shop_id
    where
        a.disabled = 0 and {$where}";

    return (new CurdUtil($this))->query($sql);
  }

}