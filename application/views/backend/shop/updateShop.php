<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">首页</a>
    <span class="crumb-step">&gt;</span>
    <a class="crumb-name" href="<?php echo UrlUtil::createBackendUrl('shop/index') ?>">店铺管理</a>
    <span class="crumb-step">&gt;</span><span>新增店铺</span></div>
</div>
<div class="result-wrap">
  <div class="result-content">
    <div class="error">
      <?php echo validation_errors(); ?>
    </div>

    <?php echo form_open_multipart(RequestUtil::CM(array('shop_id' => $shop['shop_id']))); ?>
    <table class="insert-tab" width="100%">
      <tbody>
      <tr>
        <th><i class="require-red">*</i>店铺名：</th>
        <td>
          <input class="common-text required" name="shop_name"
                 value="<?php echo $shop['shop_name']; ?>" size="50" type="text">
        </td>
      </tr>

      <tr>
        <th>地址：</th>
        <td><input class="common-text" value="<?php echo $shop['address']; ?>"
                   name="address" size="50" type="text"></td>
      </tr>

      <tr>
        <td></td>
        <td><a href="http://www.gpsspg.com/maps.htm" target="_blank"> 经纬度获取（选择腾讯高德)</a></td>
      </tr>
      <tr>
        <th>经度：</th>
        <td><input class="common-text" value="<?php echo $shop['longitude']; ?>"
                   name="longitude" size="50" type="text"></td>
      </tr>

      <tr>
        <th>纬度：</th>
        <td><input class="common-text" value="<?php echo $shop['latitude']; ?>"
                   name="latitude" size="50" type="text"></td>
      </tr>

      <tr>
        <th><i class="require-red">*</i>店铺图片：</th>
        <td>
          <img class="project_cover"
               src="<?php echo UploadUtil::buildUploadDocPath($shop['shop_logo'], '200x200'); ?>">
          <br>
          <input name="pic" id="" type="file" class="common-text"></td>
      </tr>

      <tr>
        <th>联系人：</th>
        <td><input class="common-text" value="<?php echo $shop['contacts']; ?>"
                   name="contacts" size="50" type="text"></td>
      </tr>

      <tr>
        <th>联系电话：</th>
        <td><input class="common-text" value="<?php echo $shop['contact_number']; ?>"
                   name="contact_number" size="50" type="text"></td>
      </tr>
      <tr>
        <th></th>
        <td>
          <input class="btn btn-primary btn6 mr10" value="提交" type="submit">
          <a class="btn btn6" href="<?php echo UrlUtil::createBackendUrl('shop/index'); ?>">返回</a>
        </td>
      </tr>
      </tbody>
    </table>
    </form>
  </div>