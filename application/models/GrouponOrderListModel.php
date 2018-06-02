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
  echo  $sql = "select a.* from groupon_order_list as a where UNIX_TIMESTAMP(a.created_time) + {$time} < now() and a.disabled=0";
    return (new CurdUtil($this))->query($sql);
  }

}