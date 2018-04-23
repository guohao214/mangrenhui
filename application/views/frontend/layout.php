<?php $this->load->view('frontend/header'); ?>
<?php $baseUrl = get_instance()->config->base_url(); ?>
<?php $class = $this->router->fetch_class(); ?>
<style>
  .footer-tabs {
    display: flex;
    height: 60px;
    width: 100%;
    justify-content: space-around;
    position: fixed;
    bottom: 0;
    background-color: white;
    border-top: 1px solid #EDEDED;
    z-index: 99999;
  }

  .active {
    color: #ffb400;
  }

  .footer-tabs .tab {
    display: inline-block;
    padding: 0 15px;
  }

  .footer-tabs .tab a {
    display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    height: 100%;
    width: 100%;
    text-align: center;
  }

  .footer-tabs .tab a .icon {
    display: inline-block;
    width: 25px;
    margin-bottom: 2px;
  }

  .footer-tabs .tab a .icon img{
    width: 100%;
  }

</style>
<div id="app">
  <?php echo $content; ?>
</div>
<div class="footer-tabs">
  <span class="tab">
    <a href="<?php echo $baseUrl; ?>article/look">
      <span class="icon">
        <?php if ($class === 'article'): ?>
          <img src="<?php echo $baseUrl; ?>static/tab/021.png" alt="">
        <?php else: ?>
          <img src="<?php echo $baseUrl; ?>static/tab/02.png" alt="">
        <?php endif; ?>
      </span>
      <span class="<?php echo $class === 'article' ? 'active' : '' ?>">品牌</span>
    </a>
  </span>
  <span class="tab">
    <a href="<?php echo $baseUrl; ?>appointment">
       <span class="icon">
         <?php if ($class === 'appointment'): ?>
          <img src="<?php echo $baseUrl; ?>static/tab/031.png" alt="">
         <?php else: ?>
           <img src="<?php echo $baseUrl; ?>static/tab/03.png" alt="">
         <?php endif; ?>
       </span>
      <span class="<?php echo $class === 'appointment' ? 'active' : '' ?>">预约</span>
    </a>
  </span>
  <span class="tab">
    <a href="<?php echo $baseUrl; ?>pay/order">
        <span class="icon">
          <?php if ($class === 'pay'): ?>
            <img src="<?php echo $baseUrl; ?>static/tab/011.png" alt="">
          <?php else: ?>
            <img src="<?php echo $baseUrl; ?>static/tab/01.png" alt="">
          <?php endif; ?>
        </span>

      <span class="<?php echo $class === 'pay' ? 'active' : '' ?>">买单</span>
    </a>
  </span>
  <span class="tab">
    <a href="<?php echo $baseUrl; ?>center">
      <span class="icon">
        <?php if ($class === 'center'): ?>
          <img src="<?php echo $baseUrl; ?>static/tab/041.png" alt="">
        <?php else:?>
          <img src="<?php echo $baseUrl; ?>static/tab/04.png" alt="">
        <?php endif ;?>
      </span>
      <span class="<?php echo $class === 'center' ? 'active' : '' ?>">我的</span>
    </a>
  </span>
</div>
</body>
</html>