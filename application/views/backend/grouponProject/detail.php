<style>
  .order {
    margin:10px;
  }
  .order div {
    margin: 0 10px;
  }
  .order {
    display: flex;
  }
  .avatar {
    width: 70px;
    height: 70px;
  }
  .avatar img {
    width:100%;
    display:block;
  }
  .is_first {
    color: red;
    font-weight: bold;
  }

  .list {
    display:none;
  }

  .title {
    margin-left:10px;
  }

  .view {
    color: cornflowerblue;
    text-decoration: underline;
    cursor: pointer
  }
</style>
<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i><a
      href="<?php echo UrlUtil::createBackendUrl('grouponProject/index'); ?>">拼团</a><span
      class="crumb-step">&gt;</span><span class="crumb-name">拼团记录</span></div>
</div>

<div class="result-wrap">
  <div class="result-content">
    <?php if ($orders): ?>
      <table class="result-tab" width="100%">
        <?php foreach ($orders as $key=> $order): ?>
          <tr>
          <td style="text-align: center">
            <?php echo $key + 1; ?>
          </td>
           <td>
            <div class="order">
              <div class="avatar">
                <img src="<?php echo $order['avatar'] ?>" alt="">
              </div>

              <div class="info">
                <div class="nick_name"><?php echo $order['nick_name']; ?> - <?php echo $order['phone_number']; ?></div>
                <div><span><?php echo $order['in_peoples']; ?>人团 | 已参团<?php echo $order['pay_counts']; ?>人</span></div>
              </div>

              <span class="is_first">团长</span>
              <div class = "view" data-order="<?php echo $order['groupon_order_id'] ?>" data-open="0"> 查看 </div>
            </div>

            <div class="list">
             <div class="title"> ----- 拼团订单 -----</div>
             <div class="item"></div>
            </div>
           </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="error">暂无订单</div>
    <?php endif; ?>
  </div>
</div>

<script>
  $(document).ready(function () {
    $(document).ready(function() {
      $('.view').on('click', function() {
        var that = $(this)
        if (that.data('open') == 1) {
          that.parent().parent().find('.list').fadeOut()
          that.data('open', 0)
          return
        }

        that.data('open', 1)
        
        var $orderCode = that.data('order')
        
        $.ajax({
            url: '/backend/grouponProject/getAllOrdersByGrouponOrderId/' + $orderCode,
            dataType: 'html',
            beforeSend: function () {
                //$project.children('option').not(':first').remove();
            },
            success: function (data) {
              that.parent().parent().find('.list').fadeIn().find('.item').html(data)
            },
            error: function () {
                alert('获取列表失败！');
            }
        })
      })
    })
  })
</script>