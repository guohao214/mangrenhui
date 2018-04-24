<style>
  #order {
    padding-bottom: 3rem;
  }

  .yd-cell-title {
    padding: .2rem;
    text-align: center;
    color: #ffb400;
  }
  #order .item {
    margin: .2rem 0
  }

  .notice {
    display: flex;
    height: 5rem;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }

  .notice .icon {
    width: 1.5rem;
    margin-bottom: .2rem;
  }

  .yd-preview-footer {
    height: 1.1rem;
    border: none;
    padding: .2rem;
  }

  .yd-preview:after {
    border: none
  }

  .yd-preview-footer:before, .yd-preview-footer:after {
    /* border:none */
  }

  .yd-preview-footer a {
    border: 1px solid #ccc;
    border-radius: .1rem;
    height: .7rem;
    margin: 0 .2rem;
    color: black;
    font-size: .2rem;
    text-align: center;
    position: relative;
    pointer-events: auto;
    border-radius: 3px;
  }

  .yd-preview-footer a:active{
    background-color: #f7f7f7;
  }

  .yd-preview-footer a:last-child {
    background-color: #ffb400;
    color: black;
    border-color: #EDEDED;
  }

  .yd-popup-content {
    padding: .2rem;
  }

  div[v-if] {
    display: none;
  }

  .yd-confirm-ft {
    border-left: 1px solid #d9d9d9;
    border-right: 1px solid #d9d9d9;
  }
</style>
<div id="order">

  <yd-popup v-model="showCash" position="center" width="6rem" height="3.4rem">
    <yd-cell-group title="请输入代收技师工号">
      <yd-cell-item>
        <yd-input slot="right" v-model="beauticianCode" placeholder="请输入代收技师工号" ref="beauticianCode"></yd-input>
      </yd-cell-item>

      <div class="yd-confirm-ft">
        <a href="javascript:;" class="yd-confirm-btn default" @click="showCash = false">取消</a>
        <a href="javascript:;" class="yd-confirm-btn primary" @click="payOffline">确定</a></div>
    </yd-cell-group>
  </yd-popup>

  <yd-popup v-model="showGroup" position="center" width="6.5rem" height="3.4rem">
    <yd-cell-group title="请输入点评、美团或者口碑券号">
      <yd-cell-item>
        <yd-input slot="right" v-model="couponCode" placeholder="请输入券号" ref="couponCode"></yd-input>
      </yd-cell-item>

      <div class="yd-confirm-ft">
        <a href="javascript:;" class="yd-confirm-btn default" @click="showGroup = false">取消</a>
        <a href="javascript:;" class="yd-confirm-btn primary" @click="payOffline">确定</a></div>
    </yd-cell-group>
  </yd-popup>


  <div v-if="orders.length > 0">
    <div class="item" v-for="order in orders">
      <yd-preview :buttons="btns" :data-id="order.order_id" :data-no="order.order_no">
        <yd-preview-header>
          <div slot="left">付款金额</div>
          <div slot="right">¥{{ order.total_fee}}</div>
        </yd-preview-header>
        <yd-preview-item>
          <div slot="left">订单号</div>
          <div slot="right">{{ order.order_no}}</div>
        </yd-preview-item>
        <yd-preview-item>
          <div slot="left">项目</div>
          <div slot="right">{{ order.project_name}}</div>
        </yd-preview-item>
        <yd-preview-item>
          <div slot="left">技师</div>
          <div slot="right">{{ order.beautician_name}}</div>
        </yd-preview-item>
        <yd-preview-item>
          <div slot="left">预约日期</div>
          <div slot="right">{{order.appointment_day}}
            {{order.appointment_start_time}}~{{order.appointment_end_time|realDay(order.appointment_day)}}
          </div>
        </yd-preview-item>
      </yd-preview>
    </div>
  </div>
  <div v-else>
    <div class="notice">
      <img src="<?php echo $baseUrl; ?>/static/order.png" alt="" class="icon">
      <span>您没有需买单的订单哦</span>
    </div>
  </div>
</div>

