$(document).ready(function () {
  // 绑定全局ajax
  var $document = $(document);
  $document.ajaxStart(function () {
    Vue.prototype.$dialog.loading.open('请求中...');
  })

  $document.ajaxComplete(function () {
    Vue.prototype.$dialog.loading.close();
  })

  $document.ajaxError(function () {
    Vue.prototype.$dialog.loading.close();
  })

  var $tool = {}
  Vue.prototype.$tool = $tool = {}
  $tool.getQueryString = function (name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);

    return results == null ? "" : decodeURIComponent(results[1]);
  }

  var $request = {}
  Vue.prototype.$request = $request = {}
  $request.request = function (url, method, data) {
    return new Promise(function (res, rej) {
      $.ajax({
        url: '/' + url + '?_t=' +(+(new Date())),
        method: method,
        data: data || {},
        dataType: 'json',
      }).done(function (data) {
        //debugger
        data.status != 1 ? rej(data) : res(data.data)
      }).fail(function (err) {
        console.log(err)
        rej({status: '0', 'detail': '请求失败:' + err.status})
      })
    })

  }
  $request.get = function (url, data) {
    return this.request(url, 'get', data)
  }

  $request.post = function (url, data) {
    return this.request(url, 'post', data)
  }
})