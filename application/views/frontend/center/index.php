<style>
  #center .little { height: 2rem; display: flex; justify-content: center;align-items: center; flex-direction: column }
</style>
<div id="center">
  <div class="little">
    <yd-icon name="ucenter"></yd-icon>
    <div>个人中心</div>
  </div>
  <yd-cell-group>
    <yd-cell-item arrow type="a" href="<?php echo UrlUtil::createUrl('center/order'); ?>">
      <span slot="left">我的订单</span>
    </yd-cell-item>
    <yd-cell-item type="a" href="tel:88889999">
      <span slot="left">联系我们: 8899999</span>
    </yd-cell-item>
  </yd-cell-group>
</div>

<script>
  new Vue({
    el: '#center'
  })
</script>