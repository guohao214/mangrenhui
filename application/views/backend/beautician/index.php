<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index'); ?>">首页</a><span
      class="crumb-step">&gt;</span><span class="crumb-name">技师管理</span></div>
</div>
<div class="search-wrap">
  <div class="search-content">
    <form action="<?php echo UrlUtil::createBackendUrl('beautician/index'); ?>?" method="get">
      <table class="search-tab">
        <tr>
          <th width="70">姓名:</th>
          <td><input class="common-text" placeholder="姓名" type="text"
                     name="name" value="<?php echo defaultValue($params['name']); ?>"></td>

          <td><input class="btn btn-primary btn2" type="submit"></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div class="result-wrap">
  <div class="result-title">
    <div class="result-list">
      <a href="<?php echo UrlUtil::createBackendUrl('beautician/addBeautician') ?>">
        <i class="icon-font"></i>新增技师</a>
      <!--<a id="batchDel" href="javascript:void(0)"><i class="icon-font"></i>批量删除</a>
      <a id="updateOrd" href="javascript:void(0)"><i class="icon-font"></i>更新排序</a>-->
    </div>
  </div>
  <?php if ($beauticians): ?>
    <div class="result-content">
      <table class="result-tab" width="100%">
        <tr>
          <th width="10%">ID</th>
          <th width=110>头像</th>
          <th width="200">姓名</th>
          <th width="20%">所属门店</th>
          <th width=100>接单数量</th>
          <th width=100>性别</th>
          <th width="140">工作时间</th>
          <th width="150">操作</th>
        </tr>

        <?php foreach ($beauticians as $beautician): ?>
          <?php $beautician_id = $beautician['beautician_id']; ?>
          <tr>
            <td><?php echo $beautician_id; ?></td>
            <td><img src="<?php echo UploadUtil::buildUploadDocPath($beautician['avatar'], '100x100'); ?>">
            </td>
            <td><?php echo $beautician['name']; ?></td>
            <td><?php echo $shops[$beautician['shop_id']]; ?></td>
            <td>
              <a
                href="<?php echo UrlUtil::createBackendUrl('order/index?order_status=100&beautician_id=' . $beautician_id); ?>">
                <?php echo $beauticianOrderCounts[$beautician_id] ? $beauticianOrderCounts[$beautician_id] : 0; ?>
              </a>
            </td>
            <td><?php echo $beautician['sex']; ?></td>
            <td><?php
              $beauticianWorkTime = $workTime[$beautician_id];
              foreach ($beauticianWorkTime as $key => $time) {
                echo DateUtil::inWeekName($key) . '：' . $timeSetting[$time] . '，';
              }

              ?>

            </td>
            <td>
              <a class="link-update btn btn-warning"
                 href="<?php echo UrlUtil::createBackendUrl("beautician/updateBeautician/{$beautician_id}"); ?>">修改</a>
              <a class="link-del btn btn-danger"
                 href="<?php echo UrlUtil::createBackendUrl("beautician/deleteBeautician/{$beautician_id}"); ?>">删除</a>
              <a class="link-update btn btn-info"
                 href="<?php echo UrlUtil::createBackendUrl("beautician/rest?beautician_id={$beautician_id}"); ?>">请假记录</a>

              <a class="link-update btn btn-primary"
                 href="<?php echo UrlUtil::createBackendUrl("beautician/updateBeauticianWorkTime/{$beautician_id}"); ?>">工作时间</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  <?php else: ?>
    <div class="error">请添加技师</div>
  <?php endif; ?>
</div>

<script>
  $(document).ready(function () {
    $('.link-del').on('click', function (e) {
      e.preventDefault();

      var $that = $(this);

      if (confirm('确定删除技师: ' + $that.parents('tr').children('td').eq(2).html())) {
        window.location.href = $that.attr('href');
      }
    })
  })
</script>