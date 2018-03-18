<script src="<?php echo get_instance()->config->base_url(); ?>static/backend/js/projectProperty-newUser.js"></script>
<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">首页</a>
    <span class="crumb-step">&gt;</span>
    <a class="crumb-name" href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">项目管理</a>
    <span class="crumb-step">&gt;</span><span>修改项目</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <div class="error">
      <?php echo validation_errors(); ?>
    </div>

    <?php echo form_open_multipart(RequestUtil::CM(array('project_id' => $project['project_id'], 'limit' => $limit))); ?>
    <table class="insert-tab" width="100%">
      <tbody>

      <tr>
        <th><i class="require-red">*</i>项目标题：</th>
        <td>
          <input class="common-text required" name="project_name"
                 value="<?php echo $project['project_name']; ?>" size="50" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>店铺：</th>
        <td>
          <select name="shop_id" id="">
            <?php foreach ($shops as $key => $shop): ?>
              <?php $checked = ($key == $project['shop_id']) ? ' selected' : '' ?>
              <option value="<?php echo $key; ?>"<?php echo $checked; ?>><?php echo $shop; ?></option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>使用时间：</th>
        <td><input class="common-text" value="<?php echo $project['use_time']; ?>"
                   name="use_time" size="10" type="text"> 分钟
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>价格：</th>
        <td><input class="common-text" value="<?php echo $project['price']; ?>"
                   name="price" size="10" type="text"> 元
        </td>
      </tr>

      <tr>
        <th><i class="require-red">*</i>缩略图：</th>
        <td>
          <img class="project_cover"
               src="<?php echo UploadUtil::buildUploadDocPath($project['project_cover'], '200x200'); ?>">
          <br>
          <input name="pic" id="" type="file" class="common-text">

        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <input class="btn btn-primary btn6 mr10" value="提交" type="submit">
          <a class="btn btn6" href="<?php echo UrlUtil::createBackendUrl("project/index/{$limit}"); ?>">返回</a>
        </td>
      </tr>
      </tbody>
    </table>
    </form>
  </div>