<?php $this->load->view('frontend/header'); ?>
<style>
  body {
    background-color: rgb(241,241,241);
  }
  .top-header {
    display:none;
  }

  #app_body .header_body{
    display: flex;
    padding:.5rem .3rem;
    background-color: white;
  }

  #app_body .header_body .cover {
    width: 2rem;
  }
  #app_body .header_body .cover img {
    display:block;
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
    display:flex;
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
    margin-top:.3rem;
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
    margin-right:.1rem
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
    width:100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index:99999;
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
    width:2rem;
    color:white;
    align-self: flex-end;
    margin-right: 1rem;
  }

  .mask .arrow img {
    width:100%;
    display:block
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
            <span>x<?php echo $grouponProject['in_counts']; ?></span>
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
          <span style="color:red;">{%d}<i>天</i></span>
          <span style="color:gray;">{%h}<i>时</i></span>
          <span style="color:blue;">{%m}<i>分</i></span>
          <span style="color:orange;">{%s}<i>秒</i></span>
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
var listNo = '<?php echo $listNo; ?>'
var shareTitle = '<?php echo $grouponProject['groupon_name']; ?>'
var shareImage = '<?php echo $grouponProject['project_cover']; ?>'
var shareLink = "http://www.mlxiaowu.com/groupon/grouponIndex/<?php echo $grouponProject['groupon_project_code']?>/<?php echo $order['groupon_order_code'] ?>"

$(document).ready(function() {
  new Vue({
    el: '#app_body',
    data: {
      showMask: false,
      phone: '',
      start: false,
      smsCode: '',
      endTime: '<?php echo str_replace('-', '/', $grouponProject['end_time']); ?>'
    },
    mounted: function() {
      
    },
    methods: {
      share: function () {

        var url = encodeURIComponent(window.location.href)

        var self = this
        if (!WeixinJSBridge || !WeixinJSBridge.invoke) {
          self.$dialog.toast({
            mes: '您的环境不支持微信支付，请在微信里打开',
            timeout: 1500
          })
          return
        }

        this.$request.post('groupon/shareParams', { url: url})
          .then(function (data) {
            let result = data.content

            wx.config({
              debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
              appId: result.appId, // 必填，公众号的唯一标识
              timestamp: result.timestamp, // 必填，生成签名的时间戳
              nonceStr: result.nonceStr, // 必填，生成签名的随机串
              signature: result.signature,// 必填，签名，见附录1
              jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline']
           })

           wx.ready(function() {
            wx.onMenuShareTimeline({
              title: shareTitle, // 分享标题
              link: shareLink, // 分享链接
              imgUrl: shareImage, // 分享图标
              success: function () {
              },
              cancel: function () {
              }
            })


            // 分享给朋友
            wx.onMenuShareAppMessage({
              title: shareTitle, // 分享标题
              link: shareLink, // 分享链接
              imgUrl: shareImage, // 分享图标
              desc: shareTitle,
              dataUrl: '', 
              success: function () {
              },
              cancel: function () {
              }
             })

             self.showMask = true
           })
           
          })
          .catch(error => {
            self.$dialog.toast({
              mes: error.detail || '支付失败，请重试',
              timeout: 1500
            })
          })
      },
      sendCode: function () {
      var self = this

      if (!this.phone || !/^1\d{10}$/.test(this.phone)) {
        this.$dialog.toast({
          mes: '手机号格式错误',
          timeout: 1500
        });

        return
      }

      this.$request.get('groupon/sendSmsCode/' + this.phone)
        .then(function (data) {
          self.$dialog.toast({
            mes: '已发送',
            icon: 'success',
            timeout: 1500
          });

          self.start = true;

        })
        .catch(function (err) {
          self.$dialog.toast({
            mes: err.detail || '验证码发送失败',
            timeout: 1500
          });
        })
      },
    },

  })
})
</script>
</html>