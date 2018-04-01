<?php $baseUrl = get_instance()->config->base_url(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $this->pageTitle; ?></title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
  <meta http-equiv="Pragma" content="no-cache"/>
  <meta http-equiv="Expires" content="0"/>
  <link rel="stylesheet"
        href="<?php echo $baseUrl; ?>static/frontend/lib/yui.base.css">
  <script type="text/javascript"
          src="<?php echo $baseUrl; ?>static/frontend/lib/vendor.js"></script>
  <script type="text/javascript"
          src="<?php echo $baseUrl; ?>static/jquery.min.js"></script>
<!--  <script type="text/javascript"-->
<!--          src="--><?php //echo $baseUrl; ?><!--static/frontend/lib/iscroll.js"></script>-->
  <script type="text/javascript"
          src="<?php echo $baseUrl; ?>static/frontend/lib/global.js?v=000"></script>
  <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <style>
    .yd-cell-item:after {
      margin-left: 0rem !important;
    }
  </style>
</head>
<script>
  document_root = "<?php echo $baseUrl; ?>";
</script>
<body>
<div class="top-header"></div>