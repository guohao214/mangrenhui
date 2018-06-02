<script src="<?php echo get_instance()->config->base_url(); ?>static/backend/js/getProject.js"></script>
<link rel="stylesheet" type="text/css"
      href="<?php echo get_instance()->config->base_url(); ?>static/backend/css/jquery.datetimepicker.css"/>
<script src="<?php echo get_instance()->config->base_url(); ?>static/backend/js/jquery.datetimepicker.js"></script>
<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('grouponProject/index'); ?>">首页</a>
    <span class="crumb-step">&gt;</span>
    <a class="crumb-name" href="<?php echo UrlUtil::createBackendUrl('grouponProject/index') ?>">拼团管理</a>
    <span class="crumb-step">&gt;</span><span>新增</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <div class="error">
      <?php echo validation_errors(); ?>
    </div>

    <?php echo form_open_multipart(RequestUtil::CM()); ?>
    <table class="insert-tab" width="100%">
      <tbody>
      <tr>
        <th><i class="require-red">*</i>标题：</th>
        <td>
          <input class="common-text required" name="groupon_name"
                 value="<?php echo set_value('groupon_name'); ?>" size="50" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>店铺：</th>
        <td>
          <select name="shop_id" id="shop_id">
          <?php foreach ($shops as $key => $shop): ?>
            <option value="<?php echo $key; ?>"><?php echo $shop; ?></option>
          <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>项目</th>
        <td>
          <select name="project_id" id="project_id">
          <!-- <?php foreach ($shops as $key => $shop): ?>
            <option value="<?php echo $key; ?>"><?php echo $shop; ?></option>
          <?php endforeach; ?> -->
          </select>
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>团长总数：</th>
        <td><input class="common-text" value="<?php echo set_value('groupon_count'); ?>"
                   name="groupon_count" size="10" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>包含数量：</th>
        <td><input class="common-text" value="<?php echo set_value('in_counts'); ?>"
                   name="in_counts" size="10" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>团员总数：</th>
        <td><input class="common-text" value="<?php echo set_value('in_peoples'); ?>"
                   name="in_peoples" size="10" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>参团价格：</th>
        <td><input class="common-text" value="<?php echo set_value('groupon_price'); ?>"
                   name="groupon_price" size="10" type="text"> 元
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>市场价格：</th>
        <td><input class="common-text" value="<?php echo set_value('old_price'); ?>"
                   name="old_price" size="10" type="text"> 元
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>拼团时间：</th>
        <td><input class="common-text date" value="<?php echo set_value('start_time'); ?>"
                    name="start_time" size="15" type="text"> - 
                   <input class="common-text date" value="<?php echo set_value('end_time'); ?>"
                    name="end_time" size="15" type="text">
        </td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>宣传图：</th>
        <td><input name="pic" id="" type="file" class="common-text"></td>
      </tr>
      <tr>
        <th><i class="require-red">*</i>介绍：</th>
        <td>
          <textarea name="comment" cols="50" rows="5"> <?php echo set_value('comment'); ?></textarea>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <input class="btn btn-primary btn6 mr10" value="提交" type="submit">
          <a class="btn btn6" href="<?php echo UrlUtil::createBackendUrl('project/index'); ?>">返回</a>
        </td>
      </tr>
      </tbody>
    </table>
    </form>
  </div>


  <script>
    $(document).ready(function () {
      $('.date').datetimepicker({
      lang: 'ch',
      timepicker: false,
      format: 'Y-m-d',
      formatDate: 'Y-m-d',
      allowBlank: true
    });
      
    })
  </script>