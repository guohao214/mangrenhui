<style>
  #order {
    padding-bottom: 3rem;
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

  .yd-confirm-hd {
    text-align: center;
    font-size: .3rem;
  }

  .yd-confirm-bd {
    text-align: center;
  }

  div[v-if] {
    display: none;
  }
</style>
<div id="order">
  <div v-if="orders.length > 0">
    <div class="item" v-for="order in orders">
      <yd-preview :buttons="order.order_status == 1 ? btns : (order.order_status == 100) ? doneBtns : mbtns"
                  :data-id="order.order_id">
        <yd-preview-header>
          <div slot="left">付款金额</div>
          <div slot="right">¥{{ order.total_fee}}</div>
        </yd-preview-header>

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
      <span>您没有订单哦</span>
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
      data: {
        orders: [],
        doneBtns: [
          {
            text: '订单已完成',
          },
        ],
        mbtns: [
          {
            text: '订单已取消',
          },
        ],
        btns: [
          {
            text: '取消订单',
            color: '#F00',
            click: function () {
              var preview = event.target.parentNode.parentNode
              var id = preview.dataset.id
              var self = this
              if (!id) {
                vm.$dialog.toast({
                  mes: '订单取消失败',
                  timeout: 1500
                })
                return
              }

              vm.$dialog.confirm({
                mes: '确定取消此订单?',
                opts: () => {
                  vm.$request.get('center/cancelOrder/' + id)
                    .then(function (data) {
                      for (var i = 0; i < vm.orders.length; i++) {
                        if (vm.orders[i].order_id == id) {
                          vm.orders[i].order_status = 2
                          return
                        }
                      }
                    })
                    .catch(function (error) {
                      vm.$dialog.toast({
                        mes: error.detail || '订单取消失败',
                        timeout: 1500
                      })
                    })
                }
              });

            }
          },
        ]
      },
      mounted: function () {
        this.getOrders()
      },
      methods: {
        getOrders: function () {
          var self = this
          this.$request.get('center/order')
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