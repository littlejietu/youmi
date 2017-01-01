//rem设置
!function(a,b){var d,c=function(){var a=32,c=b.documentElement,d=c.clientWidth;d&&(c.style.fontSize=a*(d/320)+"px")};b.addEventListener&&(d="orientationchange"in a?"orientationchange":"resize",a.addEventListener(d,c,!1),b.addEventListener("DOMContentLoaded",c,!1),c())}(window,document);

;(function(win,undefined){
  var utils = {
    //获取url参数
    getQueryString : function(name){
      if (win.location.href.indexOf("?") != win.location.href.lastIndexOf("?"))
        var urls = win.location.href.replace(/\?/g, "&").replace(/^.*?&/, "")
      else
        var urls = win.location.href.replace(/^.*\?/, "");
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
      var r = ("?" + urls).substr(1).match(reg);
      if (r != null) return unescape(r[2]);
      return null;
    },
    //封装ajax
    ajaxData : function(opts,fnSuccess,fnError){
      var that = this;
      $.ajax({
        type : opts.type,
        url : opts.url,
        cache:true,
        dataType : "jsonp",
        success : function(data){
          fnSuccess.call(that,data);
        },
        error:function(data){
          fnError.call(that,data);
        }
      });

    },
    //获取时间戳
    datetime_to_unix:function(datetime){
      var tmp_datetime = datetime.replace(/:/g,'-');
        tmp_datetime = tmp_datetime.replace(/ /g,'-');
        var arr = tmp_datetime.split("-");
        var now = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
        return parseInt(now.getTime());
    },
    //重置rem值
    resizeRem:function(){
      window.remFontSize = document.documentElement.clientWidth / 10;
      document.documentElement.style.fontSize = document.documentElement.clientWidth / 10 + "px";
      $("body").append('<p id="remset" style="width:10rem;"></p>');
        var realrem= $("#remset").width()/10;
         var rem=document.documentElement.clientWidth/10;
         if(realrem!=rem){
          $("html").css('font-size',(rem*rem)/realrem+"px");
         }
         $("#remset").remove();
    },
    //判断是否为ios  
    isIOS:function(){
      var ua = navigator.userAgent.toLowerCase(); 
      if (/iphone|ipad|ipod/.test(ua)) return true;
    },  
    // 添加Cookie
    addCookie: function(name, value, options) {
	    if (arguments.length > 1 && name != null) {
        if (options == null) {
            options = {};
        }
        if (value == null) {
            options.expires = -1;
        }
        if (typeof options.expires == "number") {
            var time = options.expires;
            var expires = options.expires = new Date();
            expires.setTime(expires.getTime() + time * 1000);
        }
        if (options.path == null) {
            options.path = "/";
        }
        if (options.domain == null) {
            options.domain = ".qccr.com";
        }
        document.cookie = encodeURIComponent(String(name)) + "=" + encodeURIComponent(String(value)) + (options.expires != null ? "; expires=" + options.expires.toUTCString() : "") + ("; path=/") + ("; domain=" + options.domain) + (options.secure != null ? "; secure" : "");
	    }
    },
    // 获取Cookie
    getCookie: function(name) {
	    if (name != null) {
        var value = new RegExp("(?:^|; )" + encodeURIComponent(String(name)) + "=([^;]*)").exec(document.cookie);
        return value ? decodeURIComponent(value[1]) : null;
	    }
    },
    //设置本地存储值
    setStorage: function (key, value) {
      localStorage.setItem(key, value);
    },
    //获取本地存储值
    getStorage: function (key) {
      return localStorage.getItem(key);
    },
    //删除本地存储值
    removeStorage: function (key) {
      return localStorage.removeItem(key);
    },
    //清除本地存储
    clearStorage: function () {
       return localStorage.clear();
    }
  }

  if (typeof module != 'undefined' && module.exports) {
    module.exports = utils;
  } else if (typeof define == 'function' && define.amd) {
    define(function() {
      return utils;
    });
  } else {
    window.utils = utils;
  }
})(window);

$(function() {
  utils.resizeRem();//设置rem
});
