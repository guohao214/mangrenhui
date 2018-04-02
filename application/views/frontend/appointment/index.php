<?php $this->load->view('frontend/header'); ?>
<style>
  #appointment {
    background-color: #F7F7F7;
    position: relative;
  }

  img {
    width: 100%;
    height: 100%;
    display: block;
    border-radius: 50%;
  }

  .group {
    min-height: 2.1rem;
    height: auto;
    background-color: white;
    margin-bottom: .2rem;
  }

  .group .head {
    font-size: 0.3rem;
    color: #ffb400;
    padding: 0.2rem 0 0.1rem .3rem;
    display: flex;
    border-bottom: 1px solid #EDEDED;
  }

  .group .head i {
    margin: 0 .1rem 0 0;
  }

  .group .body {
    overflow-x: scroll;
    width: 100%;
  }

  .group .body .scroll {
    position: relative;
    z-index: 1;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    width: 100%;
    -webkit-transform: translateZ(0);
    -moz-transform: translateZ(0);
    -ms-transform: translateZ(0);
    -o-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-text-size-adjust: none;
    -moz-text-size-adjust: none;
    -ms-text-size-adjust: none;
    -o-text-size-adjust: none;
    text-size-adjust: none;
    background-color: white;
  }

  .group .body .scroll ul {
    list-style: none;
    padding: 0;
    margin: 0;
    width: 100%;
    height: 1rem;
    text-align: center;
  }

  .group .body .scroll.days {
    width: 16rem;
    -webkit-overflow-scrolling: touch;
  }

  .group .body .scroll ul.days {
    height: 1rem;
  }

  .group .body .scroll.worker {
    /*width: 20rem;*/
  }

  .group .body .scroll ul.worker {
    height: 2.5rem;
    display: flex;
    align-items: center;
  }

  .group .body .scroll ul.worker li {
    width: 2rem;
    height: 2rem;
    display: flex;
    margin: 0rem;
    float: left;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;
    padding: 0.1rem 0;
  }

  .group .body .scroll ul.worker li.active {
    color: #ff6600;
    font-weight: bold;
  }

  .group .body .scroll ul.worker li.active img {
    border: 1px solid #ff6600;
  }

  .group .body .scroll ul.worker li img {
    display: block;
    width: 1.2rem;
    height: 1.2rem;
    /*background-color: rebeccapurple;*/
    border-radius: 50%;
  }

  .group .body .scroll ul.worker li span {
    display: inline-block;
    font-size: 0.23rem;
  }

  .group .body .scroll li {
    width: 2rem;
    height: 2rem;
    display: block;
    margin: 0.1rem;
    float: left;
  }

  .group .body .scroll ul.days li {
    width: 1rem;
    height: 1.2rem;
    display: flex;
    margin: 0rem;
    /*float: left;*/
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-top: .1rem;
    /*padding: 0.2rem 0;*/
  }

  .group .body .scroll ul.days li.active {
    color: #ff6600;
    font-weight: bold;
    font-size: .3rem;
  }

  .group .body .scroll ul.days li span {
    display: inline-block;
  }

  .group .body .scroll ul.days li span:last-child {
    transform: scale(0.8);
  }

  .time-select {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    padding: 0.1rem;
    border-top: 2px solid #EDEDED;
  }

  .time-select span {
    display: inline-block;
    padding: 0.3rem 0.2rem;
    font-size: 0.28rem;
    flex-basis: 1.151rem;
  }

  .group .body .shop {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .3rem;
    height: 1.8rem;
  }

  .group .body .shop .shop-pic {
    flex-basis: 1.2rem;
    height: 1rem;
    /*background-color: rebeccapurple;*/
  }

  .group .body .shop .shop-info {
    flex-grow: 1;
    padding: 0 0.2rem;
  }

  .group .body .shop .shop-info .address {
    font-size: 0.23rem;
  }

  .group .body .shop .check {
    width: 0.5rem;
    height: 0.5rem;
  }

  .checked {
    color: #ff6600;
    font-weight: bold;
  }

  .invalid {
    text-decoration: line-through;
    color: #c3c3c3;
  }

  #footer {
    /*position: fixed;*/
    /*margin-bottom: .3rem;*/
    /*left: 0;*/
    width: 100%;
    z-index: 999;
    padding-bottom: 1.5rem;
  }

  div[v-show] {
    display: none;
  }

  .yd-cell-item {
    padding: .2rem .1rem;
  }
