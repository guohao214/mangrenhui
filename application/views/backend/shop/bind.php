<div class="crumb-wrap">
  <div class="crumb-list"><i class="icon-font"></i>
    <a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>">首页</a>
    <span class="crumb-step">&gt;</span>
    <a class="crumb-name" href="<?php echo UrlUtil::createBackendUrl('shop/index') ?>">店铺管理</a>
    <span class="crumb-step">&gt;</span><span>绑定<?php echo $params['type'] == 3 ? '技师' : '前台' ?></span></div>
</div>
<div class="result-wrap">
  <div class="result-content" style="text-align: center; padding: 50px">
    <div class="qrcode"></div>
  </div>
</div>

<script>

  function getQueryString(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);

    return results == null ? "" : decodeURIComponent(results[1]);
  }

  $(function () {
    var type = getQueryString('type')
    var shopId = getQueryString('shop_id')
    var url = 'http://www.mlxiaowu.com/bind/other?type=' + type + '&shop_id=' + shopId
    $('.qrcode').qrcode(url)
  })
</script>