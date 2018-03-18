<style> #app .group {
    padding-top: 1rem
  }

  #app .little {
    height: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column
  }
</style>
<div class="cell" id="bind">
  <div class="little">
    <yd-icon name="weixin"></yd-icon>
    <div>绑定手机号</div>
  </div>
  <div class="group">
    <yd-cell-group>
      <yd-cell-item>
        <yd-icon slot="icon" name="phone3" size=".45rem"></yd-icon>
        <input type="text" slot="right" placeholder="请输入手机号码" v-model="phone"/>

        <yd-sendcode slot="right"
                     v-model="start"
                     @click.native="sendCode"
                     type="warning"/>

      </yd-cell-item>
    </yd-cell-group>

    <yd-keyboard v-model="showKeyboard"
                 title=""
                 input-text="输入验证码"
                 :trigger-close="false"
                 cancel-text="取消"
                 :callback="done" ref="keybord"/>
  </div>

</div>
<script>
  $(function () {

    new Vue({
      el: '#bind',
      data: {
        phone: '',
        start: false,
        showKeyboard: false
      },
      mounted: function () {
      },
      methods: {
        done: function (value) {
          var self = this
          this.$request.get('bind/bindMe', {code: value, phone: this.phone})
            .then(function (data) {
              setTimeout(function () {
                window.location.href = document_root + 'appointment/index'
              }, 2000)
            })
            .catch(function (err) {
              self.$refs.keybord.$emit('ydui.keyboard.error', err.detail || '对不起，验证码不正确，请重新输入。');
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

              self.showKeyboard = true
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