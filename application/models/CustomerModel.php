<?php

/**
 * Created by PhpStorm.
 * User: GuoHao
 * Date: 2016/2/22
 * Time: 23:05
 */
class CustomerModel extends BaseModel
{
  const IS_CUSTOMER = 1;
  const IS_FRONTEND = 2;
  const IS_BEAUTICIAN = 3;

  public function setTable()
  {
    $this->table = 'customer';
  }

  /**
   * 增加积分
   * @param $openId
   * @param $score
   */
  public function addCredits($openId = '', $score = 0)
  {
    $this->db->set('credits', "credits+{$score}", FALSE);
    $this->db->where(array('open_id' => $openId));
    $this->db->update($this->table);
    return $this->db->affected_rows();
  }

  /**
   * 减去积分
   * @param $openId
   * @param $score
   * @return mixed
   */
  public function subCredits($openId, $score)
  {
    $this->db->set('credits', "credits-{$score}", FALSE);
    $this->db->where(array('open_id' => $openId));
    $this->db->update($this->table);
    return $this->db->affected_rows();
  }

  /**
   * @param $beauticianId
   * @return mixed
   */
  public function getBeautician($beauticianId)
  {
    return (new CurdUtil($this))->readOne(
      array('type' => CustomerModel::IS_BEAUTICIAN, 'beautician_id' => $beauticianId, 'disabled' => 0), 'customer_id desc');
  }

  /**
   * 获取前台通知
   */
  public function getFront()
  {
    return (new CurdUtil($this))->readAll('customer_id desc', array('type' => CustomerModel::IS_FRONTEND, 'disabled' => 0));
  }

  /**
   * 读取记录
   * @param $openId
   * @return mixed
   */
  public function readOne($openId, $type)
  {
    return (new CurdUtil($this))->readOne(array('open_id' => $openId, 'type' => $type));
  }


  public function readOneByUnionId($unionId, $type)
  {
    return (new CurdUtil($this))->readOne(array('union_id' => $unionId, 'type' => $type));
  }

  /**
   * 增加记录
   * @param $openId
   * @param int $credits
   * @return mixed
   */
  public function create($openId, $credits = 0)
  {
    return (new CurdUtil($this))->create(array('open_id' => $openId, 'credits' => $credits));
  }

  public function insertXcx($unionId, $openId='', $credits = 0)
  {
    $data = array(
      'union_id' => $unionId,
      'xcx_open_id' => $openId,
      'open_id' => '',
      'credits' => $credits,
      'updated_time' => DateUtil::now()
    );

    return (new CurdUtil($this))->create($data);
  }

  public function insert($unionId, $openId, $credits = 0, $nickName, $avatar, $city, $province, $sex)
  {
    $data = array(
      'union_id' => $unionId,
      'open_id' => $openId,
      'credits' => $credits,
      'nick_name' => $nickName,
      'avatar' => $avatar,
      'city' => $city,
      'province' => $province,
      'sex' => $sex,
      'updated_time' => DateUtil::now()
    );

    return (new CurdUtil($this))->create($data);
  }

  public function update($openId, $nickName, $avatar, $city, $province, $sex, $unionId = '')
  {
    $data = array(
      'nick_name' => $nickName,
      'avatar' => $avatar,
      'city' => $city,
      'province' => $province,
      'sex' => $sex,
      'updated_time' => DateUtil::now()
    );

    if ($unionId)
      $data['union_id'] = $unionId;

    return (new CurdUtil($this))->update(array('open_id' => $openId), $data);
  }

  public function updateXcx($unionId, $openId, $nickName = '', $avatar = '', $city = '', $province = '', $sex = 1)
  {
    $data = array(
      'nick_name' => $nickName,
      'xcx_open_id' => $openId,
      'avatar' => $avatar,
      'city' => $city,
      'province' => $province,
      'sex' => $sex,
      'updated_time' => DateUtil::now()
    );

    return (new CurdUtil($this))->update(array('union_id' => $unionId), $data);
  }

  /**
   * 查找用户
   * @param $openIds
   */
  public function findCustomer($openIds)
  {
    $openIds = "'" . join("','", $openIds) . "'";

    $sql = "select count(*) as count_open_ids from {$this->table} where open_id in ({$openIds});";

    $data = (new CurdUtil($this))->query($sql);

    return $data[0]['count_open_ids'];
  }
}