<script>
  $(function () {
    var vm

    vm = new Vue({
      el: '#order',
      filters: {
        realDay: function (value, day) {
          try {
            var _day = day + ' ' + value
            _day = _day.replace(/-/g, '/')
            var date = (new Date(_day)).getTime() + 30 * 60 * 1000
            var date = (new Date(date))
            var minute = date.getMinutes()
            if (minute.toString().length == 1)
              minute += '0'
            return date.getHours() + ':' + minute + ':00'
          } catch (e) {
            return value
          }
        }
      },
      watch: {
        showCash: function (val, newValue) {
          if (newValue === false)
            this.beauticianCode = ''
        },

        showGroup: function (val, newValue) {
          if (newValue === false)
            this.couponCode = ''
        }
      },
      data: {
        orders: [],
        showCash: false,
        showGroup: false,
        showPay: false,
        beauticianCode: '',
        couponCode: '',
        payType: '',
        lastOrderId: '',
        lastOrderNo: '',
        btns: [
          {
            text: '店内现金',
            click: function () {
              vm.payType = 'cash'
              vm.showCash = true
              var preview = event.target.parentNode.parentNode
              vm.lastOrderId = preview.dataset.id

              vm.$refs.beauticianCode.$el.children[0].focus()
            }
          },
          {
            text: '店内扫码',
            click: function () {
              vm.payType = 'scan'
              var preview = event.target.parentNode.parentNode
              vm.lastOrderId = preview.dataset.id

              vm.$dialog.confirm({
                mes: '请确认您已经店内扫码完成买单！',
                opts: () => {
                  vm.payOffline()
                }
              });
            }
          },
          {
            text: '团购',
            click: function () {
              vm.payType = 'group'
              vm.showGroup = true
              var preview = event.target.parentNode.parentNode
              vm.lastOrderId = preview.dataset.id

              vm.$refs.couponCode.$el.children[0].focus()
            }
          },
          {
            text: '线上支付',
            click: function () {
              var preview = event.target.parentNode.parentNode
              vm.lastOrderNo = preview.dataset.no
              var amount = 0
              vm.orders.forEach(function (item) {
                if (item.order_no === vm.lastOrderNo)
                  amount = item.total_fee
              })


              vm.$dialog.confirm({
                mes: '确定支付 ¥' + amount,
                opts: () => {
                  vm.payOnline()
                }
              })
            }
          },
        ]
      },
      mounted: function () {
        this.getOrders()
      },
      methods: {
        payOffline: function () {
          var self = this

          if (this.payType === 'cash') {
            if (this.beauticianCode.trim().length === 0) {
              self.$dialog.toast({
                mes: '技师工号不能为空',
                timeout: 1500
              })
              return
            }
          }

          // 团购
          if (this.payType === 'group') {
            if (!this.couponCode.match(/^\d{10}$/) && !this.couponCode.match(/^\d{12}$/)) {
              self.$dialog.toast({
                mes: '券号输入错误,券号位数为10位或者12位',
                timeout: 1500
              })
              return
            }
          }

          var lastOrderId = this.lastOrderId
          this.$request.post('center/completeOrder/' + lastOrderId, {
            type: this.payType,
            beautician_code: this.beauticianCode,
            coupon_code: this.couponCode
          }).then(function () {

            self.$dialog.toast({
              mes: '买单成功。',
              timeout: 1500
            })

            self.cancel()

            setTimeout(function () {
              self.getOrders()
            }, 2000)

          })
            .catch(function (error) {
              self.$dialog.toast({
                mes: error.detail || '提交失败',
                timeout: 1500
              })
            })
        },
        payOnline: function () {
          var self = this
          if (!WeixinJSBridge || !WeixinJSBridge.invoke) {
            self.$dialog.toast({
              mes: '您的环境不支持微信支付，请在微信里打开',
              timeout: 1500
            })
            return
          }

          this.$request.get('order/pay/' + this.lastOrderNo)
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
                    self.cancel()
                    setTimeout(function () {
                      self.getOrders()
                    }, 2000)
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
        cancel: function () {
          vm.showCash = false
          vm.showGroup = false
          vm.showPay = false
          vm.lastOrderId = ''
          vm.lastOrderNo = ''
        },
        getOrders: function () {
          var self = this
          this.$request.get('center/order', {
            order_status: 1
          })
            .then(function (data) {
              self.orders = data.content
            })
            .catch(function (error) {
              self.$dialog.toast({
                mes: error.detail || '获取订单失败',
                timeout: 1500
              });
            })
        }
      }
    })
  })

</script>