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
</style>

<div id="app_body">
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
      <span slot="left">手机号：</span>
        <yd-input slot="right" placeholder="请输入手机号码" regex="^1\d{10}$"  max="11" v-model="phone"></yd-input>
      </yd-cell-item>

    <yd-cell-item>
      <span slot="left">验证码：</span>
      <yd-input slot="right" placeholder="请输入验证码"  regex="^\d{6}$"  v-model="smsCode"  max="6"></yd-input>
      <yd-sendcode slot="right"
                    v-model="start"
                    @click.native="sendCode"
                    type="warning"/>
    </yd-cell-item>
    <yd-cell-item>
      <span slot="left" class="flex"><yd-icon name="weixin" class="green"></yd-icon>微信支付</span>
      <span slot="right" class="f-3rem">¥ <?php echo $grouponProject['groupon_price']; ?></span>
    </yd-cell-item>
  </yd-cell-group>

  <div class="footer" id="footer">
    <yd-button-group>
      <yd-button size="large" type="warning" @click.native="pay">立即支付¥ <?php echo $grouponProject['groupon_price']; ?></yd-button>
    </yd-button-group>
  </div>
</div>

</body>

<script>
var listNo = '<?php echo $listNo; ?>'

$(document).ready(function() {
  new Vue({
    el: '#app_body',
    data: {
      phone: '',
      start: false,
      smsCode: '',
    },
    mounted: function() {},
    methods: {
      pay: function () {
        if (!listNo)
          return 
        
        if (!this.phone) {
          this.$dialog.toast({
            mes: '请输入手机号',
            timeout: 1500
          })

          return
        }
       
        if (!this.smsCode) {
          this.$dialog.toast({
            mes: '请输入验证码',
            timeout: 1500
          })

          return
        }


        var self = this
        if (!WeixinJSBridge || !WeixinJSBridge.invoke) {
          self.$dialog.toast({
            mes: '您的环境不支持微信支付，请在微信里打开',
            timeout: 1500
          })
          return
        }

        this.$request.post('groupon/payParams/' + listNo, this.$data)
          .then(function (data) {
            let result = data.content

            WeixinJSBridge.invoke(
              'getBrandWCPayRequest',
              result,
              function (res) {
                if (res.err_msg == 'get_brand_wcpay_request:ok') {
                  self.$dialog.toast({
                    mes: '支付成功,等待数据确认，请稍等...',
                    timeout: 1500
                  })
                } else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                  //alert('取消支付');
                  return false;
                } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                  self.$dialog.toast({
                    mes: '支付失败，请重试',
                    timeout: 1500
                  })
                } else {
                  return false;
                }
              }
            )
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