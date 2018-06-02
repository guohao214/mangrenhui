<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i><a
      href="<?php echo UrlUtil::createBackendUrl('project/index'); ?>">首页</a><span
      class="crumb-step">&gt;</span><span class="crumb-name">拼团项目管理</span></div>
</div>
<div class="search-wrap">
  <div class="search-content">
    <form action="<?php echo UrlUtil::createBackendUrl('grouponProject/index'); ?>?" method="get">
      <table class="search-tab">
        <tr>
          <!-- <th width="70">项目标题:</th>
          <td><input class="common-text" placeholder="项目标题" type="text"
                     name="project_name" value="<?php echo defaultValue($params['project_name']); ?>"></td> -->

          <th width="70">拼团状态:</th>
          <td>
            <select name="type" class="select">
              <option
                value="ing"<?php echo ($params['type'] == 'ing') ? 'selected' : ''; ?>>
                进行中
              </option>
              <option
                value="wait"<?php echo ($params['type'] == 'wait') ? 'selected' : ''; ?>>
                未开始
              </option>
              <option
                value="end"<?php echo ($params['type'] == 'end') ? 'selected' : ''; ?>>
                已结束
              </option>
            </select>
          </td>
          <!--                    </td>-->
          <td><input class="btn btn-primary btn2" type="submit"></td>

        </tr>
      </table>
    </form>
  </div>
</div>
<div class="result-wrap">
  <div class="result-title">
    <div class="result-list">
      <a href="<?php echo UrlUtil::createBackendUrl('grouponProject/addProject') ?>">
        <i class="icon-font"></i>新增</a>
    </div>
  </div>
  <div class="result-content">
    <?php if ($projects): ?>
      <table class="result-tab" width="100%">
        <tr>
          <!-- <th width="110">封面</th> -->
          <th>项目</th>
          <th>门店</th>
          <th width="60">开团数量</th>
          <th width="60">已付款</th>
          <th width="60">已开团</th>
          <th width="60">未开团</th>
          <th width="100">拼团收入</th>
          <th width="200">拼团时间</th>
          <th width="140">操作</th>
        </tr>
        <?php foreach ($projects as $project): ?>
          <tr>
            <!-- <td>
              <img class="cover"
                   src="<?php echo UploadUtil::buildUploadDocPath($project['project_cover'], '200x200'); ?>">
            </td> -->
            <td>
              <?php echo $project['project_name']; ?>
              <div style="color: red;"><?php echo $project['in_peoples']; ?>人团, 拼团金额：¥<?php echo $project['groupon_price']?></div>
            </td>
            <td>
              <?php echo $project['shop_name']; ?>
            </td>
            <td><?php echo $project['groupon_count']; ?> </td>
            <td><?php echo $project['pay_counts']; ?> </td>
            <td><?php echo $project['open_counts']; ?> </td>
            <td><?php echo $project['groupon_count'] - $project['open_counts']; ?></td>
            <td>¥<?php echo number_format($project['open_counts'] * $project['groupon_price'], 2); ?></td>
            <td><?php echo $project['start_time'] ?>~ <?php echo $project['end_time']; ?></td>
            <td>
            <a class="link-update btn btn-success"
                 href="<?php echo UrlUtil::createBackendUrl('grouponProject/detail/' . $project['groupon_project_code'] . "/{$limit}"); ?>">详情</a>
              <a class="link-update btn btn-warning"
                 href="<?php echo UrlUtil::createBackendUrl('grouponProject/updateProject/' . $project['groupon_project_code'] . "/{$limit}"); ?>">修改</a>
              <a class="link-del btn btn-default"
                 target="_blank"
                 href="<?php echo UrlUtil::createBackendUrl('grouponProject/share/' . $project['groupon_project_code']); ?>">分享二维码</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <div class="list-page"><?php echo $pages; ?></div>
    <?php else: ?>
      <div class="error">暂无项目</div>
    <?php endif; ?>
  </div>
</div>

<script>
  $(document).ready(function () {
    // $('.link-del').on('click', function (e) {
    //   e.preventDefault();

    //   if (confirm('确定删除此项目？')) {
    //     window.location.href = $(this).attr('href');
    //   }
    // })

//        $('input[name="onIndex"]').on('click', function () {
//            var that = $(this),
//                onIndex = 0;
//            if (that.prop('checked') == true) {
//                onIndex = 1;
//            }
//
//            window.location.href = that.attr('data-url') + onIndex;
//        })
  })
</script>