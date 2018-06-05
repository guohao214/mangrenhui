<div id="share_page"></div>
<script>
  var shareTitle = '<?php echo $grouponProject['groupon_name']; ?>'
  var shareImage = '<?php echo $grouponProject['project_cover']; ?>'
  var shareLink = "http://www.mlxiaowu.com/groupon/grouponIndex/<?php echo $grouponProject['groupon_project_code']?>"

  $(document).ready(function () {
    new Vue({
      el: '#share_page',
      data: {},
      mounted: function () {
        this.share()
      },
      methods: {
        share: function () {

          var url = encodeURIComponent(window.location.href)

     //     var self = this
//          if (!WeixinJSBridge || !WeixinJSBridge.invoke) {
//            self.$dialog.toast({
//              mes: '您的环境不支持微信支付，请在微信里打开',
//              timeout: 1500
//            })
//            return
//          }

          this.$request.post('groupon/shareParams', {url: url})
            .then(function (data) {
              let result = data.content

              wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: result.appId, // 必填，公众号的唯一标识
                timestamp: result.timestamp, // 必填，生成签名的时间戳
                nonceStr: result.nonceStr, // 必填，生成签名的随机串
                signature: result.signature,// 必填，签名，见附录1
                jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline']
              })

              wx.ready(function () {
                wx.onMenuShareTimeline({
                  title: shareTitle, // 分享标题
                  link: shareLink, // 分享链接
                  imgUrl: shareImage, // 分享图标
                  success: function () {
                  },
                  cancel: function () {
                  }
                })

                // 分享给朋友
                wx.onMenuShareAppMessage({
                  title: shareTitle, // 分享标题
                  link: shareLink, // 分享链接
                  imgUrl: shareImage, // 分享图标
                  desc: shareTitle,
                  dataUrl: '',
                  success: function () {
                  },
                  cancel: function () {
                  }
                })

              })

            })
            .catch(error => {
            })
        },
      },

    })
  })
</script>