</style>
<div class="appointment" id="appointment">
  <div class="group" id="shop">
    <div class="head">
      <yd-icon name="location" size="0.4rem"></yd-icon>
      选择店铺
    </div>
    <div class="body">
      <div class="shop" v-show="items.length">
        <div class="shop-pic">
          <img :src="currentItem.shop_logo" alt="">
        </div>
        <div class="shop-info">
          <div class="shop-name"> {{ currentItem.shop_name }}</div>
          <div class="address">地址:&ensp;{{ currentItem.address }}</div>
        </div>
        <div class="check yd-cell-arrow" @click="showShopList=true"></div>
      </div>
    </div>

    <yd-popup v-model="showShopList" position="right" width="90%">
      <yd-search v-model="searchKeywords" :on-submit="submitSearch" :on-cancel="cancelSearch"></yd-search>
      <yd-cell-group>
        <yd-cell-item v-for="(w, k) in cpItems" @click.native="choose(w)">
          <span slot="left">
            <div style="font-size: .3rem"> {{ w.shop_name }}</div>
            <div style="font-size: .25rem;"> {{ w.address }} ({{w.distance}})</div>
          </span>
        </yd-cell-item>
      </yd-cell-group>

    </yd-popup>
  </div>


  <div class="group" id="project">
    <div class="head">
      <yd-icon name="discover" size="0.4rem"></yd-icon>
      选择项目
    </div>
    <div class="body">
      <div class="scroll worker">
        <ul class="worker">
          <template v-show="items.length">
            <li v-for="(w,k) in items" @click="choose(k)"
                :class="{active: w.project_id === currentItem.project_id}">
              <img :src="w.project_cover" alt="">
              <span> {{ w.project_name }}</span>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>

  <div class="group" id="worker">
    <div class="head">
      <yd-icon name="footmark" size="0.4rem"></yd-icon>
      选择技师
    </div>
    <div class="body">
      <div class="scroll worker">
        <ul class="worker">
          <template v-show="items.length">
            <li v-for="(w, k) in items" @click="choose(k)"
                :class="{active: w.beautician_id === currentItem.beautician_id}">
              <img :src="w.avatar" alt="">
              <span> {{ w.name }}</span>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>

  <div class="group" id="appointment-days">
    <div class="head">
      <yd-icon name="clock" size="0.4rem"></yd-icon>
      预约时间
    </div>
    <div class="body">
      <div class="scroll days">
        <ul class="days">
          <li v-for="d in days" @click="fetchTime(d)" :class="{active: d.match(new RegExp(appointment_day)) }">
            <span>{{ d | getDay }}</span>
            <span>{{ d | getWeek }}</span>
          </li>
        </ul>
      </div>

    </div>
    <div class="time-select">
      <span v-for="(t, k) in times"
            @click="check(k)"
            :class="{ checked: t.checked, invalid: !t.valid}">{{ t.time || '' }}</span>
    </div>
  </div>


  <div class="footer" id="footer">
    <yd-button-group>
      <yd-button size="large" type="warning" @click.native="appointment">发送预约</yd-button>
    </yd-button-group>
  </div>

</div>


