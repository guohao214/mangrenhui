<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>后台管理</title>
  <link rel="stylesheet" type="text/css"
        href="<?php echo get_instance()->config->base_url(); ?>static/backend/css/common.css?v=2015010303"/>
  <link rel="stylesheet" type="text/css"
        href="<?php echo get_instance()->config->base_url(); ?>static/backend/css/main.css?v=2015010302"/>
  <script src="<?php echo get_instance()->config->base_url(); ?>static/jquery.min.js"></script>
  <script src="<?php echo get_instance()->config->base_url(); ?>static/qrcode.js"></script>
</head>
<body>
<div class="topbar-wrap white">
  <div class="topbar-inner clearfix">
    <div class="topbar-logo-wrap clearfix">
      <ul class="navbar-list clearfix">
        <li>后台管理系统</li>
<!--        <li><a class="on" href="--><?php //echo UrlUtil::createBackendUrl('project/index'); ?><!--">首页</a></li>-->
<!--        <li><a href="--><?php //echo UrlUtil::createUrl('project/index'); ?><!--" target="_blank">网站首页</a></li>-->
      </ul>
    </div>
    <div class="top-info-wrap">
      <ul class="top-info-list clearfix">
        <li><a><?php echo UserUtil::getUserName(); ?></a></li>
        <li><a href="<?php echo UrlUtil::createBackendUrl('user/changePassword/' . UserUtil::getUserId()); ?>">修改密码</a>
        </li>
        <li><a href="<?php echo UrlUtil::createBackendUrl('login/logout'); ?>">退出</a></li>
      </ul>
    </div>
  </div>
</div>
<div class="container clearfix">
  <div class="sidebar-wrap">
<!--    <div class="sidebar-title">-->
<!--      <h1>菜单</h1>-->
<!--    </div>-->
    <div class="sidebar-content">
      <ul class="sidebar-list">
        <li>
          <a href="#"><i class="icon-font">&#xe003;</i>常用操作</a>
          <ul class="sub-menu">
            <li><a href="<?php echo UrlUtil::createBackendUrl('shop/index') ?>"><i
                  class="icon-font">&#xe031;</i>门店管理</a>
            </li>
              <li><a href="<?php echo UrlUtil::createBackendUrl('project/index') ?>"><i class="icon-font">
                    &#xe008;</i>项目管理</a></li>
            <li><a href="<?php echo UrlUtil::createBackendUrl('beautician/index') ?>"><i class="icon-font">
                  &#xe007;</i>技师管理</a></li>
            <li><a href="<?php echo UrlUtil::createBackendUrl('order/index') ?>"><i class="icon-font">
                  &#xe005;</i>订单管理</a></li>
            <li><a href="<?php echo UrlUtil::createBackendUrl('customer/index') ?>"><i class="icon-font">&#xe060;</i>顾客管理</a>
            <li><a href="<?php echo UrlUtil::createBackendUrl('grouponProject/index') ?>"><i class="icon-font">
                    &#xe008;</i>拼团项目管理</a></li>
            </li>

          </ul>
        </li>
        <li>
          <a href="#"><i class="icon-font">&#xe018;</i>系统管理</a>
          <ul class="sub-menu">
            <li><a href="<?php echo UrlUtil::createBackendUrl('user/index') ?>"><i class="icon-font">
                  &#xe014;</i>账号管理</a></li>
            <li><a href="<?php echo UrlUtil::createBackendUrl('workTime/index') ?>"><i class="icon-font">&#xe017;</i>工作时间设置</a>
            <li><a href="<?php echo UrlUtil::createBackendUrl('setting/contactPhone') ?>"><i class="icon-font">&#xe013;</i>联系方式</a>
            <li><a href="<?php echo UrlUtil::createBackendUrl('unbind/index') ?>"><i class="icon-font">&#xe012;</i>管理技师与前台</a>
            </li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
  <div class="main-wrap"><?php echo $content; ?></div>
</div>
</body>
</html>