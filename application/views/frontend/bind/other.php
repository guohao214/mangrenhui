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

  .yd-cell:after {
    border: none;
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
</style>
<div class="cell" id="bind">
  <div class="little">
    <div class="logo">
      <img src="<?php echo $baseUrl?>/static/frontend/images/WechatIMG177.jpeg" alt="">
    </div>
    <div>绑定手机号</div>
  </div>

  <div class="group">
    <yd-cell-group>
      <yd-cell-item>
        <span slot="left">手机号：</span>
        <yd-input slot="right" v-model="phone" regex="^1\d{10}$" placeholder="请输入手机号" max="11"/>
      </yd-cell-item>

      <yd-button-group>
        <yd-button size="large" type="warning" @click.native="bind">确定</yd-button>
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
      },
      mounted: function () {
      },
      methods: {
        bind: function () {
          var self = this
          var type = this.$tool.getQueryString('type')

          if (!type)
            return

          this.$request.post('bind/other', {phone: this.phone, type: type})
            .then(function (data) {
              self.$dialog.toast({
                mes: '手机号与微信绑定成功',
                timeout: 1500
              });

              setTimeout(function () {
                window.location.href = document_root + 'appointment/index'
              }, 2000)
            })
            .catch(function (err) {
              self.$dialog.toast({
                mes: err.detail || '手机号与微信绑定失败',
                timeout: 1500
              });
            })
        },
      }
    })
  })

</script>