<script>
  window.onerror = function (err) {
    console.log(err)
  }
  $(function () {
    var Bus = new Vue
    var shop, worker, project, appointmentDay;
    Bus.$on('init', function (shopId) {
      Bus.$request.get('appointment/getBeauticianAndProject/' + shopId)
        .then(function (data) {
          var content = data.content
          worker.items = content['beauticians']
          project.items = content['projects']
          appointmentDay.days = content['days']
        })
        .catch(function (error) {
          worker.$dialog.toast({
            mes: error.detail || '获取数据失败',
            timeout: 1500
          });
        })
    })


    // 获得物理位置信息
    Bus.$request.get('appointment/getJsTicket', {url: encodeURIComponent(location.href)}).then(function (data) {
      var config = $.extend({
        debug: false,
        jsApiList: ['getLocation']
      }, data.content)

      wx.config(config);

      wx.error(function (res) {
        console.log(res)
      })

      wx.ready(function () {
        wx.getLocation({
          type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
          success: function (res) {
            var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            var speed = res.speed; // 速度，以米/每秒计
            var accuracy = res.accuracy; // 位置精度

            shop.getShop(latitude, longitude)
          }
        });
      })
    })
      .catch(function (error) {
        Bus.$dialog.toast({
          mes: error.detail || '获取微信参数失败',
          timeout: 1500
        });
      })

    shop = new Vue({
      el: '#shop',
      data: {
        currentItem: {},
        shop_id: '',
        items: [],
        showShopList: false,
        searchKeywords: '',
        cpItems: []
      },
      watch: {
        items: function (value) {
          this.currentItem = value[0]
          this.cpItems = value
          this.searchKeywords = ''
        },
        searchKeywords: function (value) {
          this.cpItems = this.items.filter(function (item) {
            return item.shop_name.match(new RegExp(value))
          })
        },
        currentItem: function (newValue, oldValue) {
          if (newValue['shop_id'] === oldValue['shop_id'])
            return

          this.shop_id = newValue['shop_id']
          Bus.$emit('init', this.shop_id)
        }
      },
      mounted: function () {
      },
      methods: {
        choose: function (item) {
          this.currentItem = item
          this.showShopList = false
          project.choose(0)
        },
        submitSearch: function () {

        },
        cancelSearch: function () {
          this.searchKeywords = ''
        },
        getShop: function (latitude, longitude) {
          var self = this
          this.$request.get('shop/getList', {latitude: latitude, longitude: longitude}).then(function (data) {
            self.items = data.content
          })
            .catch(function (error) {
              self.$dialog.toast({
                mes: error.detail || '获取店铺失败',
                timeout: 1500
              });
            })
        }
      }
    })

    project = new Vue({
      el: '#project',
      data: {
        currentItem: '',
        items: []
      },
      watch: {
        items: function (value) {
          this.currentItem = value[0]
        }
      },
      methods: {
        choose: function (index) {
          this.currentItem = this.items[index] || []
          appointmentDay.appointment_time = []
          appointmentDay.times.forEach(function (item) {
            item.checked = false
          })
        }
      }
    })

    worker = new Vue({
      el: '#worker',
      data: {
        currentItem: [],
        items: []
      },
      watch: {
        items: function (value) {
          this.currentItem = value[0]
        },
        currentItem: function (value) {
          appointmentDay.fetch()
        }
      },
      methods: {
        choose: function (index) {
          this.currentItem = this.items[index]
        }
      }
    })


    appointmentDay = new Vue({
      el: '#appointment-days',
      data: {
        appointment_day: '',
        appointment_time: [],
        every: 30,
        days: [],
        times: [],
      },
      filters: {
        getDay: function (value) {
          return value.split('#')[0]
        },
        getWeek: function (value) {
          return value.split('#')[1]
        }
      },
      watch: {
        days: function (newValue) {
          var day = newValue[0].split('#')
          this.appointment_day = day[2]
        },
        appointment_day: function (value) {
          this.fetch()
        }
      },
      methods: {
        fetch: function () {
          var self = this

          var beautician_id = (worker.currentItem && typeof worker.currentItem['beautician_id'] !== 'undefined')
            ? worker.currentItem['beautician_id'] : ''
          if (!beautician_id || !self.appointment_day)
            return

          this.$request.get('appointment/getAppointmentTime/' + beautician_id + '/' + self.appointment_day)
            .then(function (data) {
              for (var i in [0, 0, 0, 0])
                data.content.push({})

              self.times = data.content
            })
            .catch(function (error) {
              self.$dialog.toast({
                mes: error.detail || '获取时间数据失败',
                timeout: 1500
              });
            })
        },
        fetchTime: function (day) {
          var day = day.split('#')
          this.appointment_day = day[2]
        },
        check: function (index) {
          var time = this.times[index]
          if (!time.valid) {
            return
          }
          this.appointment_time = []

          // 所有的数据清空
          for (var i = 0; i < this.times.length - 4; i++) {
            this.$set(this.times[i], 'checked', false)
          }

          this.$nextTick(checkedIt.apply(this))

          function checkedIt() {
            var t = project.currentItem['use_time'] // 30
            var length = this.times.length
            //选择最后一个
            if (index === length - 5) {
              if (this.every < t) {
                this.$dialog.toast({
                  mes: '预约时间不足',
                  timeout: 1500
                });
                return
              }
            }

            // 选择个数
            var num = Math.ceil(t / this.every)
            var k = 0
            for (var i = 0; i < num; i++) {
              var time = this.times[index + i]
              if (time.time && time.valid) {
                time.checked = true
                k++
                this.appointment_time.push(time)
              }
            }

            if (k !== num) {
              for (var i = 0; i < this.appointment_time.length; i++) {
                this.$set(this.appointment_time[i], 'checked', false)
              }

              this.$dialog.toast({
                mes: '预约时间不足',
                timeout: 1500
              });
            }

          }
        }
      }
    })


    var footer = new Vue({
      el: '#footer',
      data: {},
      methods: {
        appointment: function () {
          var self = this
          var times = []

          times = appointmentDay.appointment_time.map(function (item) {
            return item.time
          })

          if (!times.length) {
            this.$dialog.toast({
              mes: '请选择预约时间',
              timeout: 1500
            });

            return
          }

          var data = {
            shop_id: shop.shop_id,
            beautician_id: worker.currentItem['beautician_id'],
            project_id: project.currentItem['project_id'],
            appointment_day: appointmentDay.appointment_day,
            appointment_time: times.join(',')
          }
          this.$request.post('cart/appointment', data)
            .then(function () {
              self.$dialog.toast({
                mes: '预约成功',
                timeout: 1500
              });

              setTimeout(function () {
                window.location.href = document_root + 'center/order'
              }, 1500)
            })
            .catch(function (error) {
              self.$dialog.toast({
                mes: error.detail || '预约失败，请重试',
                timeout: 1500
              });
            })
        },
      }
    })
  })

</script>