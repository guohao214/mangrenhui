<style>
  #center {
    padding-top:.5rem;
  }
  #center .little { height: 2rem; display: flex; justify-content: center;align-items: center; flex-direction: column }
  .logo {
    width: 1.2rem;
    height: 1.2rem;
  }

  .logo img {
    width: 100%;
    height: 100%;
    display: block;
  }
</style>
<div id="center">
  <div class="little">
    <div class="logo">
      <img src="<?php echo $customer['avatar'];?>" alt="">
    </div>
    <div><?php echo $customer['nick_name'];?></div>
  </div>
  <yd-cell-group>
    <yd-cell-item arrow type="a" href="<?php echo UrlUtil::createUrl('center/order'); ?>">
      <span slot="left">我的订单</span>
    </yd-cell-item>
    <yd-cell-item type="a" href="tel:<?php echo $phone; ?>">
      <span slot="left">联系我们: <?php echo $phone; ?></span>
    </yd-cell-item>
  </yd-cell-group>
</div>

<script>
  new Vue({
    el: '#center'
  })
</script>