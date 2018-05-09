<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">首页</a>
    <span class="crumb-step">&gt;</span>
    <a class="crumb-name" href="<?php echo UrlUtil::createBackendUrl("order/index/{$limit}") ?>">订单管理</a>
    <span class="crumb-step">&gt;</span><span>订单详情</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <h1 class="table-title">订单详情</h1>
    <table class="insert-tab" width="100%">
      <tbody>
      <tr>
        <th width="120">订单号：</th>
        <td width="400">
          <?php echo $order['order_no']; ?>
        </td>
        <th width="120">预约日期：</th>
        <td>
          <?php echo $order['appointment_day']; ?>
        </td>
      </tr>

      <tr>
        <th width="120">开始时间：</th>
        <td>
          <?php echo $order['appointment_start_time']; ?>
        </td>
        <th width="120">结束时间：</th>
        <td>
          <?php echo $order['appointment_end_time']; ?>
        </td>
      </tr>

      <tr>
        <th width="120">美容师：</th>
        <td>
          <?php echo $order['beautician_name']; ?>
        </td>
<!--        <th width="120">预约项目：</th>-->
<!--        <td>-->
<!--          --><?php //foreach ($orderProjects as $project): ?>
<!--            <p>--><?php //echo $project['project_name']; ?><!--</p>-->
<!--          --><?php //endforeach; ?>
<!--        </td>-->
      </tr>

      <tr>
        <th>金额：</th>
        <td>￥<?php echo $order['total_fee']; ?></td>
<!--        <th>订单状态：</th>-->
<!--        <td>--><?php //echo $order['order_status']; ?><!--</td>-->
<!--      </tr>-->
<!---->
<!--      <tr>-->
<!--        <th>联系人：</th>-->
<!--        <td>--><?php //echo $order['user_name']; ?><!--</td>-->
        <th>手机号：</th>
        <td><?php echo $order['phone_number']; ?></td>
      </tr>

      <tr>
        <th>下单时间：</th>
        <td><?php echo $order['created_time']; ?></td>
        <th>订单门店：</th>
        <td><?php echo ($order['shop_id']) ? $shops[$order['shop_id']] : '所有门店'; ?></td>

      </tr>

      </tbody>
    </table>

    <?php if ($orderProjects): ?>
      <h1 class="table-title">订单项目</h1>
      <?php foreach ($orderProjects as $orderProject): ?>
        <table class="insert-tab" width="100%" style="margin: 10px 0">
          <tr>
            <th width="120">项目名：</th>
            <td><?php echo $orderProject['project_name']; ?></td>
            <th width="120">使用时间：</th>
            <td><?php echo $orderProject['use_time']; ?> 分钟</td>
          </tr>

          <tr>
            <th width="120">价格：</th>
            <td width="400"><?php echo $orderProject['price']; ?> 元</td>
            <th width="120">购买数量：</th>
            <td colspan="3">1</td>
          </tr>
        </table>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>