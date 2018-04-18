<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i><a href="<?php echo UrlUtil::createBackendUrl('project/index'); ?>">首页</a><span
      class="crumb-step">&gt;</span><span class="crumb-name">管理技师与前台</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <?php if ($customers): ?>
      <table class="result-tab" width="100%">
        <tr>
          <th width="110">手机号</th>
          <th width="200">角色</th>
          <th width="">店铺名</th>
          <th width="140">操作</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
          <tr>
            <td><?php echo $customer['phone']; ?></td>
            <td><?php echo $customer['type'] == 2 ? '前台' : '技师'; ?></td>
            <td><?php echo $customer['shop_name']; ?></td>
            <td>
              <a class="link-del btn btn-warning"
                 href="<?php echo UrlUtil::createBackendUrl('unbind/delete/?customer_id=' . $customer['customer_id']); ?>">解绑</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="error">暂无记录</div>
    <?php endif; ?>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('.link-del').on('click', function (e) {
      e.preventDefault();

      if (confirm('确定解绑？')) {
        window.location.href = $(this).attr('href');
      }
    })
  })
</script>