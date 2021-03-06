<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i><a
      href="<?php echo UrlUtil::createBackendUrl('project/index'); ?>">首页</a><span
      class="crumb-step">&gt;</span><span class="crumb-name">项目管理</span></div>
</div>
<div class="search-wrap">
  <div class="search-content">
    <form action="<?php echo UrlUtil::createBackendUrl('project/index'); ?>?" method="get">
      <table class="search-tab">
        <tr>
          <th width="70">项目标题:</th>
          <td><input class="common-text" placeholder="项目标题" type="text"
                     name="project_name" value="<?php echo defaultValue($params['project_name']); ?>"></td>
          <!--                    <td>-->
          <!--                        <input type="checkbox" value="1" name="on_index"--><?php //echo defaultValue($params['on_index'], 0)?' checked' : '';?>
          <!--                               data-url="-->
          <?php //echo UrlUtil::createBackendUrl("project/index/{$limit}/"); ?><!--">首页展示-->
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
      <a href="<?php echo UrlUtil::createBackendUrl('project/addProject') ?>">
        <i class="icon-font"></i>新增项目</a>
    </div>
  </div>
  <div class="result-content">
    <?php if ($projects): ?>
      <table class="result-tab" width="100%">
        <tr>
          <th width="110">封面</th>
          <th>项目标题</th>
<!--          <th>所属分类</th>-->
          <th width="100">使用时间</th>
          <th width="200">门店</th>
          <th width="100">价格</th>
          <th width="150">创建时间</th>
          <th width="140">操作</th>
        </tr>
        <?php foreach ($projects as $project): ?>
          <tr>
            <td>
              <img class="cover"
                   src="<?php echo UploadUtil::buildUploadDocPath($project['project_cover'], '200x200'); ?>">
            </td>
            <td><?php echo $project['project_name']; ?></td>
<!--            <td>--><?php //echo $categories[$project['category_id']]; ?><!--</td>-->
            <td><?php echo $project['use_time']; ?> 分钟</td>
            <td><?php echo $shops[$project['shop_id']]; ?> </td>
            <td><?php echo $project['price']; ?> 元</td>
            <td><?php echo $project['created_time']; ?></td>
            <td>
              <a class="link-update btn btn-warning"
                 href="<?php echo UrlUtil::createBackendUrl('project/updateProject/' . $project['project_id'] . "/{$limit}"); ?>">修改</a>
              <a class="link-del btn btn-danger"
                 href="<?php echo UrlUtil::createBackendUrl('project/deleteProject/' . $project['project_id'] . "/{$limit}"); ?>">删除</a>

              <!--                            <p><a class="link-recommend btn -->
              <?php //echo $project['on_index'] ? 'btn-success' : 'btn-primary'; ?><!--"-->
              <!--                               href="-->
              <?php //echo UrlUtil::createBackendUrl("project/onIndex/{$project[project_id]}/{$limit}/{$project[on_index]}"); ?><!--">-->
              <!--                                --><?php //echo $project['on_index'] ? '不显示在首页' : '首页显示'; ?>
              <!--                            </a></p>-->
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
    $('.link-del').on('click', function (e) {
      e.preventDefault();

      if (confirm('确定删除此项目？')) {
        window.location.href = $(this).attr('href');
      }
    })

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