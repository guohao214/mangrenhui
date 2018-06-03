<?php

class GrouponOrderListModel extends BaseModel
{
  public function setTable()
  {
    $this->table = 'groupon_order_list';
  }

  public function getOne($openId, $grouponOrderId) {
    return (new CurdUtil($this))->readOne(array('open_id' => $openId, 'groupon_order_id' => $grouponOrderId, 'disabled' => 0));
  }

  public function phoneNumberIsNotJoin($grouponProjectCode, $projectId, $phoneNumber) {
    $sql = "select
          *
      from
          groupon_order_list as a
      where
          a.ggroupon_project_code = '${grouponProjectCode}'
          and a.project_id = {$projectId}
          and a.phone_number = '${phoneNumber}'";

    $result = (new CurdUtil($this))->query($sql);
    //var_dump($this->db->last_query());
    if (!$result || count($result) === 0)
      return true;
    else
      return false;
  }

  public function getOrder($openId, $listNo) {
    $sql = "select
        a.groupon_project_code,
        a.groupon_order_code,
        b.*
    from
        groupon_order as a
        left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
    where
        a.disabled = 0
        and b.disabled = 0
        and b.groupon_order_list_no = '{$listNo}'
        and b.open_id = '{$openId}'";

    $result = (new CurdUtil($this))->query($sql);
    if ($result)
      return array_pop($result);
    else
      return false;
  }

   /**
   * 清除过期的订单， 默认5分钟过期
   */
  public function getInvalidOrders($time = 300) {
    $sql = "select
          a.*
      from
          groupon_order_list as a
      where
          UNIX_TIMESTAMP(a.created_time) + { $ time } < UNIX_TIMESTAMP(now())
          and a.disabled = 0
          and order_status != 20";

    return (new CurdUtil($this))->query($sql);
  }

  /**
   * 查询已经使用了的开团订单数量， （预约用团购订单支付）
   */
  public function orderUseCounts($phoneNumber, $projectId) {
    $sql = "select 
        *,
        (
            select
                count(*)
            from
                groupon_order_use as b
            where
                b.groupon_order_list_id = a.groupon_order_list_id
            group by
                a.groupon_order_list_id
        ) as use_counts
      from
          groupon_order_list as a
      where
          phone_number = '{$phoneNumber}'
          and disabled = 0
          and order_status = 20
          and project_id = {$projectId}";

    $result =  (new CurdUtil($this))->query($sql);
    if (!$result || count($result) === 0)
      return null;
    else
      return array_pop($result);
  }

}