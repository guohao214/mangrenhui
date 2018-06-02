<?php foreach($orders as $order): ?>
<div class="order">
  <div class="avatar">
    <img src="<?php echo $order['avatar'] ?>" alt="">
  </div>

  <div class="info">
    <div class="nick_name"><?php echo $order['nick_name']; ?> - <?php echo $order['phone_number']; ?></div>
    <div>
    <?php echo $order['order_status'] == 20 ? '已支付' : '未支付'; ?>
    支付时间：<?php echo $order['payment_time']; ?>， 支付金额：¥ <?php echo $order['total_fee']; ?></div>
  </div>
</div>
<?php endforeach; ?>