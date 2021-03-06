<?php

class OrderProjectModel extends BaseModel
{

  public function setTable()
  {
    $this->table = 'order_project';
  }

  /**
   * 获得订单下的商品
   * @param $order_id
   */
  public function getOrderProject($order_id)
  {
    $project = new ProjectModel();
    //$category = new CategoryModel();

    $sql = "select {$this->table}.*, {$project->table}.*,{$project->table}.category_id 
                from {$this->table} left join {$project->table}
                on {$this->table}.project_id={$project->table}.project_id
                where {$this->table}.order_id={$order_id} and {$project->table}.disabled=0";

    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function readOneByOrderId($orderId)
  {
    return (new CurdUtil($this))->
    readOne(array('order_id' => $orderId));
  }

} 