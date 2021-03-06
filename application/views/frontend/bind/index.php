<style> #app .group {
    /*padding-top: 1rem*/
    padding: 1rem .25rem 0 .25rem
  }

  #app .little {
    height: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column
  }

  .logo {
    width: 1.2rem;
    height: 1.2rem;
  }

  .logo img {
    width: 100%;
    height: 100%;
    display: block;
  }

  .yd-cell:after {
    border: none;
  }
</style>
<div class="cell" id="bind">
  <div class="little">
    <div class="logo">
      <img src="<?php echo $baseUrl ?>/static/frontend/images/WechatIMG177.jpeg" alt="">
    </div>
    <div>请用手机号码登录</div>
  </div>
  <div class="group">
    <yd-cell-group>
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

      <yd-button-group>
        <yd-button size="large" type="warning" @click.native="done">登录</yd-button>
      </yd-button-group>
    </yd-cell-group>

  </div>

</div>
<script>
  $(function () {

    new Vue({
      el: '#bind',
      data: {
        phone: '',
        start: false,
        smsCode: '',
      },
      mounted: function () {
      },
      methods: {
        done: function () {
          if (!this.smsCode || !/^\d{6}$/.test(this.smsCode)) {
            this.$dialog.toast({
              mes: '验证码错误',
              timeout: 1500
            });

            return
          }

          var self = this
          this.$request.get('bind/bindMe', {code: this.smsCode, phone: this.phone})
            .then(function (data) {
              setTimeout(function () {
                window.location.href = document_root + 'appointment/index'
              }, 2000)
            })
            .catch(function (err) {
              self.$dialog.toast({
                mes: err.detail || '登录失败，请重试',
                timeout: 1500
              });
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


          this.$request.get('sms/send/' + this.phone)
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
      }
    })
  })

</script>