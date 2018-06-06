<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">拼团</a>
    <span class="crumb-step">&gt;</span><span>项目二维码</span></div>
</div>


<div class="result-wrap">
  <div class="result-content" style="text-align: center; padding: 50px">
    <div style="padding: 10px 0;">
      <div><h3>链接：</h3></div>
      <div><?php echo $url; ?></div>
    </div>
    <div class="qrcode"></div>
  </div>
</div>

<script>

  var url ='<?php echo $url; ?>'

  $(function () {
    $('.qrcode').qrcode(url)
  })
</script>