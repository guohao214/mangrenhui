<?php $this->load->view('frontend/header'); ?>
<style>
  body {
    background-color: rgb(241, 241, 241);
  }

  .top-header {
    display: none;
  }

  #app_body .header_body {
    display: flex;
    padding: .5rem .3rem;
    background-color: white;
  }

  #app_body .header_body .cover {
    width: 2rem;
  }

  #app_body .header_body .cover img {
    display: block;
    width: 100%;
    height: 100%;
  }

  .groupon_project {
    padding: 0 .2rem;
    flex-grow: 1
  }

  .groupon_project .project_name {
    font-size: .35rem;
    padding-bottom: .5rem
  }

  .groupon_project .project_info {
    display: flex;
    /* font-size: .3rem; */
    /* padding: .2rem 0; */
    flex-direction: column;
  }

  .groupon_project .project_info .price {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: red;
    font-size: .35rem;
  }

  .groupon_project .project_info .groupon_price {
    font-size: .35rem;
    color: black;
  }

  .groupon_project .project_info .old_price {
    color: gray;
    text-decoration: line-through;
  }

  .groupon {
    display: flex;
    padding: .27rem .27rem .27rem 0;
  }

  .groupon .avatar {
    width: 1.3rem
  }

  .groupon .avatar img {
    width: 100%;
    display: block;
  }

  .groupon .info {
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    flex-direction: column;
    padding: 0 .2rem;
  }

  .groupon .peoples {
    padding-top: .2rem
  }

  .sms {
    margin-top: .3rem;
  }

  .yd-badge-danger {
    /* background-color: #ef4f4f; */
    color: gray;
    /* padding: .1rem .25rem; */
    /* color: #ef4f4f; */
    background: none;
    /* border: 1px solid #ef4f4f; */
    /* text-align: left; */
    padding: .1rem 0;
  }

  #footer {
    position: fixed;
    /*margin-bottom: .3rem;*/
    /*left: 0;*/
    width: 100%;
    z-index: 999;
    bottom: .1rem;
  }

  div[v-show] {
    display: none;
  }

  .yd-cell-item {
    /* padding: .2rem .1rem; */
  }

  .green {
    color: green;
    margin-right: .1rem
  }

  .flex {
    display: flex;
    align-items: center;
  }

  .f-3rem {
    font-size: .3rem
  }

  .mask {
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999;
    display: flex;
    flex-direction: column;
    align-items: center;

  }

  .mask div {
    margin: .1rem;
  }

  .mask .text {
    color: white;
    font-size: .35rem;
    width: 5rem;
    border: 2px dashed white;
    padding: .5rem .8rem;
    border-radius: 50%;
    text-align: center;
  }

  .mask .arrow {
    width: 2rem;
    color: white;
    align-self: flex-end;
    margin-right: 1rem;
  }

  .mask .arrow img {
    width: 100%;
    display: block
  }
</style>

<div id="app_body">

  <div class="mask" v-show="showMask">
    <div class="arrow">
      <img src="/static/frontend/images/arrow.png" alt="">
    </div>
    <div class="text">
      点击右上角发送给好友或者分享到朋友圈
    </div>
    <div class="button">
      <yd-button type="warning" @click.native="showMask = false">知道了</yd-button>
    </div>
  </div>

  <div class="header_body">
    <div class="cover">
      <img src="<?php echo $grouponProject['project_cover']; ?>" alt="">
    </div>
    <div class="groupon_project">
      <div class="project_name"><?php echo $grouponProject['groupon_name']; ?></div>
      <div class="project_info">
        <div>
          <yd-badge shape="square" type="danger"><?php echo $grouponProject['in_peoples']; ?>人团</yd-badge>
        </div>
        <div class="price">
          <span class="groupon_price">¥ <?php echo $grouponProject['groupon_price']; ?></span>
          <span><?php echo $grouponProject['in_counts']; ?>次</span>
        </div>
      </div>
    </div>
  </div>


  <yd-cell-group class="sms">
    <yd-cell-item>
      <span slot="left" class="flex"></yd-icon>微信支付：</span>
      <span slot="right" class="f-3rem">已支付：¥ <?php echo $grouponProject['groupon_price']; ?></span>
    </yd-cell-item>
  </yd-cell-group>


  <yd-cell-group>
    <yd-cell-item>
      <span slot="left" class="flex"></yd-icon>订单号：</span>
      <span slot="right" class="f-3rem"><?php echo $listNo; ?></span>
    </yd-cell-item>
    <yd-cell-item>
      <span slot="left" class="flex"></yd-icon>下单时间：</span>
      <span slot="right" class="f-3rem"><?php echo $order['created_time']; ?></span>
    </yd-cell-item>
    <yd-cell-item>
      <span slot="left" class="flex"></yd-icon>支付时间：</span>
      <span slot="right" class="f-3rem"><?php echo $order['payment_time']; ?></span>
    </yd-cell-item>
  </yd-cell-group>

  <yd-cell-group v-if="endTime">
    <yd-cell-item>
      <span slot="left" class="flex"></yd-icon>倒计时:</span>
      <span slot="right" class="f-3rem">
        <yd-countdown :time="endTime">
          <span style="color:gray;">{%d}<i>天</i></span>
          <span style="color:gray;">{%h}<i>时</i></span>
          <span style="color:gray;">{%m}<i>分</i></span>
          <span style="color:gray;">{%s}<i>秒</i></span>
        </yd-countdown>
      </span>
    </yd-cell-item>
  </yd-cell-group>

  <div class="footer" id="footer">
    <yd-button-group>
      <yd-button size="large" type="warning" @click.native="share">召集小伙伴参团</yd-button>
    </yd-button-group>
  </div>
</div>

</body>

<script>
  $(document).ready(function () {
    new Vue({
      el: '#app_body',
      data: {
        showMask: false,
        endTime: '<?php echo str_replace('-', '/', $grouponProject['end_time']); ?>'
      },
      mounted: function () {
      },
      methods: {
        share: function () {
          this.showMask = true
        },
      },

    })
  })
</script>

<?php echo $sharePage; ?>
</html>