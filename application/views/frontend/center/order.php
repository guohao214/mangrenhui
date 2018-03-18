<style> #order .item {
    margin: .2rem 0
  }</style>
<div id="order">
  <div class="item" v-for="order in orders">
    <yd-preview :buttons="order.order_status == 1 ? btns : mbtns" :data-id="order.order_id">
      <yd-preview-header>
        <div slot="left">付款金额</div>
        <div slot="right">¥{{ order.total_fee}}</div>
      </yd-preview-header>

      <yd-preview-item>
        <div slot="left">项目</div>
        <div slot="right">{{ order.project_name}}({{order.use_time}}分钟)</div>
      </yd-preview-item>
      <yd-preview-item>
        <div slot="left">技师</div>
        <div slot="right">{{ order.beautician_name}}</div>
      </yd-preview-item>
      <yd-preview-item>
        <div slot="left">预约日期</div>
        <div slot="right">{{order.appointment_day}}
          {{order.appointment_start_time}}~{{order.appointment_end_time}}
        </div>
      </yd-preview-item>
    </yd-preview>
  </div>
</div>

<script>
  $(function () {
    var vm

    vm = new Vue({
      el: '#order',
      data: {
        orders: [],
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
              if (!id) {
                vm.$dialog.toast({
                  mes: '订单取消失败',
                  timeout: 1500
                })
                return
              }
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

            }.bind(this)
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