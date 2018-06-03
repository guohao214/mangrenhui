<?php

class GrouponOrderModel extends BaseModel
{
  public function setTable()
  {
    $this->table = 'groupon_order';
  }

  public function getOne($grouponOrderCode) {
    return (new CurdUtil($this))->readOne( array('groupon_order_code' => $grouponOrderCode, 'disabled' => 0));
  }

  /**
   * 获得团长的支付订单
   */
  public function getFirstOrderList($grouponProjectCode, $openId = '', $grouponOrderCode = '') {
    if ($openId)
      $openId = "a.open_id='{$openId}'";
    else
      $openId = '1=1';


    if ($grouponOrderCode)
      $grouponOrderCode = "a.groupon_order_code='{$grouponOrderCode}'";
    else
      $grouponOrderCode = '1=1';

    $sql = "select
        a.groupon_project_code,
        a.groupon_order_code,
        b.*
    from
        groupon_order as a
        left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
    where
        b.is_first = 1
        and a.disabled = 0
        and b.disabled = 0
        and {$openId}
        and {$grouponOrderCode}
        and a.groupon_project_code = '{$grouponProjectCode}'";

    $result = (new CurdUtil($this))->query($sql);
    if ($result && count($result) > 0)
      return array_pop($result);
    else
      return false;
  }

  /**
   * 获得一个拼团项目的 所有团长的开团信息
   */
  public function getFirstOrderByGrouponProjectCode($grouponProjectCode) {
    $sql = "select
      a.*,
      b.phone_number,
      c.nick_name,
      c.avatar,
      (
          select
              count(*)
          from
              groupon_order_list as k
          where
              k.groupon_order_id = a.groupon_order_id
              and k.order_status = 20
      ) as pay_counts,
      (
          select
              in_peoples
          from
              groupon_project as j
          where
              j.groupon_project_code = a.groupon_project_code
      ) as in_peoples
  from
      groupon_order as a
      left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
      left join customer as c on b.open_id = c.open_id
  where
      a.groupon_project_code = '{$grouponProjectCode}'
      and a.disabled = 0
      and b.disabled = 0
      and b.is_first = 1
      and c.type = 1";

    return (new CurdUtil($this))->query($sql);
  }


  /**
   * 获得一个拼团订单下的所有参团人员信息
   */
  public function getOrdersByGrouponOrderId($grouponOrderId) {
    $sql = "select
        b.*,
        c.nick_name,
        c.avatar
    from
        groupon_order as a
        left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
        left join customer as c on b.open_id = c.open_id
    where
        a.groupon_order_id = {$grouponOrderId}
        and c.type = 1";


    return (new CurdUtil($this))->query($sql);
  }

  /**
   * 正在进行中的项目
   */
  public function getIngOrderByGrouponProjectCode($grouponProjectCode, $grouponOrderCode = '', $openId) {
    $sql = "select
          a.*,
          b.*,
          c.nick_name,
          c.avatar,
          d.in_peoples,
          d.start_time,
          d.end_time,
          (select count(*) from groupon_order as j left join groupon_order_list as k
            on j.groupon_order_id = k.groupon_order_id where j.disabled = 0 and k.disabled = 0
            and j.groupon_order_id = a.groupon_order_id
          ) as in_counts
      from
          groupon_order as a
          left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
          left join groupon_project as d on a.groupon_project_code = d.groupon_project_code
          left join customer as c on b.open_id = c.open_id
      where
          d.groupon_project_code = '{$grouponProjectCode}'
          and c.type = 1
          and a.disabled=0
          and b.order_status=20
          and b.disabled=0
          and d.start_time < now()
          and now() < d.end_time
          and b.open_id != '${openId}'
          and a.groupon_order_code != '{$grouponOrderCode}'
          and b.is_first = 1";


    $result =  (new CurdUtil($this))->query($sql);

    return array_filter($result, function($item) {
        return $item['in_counts'] > 0;
    });
  }

}