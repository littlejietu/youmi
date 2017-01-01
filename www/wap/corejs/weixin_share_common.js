var weixinChecked = 0,checkTime=0;
//分享
var app_path = window.location.href;
//app_baseUrl = app_path.substr(0, app.path.lastIndexOf("/") + 1),
var debug = 1;
var myAppId;
var shareObj  = {};
shareObj.imgUrl = WapSiteUrl+'/images/108X108-icon.png';
shareObj.desc = '九号街区';
shareObj.title = '九号街区';
function showAlert(res){
    if(debug){
        if(typeof res == 'string'){
            //tipsAlert(res);
            alert(res);
            return ;
        }
        //tipsAlert(res.errMsg);
        alert(res.errMsg);
    }
}

function weixinInit(e,list,fun) {
    myAppId = e.appId;
    wx.config({
        debug:0,
        appId: e.appId,
        timestamp: e.timestamp,
        nonceStr: e.nonceStr,
        signature: e.signature,
        jsApiList: list,
        fail:function(res){
            fun(2)
            showAlert('fail：'+res);
        }
    }),
    wx.ready(function() {
        weixinChecked = 1;
        if(fun)
            fun(1)
        showAlert('ready');

        //setShareInfo();
    })
    wx.error(function(res){
        showAlert('config 出错：'+res.errMsg);
        if(fun)
            fun(2)

    });
    //wx.checkJsApi({
    //    jsApiList: list, // 需要检测的JS接口列表，所有JS接口列表见附录2,
    //    success: function(res) {
    //        // 以键值对的形式返回，可用的api值true，不可用为false
    //        // 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
    //    },
    //    fail:function(res){
    //        fun(2)
    //    }
    //});

    //wx.checkJsApi({
    //    jsApiList: list,
    //    success: function (res) {
    //        // alert(JSON.stringify(res));
    //        // alert(JSON.stringify(res.checkResult.getLocation));
    //        if (res.checkResult.getLocation == false) {
    //            alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
    //            return;
    //        }
    //    }
    //});
}

function wxpay(obj,success){
    if(!weixinChecked){
        showAlert('微信初始化未完成');
        return;
    }
    wx.chooseWXPay({
        timestamp: obj.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
        nonceStr: obj.nonceStr, // 支付签名随机串，不长于 32 位
        package: obj.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
        signType: obj.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
        paySign: obj.paySign, // 支付签名
        success: success
    });
}

//var images = {
//    localId: [],
//    serverId: []
//};
/*
* 选择图片
* successHandler 选择完成后处理函数
* */
function wxChooseImage(successHandler){

    if(!weixinChecked){
        showAlert('微信初始化未完成');
        return;
    }
    wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'],
        success:successHandler,
        fail:function(res){
            showAlert(res);
        }
        //    function (res) {
        //    images.localId = res.localIds;
        //    alert('已选择 ' + res.localIds.length + ' 张图片');
        //}
    });
}

function wxGetLocation(obj){
    if(!weixinChecked){
        showAlert('微信初始化未完成');
        obj.cancel();
        return;
    }
    wx.getLocation({
        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success:function(res){
            showAlert('location success:'+res.errMsg);
            obj.success(res);
        },
            //function (res) {
            //var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            //var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            //var speed = res.speed; // 速度，以米/每秒计
            //var accuracy = res.accuracy; // 位置精度
        //}
        cancel:function(res){
            showAlert('location cancel:'+res.errMsg);
            if(obj.cancel){
                obj.cancel(res);
            }

        },
        fail:function(res){
            showAlert('location fail:'+res.errMsg);
            if(obj.cancel){
                obj.cancel(res);
            }
        },
        complete:function(res){
            showAlert('location complete:'+res.errMsg);
            if(obj.complete){
                obj.complete(res);
            }else{
                complete(res);
            }
        }
    });

    //WeixinJSBridge.invoke("geoLocation",{
    //    type: 'wgs84',
    //
    //},success,function(){
    //    alert('用户拒绝授权获取地理位置');
    //});

}



//wx.previewImage({
//    current: 'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
//    urls: [
//        'http://img3.douban.com/view/photo/photo/public/p2152117150.jpg',
//        'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
//        'http://img3.douban.com/view/photo/photo/public/p2152134700.jpg'
//    ]
//});
function fail(res){
    //console.log(res.errMsg);
    showAlert('fail:'+res.errMsg);

}

function complete(res){
    //console.log(res.errMsg);
    showAlert('complete'+res.errMsg);
}

function wxShareTimeLine(data){
    if(!weixinChecked){
        showAlert('微信初始化未完成');
        return;
    }

    wx.onMenuShareTimeline({
        title: data.title, // 分享标题
        link: data.link, // 分享链接
        imgUrl: data.imgUrl, // 分享图标
        desc: data.desc, // 分享图标
        success:data.success,
        //    function () {
        //    // 用户确认分享后执行的回调函数
        //},
        cancel:data.cancel,
        fail:fail,
        complete:complete

        //function () {
        //    // 用户取消分享后执行的回调函数
        //}
    });
    //WeixinJSBridge.invoke("shareTimeline");
    //WeixinJSBridge.invoke("shareTimeline",{
    //    title: data.title, // 分享标题
    //    link: data.link, // 分享链接
    //    imgUrl: data.imgUrl, // 分享图标
    //    desc: data.desc, // 分享图标
    //    success:data.success,
    //    cancel:data.cancel,
    //    //fail:
    //});
}

function wxShareToFriend(data){
    if(!weixinChecked){
        showAlert('微信初始化未完成');
        return;
    }
    wx.onMenuShareAppMessage({
        title: data.title, // 分享标题
        link: data.link, // 分享链接
        imgUrl: data.imgUrl, // 分享图标
        desc: data.desc, // 分享图标
        success:data.success,
        //    function () {
        //    // 用户确认分享后执行的回调函数
        //},
        cancel:data.cancel,
        fail:fail,
        complete:complete
        //function () {
        //    // 用户取消分享后执行的回调函数
        //}
    });
    //WeixinJSBridge.invoke("sendAppMessage");
    //WeixinJSBridge.invoke("sendAppMessage",{
    //    title: data.title, // 分享标题
    //    link: data.link, // 分享链接
    //    imgUrl: data.imgUrl, // 分享图标
    //    desc: data.desc, // 分享图标
    //    success:data.success,
    //    cancel:data.cancel,
    //});

}




function initWx(list,fun,url,site_id){

    //var url = location.href;
    ////if(url.indexOf('?')>=0){
    ////    url= url.split('?')[0];
    ////}

    sendPostData({url:url,site_id:site_id},ApiUrl+'wxauth/jsapi',
        function(e){
            //showAlert('code：'+e.code);
            if(e.code == 1 || e.code == 'SUCCESS'){
                //console.log(e.data.appId);
                if(e.data.timestamp && e.data.nonceStr && e.data.signature){
                    weixinInit(e.data,list,fun);
                }
            }else{
                if(fun)
                    fun(2);
            }

        },
        function(XMLHttpRequest, textStatus, errorThrown){
            //console.log(textStatus);
            showAlert('errorThrown:'+errorThrown);
            if(fun)
                fun(2);
        //alert(XMLHttpRequest.status);
        //alert(XMLHttpRequest.readyState);
        //alert(textStatus+'dd');
    });
}
