<?php $this->load->view('frontend/header'); ?>
<style>
  body {
    background-color: rgb(241,241,241);
  }

  .top-header {
    display:none;
  }

  #app_body {

  }
  #app_body .cover img {
    display:block;
    width: 100%
  }

  .groupon_project {
    padding: .2rem;
    /* border-bottom: 1px dashed rgb(223,223,223); */
    background-color: white;
  }

  .groupon_project .project_name {
    font-size: .35rem;
  }

  .groupon_project .project_info {
    display: flex;
    font-size: .3rem;
    padding: .2rem 0;
  }

  .groupon_project .project_info .price {
    color: red;
    padding-right: .3rem;
  }

  .groupon_project .project_info .groupon_price {
    font-size: .4rem;
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

  .groupon_comment {
    margin-top:.3rem
  }

  .yd-cell-title {
    color: black;
      font-weight: bold;
      background: white;
      padding: .2rem;
  }

  .yd-badge-danger {
      background-color: #ef4f4f;
      color: #fff;
      padding: .1rem .25rem;
      color: #ef4f4f;
      background: none;
      border: 1px solid #ef4f4f;
  }

  #footer {
    position: fixed;
    /*margin-bottom: .3rem;*/
    /*left: 0;*/
    width: 100%;
    z-index: 999;
    bottom: .1rem;
  }

  .shop {
    padding-bottom: 1.5rem
  }

  .use_shop {
    display: flex;
    align-items: center;
  }

  .use_shop div {
    padding: .1rem
  }

  div[v-show] {
    display: none;
  }

  .f_2 {
    font-size: .2rem
  }

  .groupon_comment .yd-cell-left {
    white-space: pre-wrap;
    padding: 2px 0;
  }
  .yd-cell-item {
    /* padding: .2rem .1rem; */
  }
</style>

<div id="app_body" v-show="project.groupon_project_code">
  <div class="cover">
    <img :src="project.project_cover" alt="">
  </div>

  <div class="groupon_project">
    <div class="project_name">{{ project.groupon_name }}</div>
    <div class="project_info">
      <div class="price">
        <div class="groupon_price">¥ {{ project.groupon_price }}</div>
        <div class="old_price">¥ {{ project.old_price }}</div>
      </div>
      <div class="people">
        <yd-badge shape="square" type="danger">{{ project.in_peoples }}人团</yd-badge>
      </div>
    </div>
  </div>

  <div class="groupon_comment">
    <yd-cell-group title="拼团介绍">
      <yd-cell-item>
        <span slot="left">{{ project.comment }}</span>
      </yd-cell-item>
    </yd-cell-group>
  </div>

  <div class="groupons" v-show="ingProjects.length">
  <yd-cell-group title="进行中的团">
    <yd-cell-item  v-for="item in ingProjects">
      <span slot="left">
        <div class="groupon">
          <div class="avatar">
            <img :src="item.avatar" alt="">
          </div>
          <div class="info">
            <div class="user">
              大师我
            </div>
            <div class="remain_people">还差 {{ item.in_peoples - (item.in_counts || 0)}}人</div>
          </div>
          <div class="peoples">
            <yd-badge shape="square" type="danger">{{item.in_peoples}}人团</yd-badge>
          </div>
        </div>
      </span>
      <span slot="right">
        <yd-button type="danger" @click.native="join(item.groupon_order_code)">参团</yd-button>
      </span>
    </yd-cell-item>
    </yd-cell-group>
  
  </div>

  <div class="time-range">
    <yd-cell-group title="活动有效期">
      <yd-cell-item>
        <span slot="left">{{ project.start_time }} - {{ project.end_time }}</span>
      </yd-cell-item>
    </yd-cell-group>
  </div>

  <div class="project">
    <yd-cell-group title="活动内容">
      <yd-cell-item>
        <span slot="left">{{ project.project_name}} x{{ project.in_counts}}</span>
      </yd-cell-item>
    </yd-cell-group>
  </div> 

  <div class="shop">
    <yd-cell-group title="适用门店">
      <yd-cell-item>
        <span slot="left">
          <div class="use_shop">
            <div>
              <div>{{ project.shop_name}}</div>
              <div class="f_2">{{ project.address}}</div>
            </div>
            <div>
              <a href="tel:{{project.contact_number}}">
                <yd-icon name="phone2" color="red"></yd-icon>
              </a>
              
            </div>
          </div>
        </span>
      </yd-cell-item>
    </yd-cell-group>
  </div>

  <div class="footer" id="footer">
    <yd-button-group>

      <yd-button size="large" type="warning" @click.native="commit" v-if="project.__type==='ing'">我要开团 ¥{{ project.groupon_price}}</yd-button>
      <yd-button size="large"  disabled type="warning" @click.native="commit" v-else-if="project.__type==='wait'">未开始 ¥{{ project.groupon_price}}</yd-button>
      <yd-button size="large" disabled type="warning" @click.native="commit" v-else="project.__type==='end'">已结束 ¥{{ project.groupon_price}}</yd-button>
    </yd-button-group>
  </div>
</div>
</body>

<script>
  var grouponProjectCode = '<?php echo $groupon_project_code; ?>'
  var grouponOrderCode = '<?php echo $groupon_order_code; ?>'

  $(document).ready(function() {
    new Vue({
      el: '#app_body',
      data: {
        project: {},
        ingProjects: []
      },
      mounted: function() {
        this.getIngOrders(grouponProjectCode, grouponOrderCode)
        this.getProject(grouponProjectCode)
      },
      methods: {
        getIngOrders: function (grouponProjectCode, grouponOrderCode) {
          var self = this

          this.$request.get('groupon/getGrouponIngOrders/' + grouponProjectCode + '/' + grouponOrderCode)
            .then(function(data) {
              self.ingProjects = data.content
            })
        },
        getProject: function(getGrouponProject) {
          var self = this
          this.$request.get('groupon/getGrouponProject/' + grouponProjectCode)
            .then(function(data) {
              self.project = data.content
            })
            .catch(function (err) {
              self.$dialog.toast({
                mes: err.detail || '开团失败，请重试.',
                timeout: 1500
              });
            })
        },
        commit: function() {
          var self = this
          this.$request.get('groupon/newGrouponOrder/' + grouponProjectCode)
            .then(function(data) {
              var orderNo = data.content[0]
              // debugger
              window.location.href = '/groupon/pay/' + orderNo
            })
            .catch(function (err) {
                self.$dialog.toast({
                  mes: err.detail || '开团失败，请重试.',
                  timeout: 1500
                });
              })
        },
        join: function(grouponOrderCode) {
          var self = this
          this.$request.get('groupon/join/' + grouponOrderCode)
            .then(function(data) {
              
            })
            .catch(function (err) {
                self.$dialog.toast({
                  mes: err.detail || '参团失败，请重试.',
                  timeout: 1500
                });
              })
        }
      }
    })
  })
</script>


</html>