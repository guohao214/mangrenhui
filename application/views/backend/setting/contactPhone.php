<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">首页</a>
    <span class="crumb-step">&gt;</span><span>联系方式</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <div class="error">
      <?php echo validation_errors(); ?>
    </div>

    <?php echo form_open(RequestUtil::CM()); ?>
    <table class="insert-tab" width="100%">
      <tbody>
      <tr>
        <th><i class="require-red">*</i>联系方式：</th>
        <td>
          <input class="common-text required" name="phone" value="<?php echo $phone['phone'] ?>" size="30" type="text">
        </td>
      </tr>

      <tr>
        <th></th>
        <td>
          <input class="btn btn-primary btn6 mr10" value="提交" type="submit">
        </td>
      </tr>
      </tbody>
    </table>
    </form>
  </div>