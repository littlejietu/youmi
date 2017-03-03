/**
 * Created by mirong on 2017/2/19.
 */
var CONFIG_DEBUG = true;
var IS_DEBUG = true;

if (CONFIG_DEBUG) {
    //document.write("<script src='http://www.xshop.com/wap/corejs/testData.js'></script>");
    var m1 = document.createElement('meta');
    m1.setAttribute('http-equiv', 'Cache-Control');
    m1.setAttribute('content', 'no-cache, no-store, must-revalidate');
    var m2 = document.createElement('meta');
    m2.setAttribute('http-equiv', 'Pragma');
    m2.setAttribute('content', 'no-cache');
    var m3 = document.createElement('meta');
    m3.setAttribute('http-equiv', 'Expires');
    m3.setAttribute('content', '0');
    document.getElementsByTagName('head')[0].appendChild(m1);
    document.getElementsByTagName('head')[0].appendChild(m2);
    document.getElementsByTagName('head')[0].appendChild(m3);

}
//window.location.href = 'js://getDeviceInfo/123/getDeviceInfoCallBack';

var pageImageTotal;
$(function(){
    //var imgLoad= 0;
    //pageImageTotal = $('img').length;
    //$('img').each(function () {
    //    $(this).load(function(){
    //        imgLoad++;
    //        if(imgLoad>pageImageTotal){
    //            $('#loading_inmation').hide();
    //        }
    //    })
    //    if ($(this).complete) {
    //        imgLoad++;
    //    }
    //    $(this).error(function(){
    //        imgLoad++;
    //        if(imgLoad>pageImageTotal){
    //            $('#loading_inmation').hide();
    //        }
    //    })
    //});
    if(typeof FastClick != 'undefined'){
        FastClick.attach(document.body);
    }
    //$(window).resize(function() {
    //    location.reload();
    //});

});

function jump_by_tpl_id(tpl_id){
    var url = 'zooer://productdetail?tpl_id='+tpl_id;
    jump_to_url(url);
}


function webBackHandler(url, boo) {
    if (url && boo) {
        var ss = "?";
        if (url.indexOf(ss) > 0) {
            ss = "&"
        }

        if (urlToken) {
            url = url + ss + 'token=' + urlToken;
        }
        location.href = url;
        return;
    }
    if (!deviceInfo) {
        if (url) {
            window.location.href = url;
        } else {
            window.history.go(-1);
        }

    } else {
        window.location.href = 'js://closePage/123/';
    }
}

function ajaxFileUpload(url, img, file, imageObj) {
    var time = new Date().getTime();
    var obj = {};
    var sign = get_sign_str(obj, time, isNeedToken(url))
    obj.sign = sign;
    obj.timestamp = time;
    $.ajaxFileUpload
    (
        {
            url: url, //用于文件上传的服务器端请求地址
            secureuri: true, //是否需要安全协议，一般设置为false
            fileElementId: file, //文件上传域的ID
            dataType: 'json', //返回值类型 一般设置为json
            data: obj,
            type: 'post',
            success: function (data, status)  //服务器成功响应处理函数
            {
                $('#' + img).attr("src", data.data.url);
                if (imageObj) {
                    imageObj[img] = data.data.path;
                }
                if ($('#' + img).siblings('i')) {
                    $('#' + img).siblings('i').show()
                }
                if (typeof (data.error) != 'undefined') {
                    if (data.error != '') {
                        tipsAlert(data.error);
                    } else {
                        tipsAlert(data.msg);
                    }
                }
            },
            error: function (data, status, e)//服务器响应失败处理函数
            {
                tipsAlert(e);
            }
        }
    )
    return false;
}


function testMobile(str) {
    return /^1[3|4|5|7|8]\d{9}$/.test(str)
}
function get_total_page(count, pagesize) {
    var total = parseInt((parseInt(count) + parseInt(pagesize) - 1) / parseInt(pagesize));
    return total;
}
function jump_to_search(url, category) {
    //getUrlParam()
    var arr = url.split('?');

    var url = SiteUrl + '/wap/home/gshow-hor.html?' + arr[1];

    location.href = url;
}

function jump_to_url(url) {
    if (!url) {
        return;
    }
    if (url.indexOf('site_id') < 0 && !deviceInfo) {
        url += '&site_id=' + get_string_fromlocal('site_id');

    }
    var str = url.substring(url.indexOf("//") + 2, url.indexOf("?"));
    var arr = url.split("?");
    var new_url = "";
    switch (str) {
        case 'productdetail':

            new_url = SiteUrl + '/wap/home/productdetails/index.html' + '?' + arr[1];
            url = 'js://openProductDetail/123/callback?' + arr[1]
            break;

        default :

            new_url = url.replace('zooer://webview?url=', "");
            new_url = new_url.substr(0, new_url.indexOf('&'));

            break;
    }
    if (deviceInfo) {
        location.href = url;
    } else {
        location.href = new_url;
    }


}

var urlToken;
function get_user_token() {
    if (getTokenFromUrl()) {
        return urlToken;
    }
    if(IS_DEBUG) return 'mirong';
    return get_user_data_from_local('token');
}

function showTips(msg){
    tipsAlert('加入购物车成功！');
}

function add_goods_to_cart(goods_id, sku_id, num1, fun) {
    if (event) {
        event.stopPropagation();
    }
    if (deviceInfo) {
        location.href = 'js://addToCart/123/callback?id=' + goods_id + '&sku_id=' + sku_id + '&number=' + num1;
        return;
    }
    sendPostData({goods_id: goods_id, sku_id: sku_id, num: num1}, ApiUrl + 'm/cart/add', function (result) {
        if (result.code == 1) {
            add_cart_num_to_local(goods_id, sku_id, num1);
            showTips('加入购物车成功！');
            //sendPostData({site_id:get_string_fromlocal('site_id')},ApiUrl+'m/cart/',function(result){
            //    if(result.code == 1){
            //        if (typeof  setCartNum == 'function') {
            //            setCartNum(result.num);
            //            return;
            //        }
            //    }else{
            //        tipsAlert(result.msg);
            //    }
            //
            //})
        } else {
            tipsAlert(result.msg);
        }
        if (fun) {
            fun(result);
        }
        if (typeof  setCartNum == 'function') {
            setCartNum();
        }


    });
}

function add_cart_num_to_local(goods_id, sku_id, num1) {
    //var obj =  get_json_fromlocal(get_my_open_id());
    var obj = get_user_data_from_local('cart');
    var num = 0;
    if (!obj) {
        obj = {};
    }
    if (!obj.goods_list) {
        obj.goods_list = [];
    }
    var boo = true;
    for (var key in obj.goods_list) {
        if (obj.goods_list[key].goods_id == goods_id && obj.goods_list[key].sku_id == sku_id) {
            obj.goods_list[key].num = parseInt(num1)+parseInt(obj.goods_list[key].num) ;
            boo = false;
        }
    }
    if (boo) {
        obj.goods_list.push({goods_id: goods_id, sku_id: sku_id, num: num1});
    }
    save_user_data_to_local('cart', obj);

}


function getTokenFromUrl() {
    if (!urlToken) {
        urlToken = getUrlParam('token');
    }
    return urlToken;
}

function addAddressNow(){
    save_user_data_to_local('op_addr',location.href);
    var url = '../../mine/setting/address.html?ef=1';
    var token = getUrlParam('token');
    if(token){
        url+='&'+token;
    }
    location.href=url;
}
var deviceInfo;
window.getDeviceInfoCallBack = function (result) {
    //alert(result);
    var obj = JSON.parse(result);
    deviceInfo = obj;
    if (obj.terminal == "android" || obj.terminal == "android") {

    }


}
/*
 *token
 * cart
 * cartNum
 * collect
 * collectNum
 * userInfo
 * message
 * messageNum
 * addressList
 *op_addr
 * */
function save_user_data_to_local(key, obj) {
    if (key) {
        var userData = get_json_fromlocal(get_my_open_id());
        if (userData) {
            userData[key] = obj;
            save_json_tolocal(get_my_open_id(), userData);
        }
    }
}



function get_user_data_from_local(key) {
    if (key) {
        var userData = get_json_fromlocal(get_my_open_id());
        if (userData) {
            return userData[key];
        }
    }
}
function get_my_open_id() {
    var key = get_string_fromlocal('youmikey');
    return key;
}

function isNeedToken(url) {
    if (url.indexOf('api/home') >= 0) {
        return false;
    }else {
        return true;
    }
}

function showUnkownError(){
    show_tips_content2({msg:'好像出了点问题，您可以去其他地方看看！',canfun:goHome,canbtn:'确定'});
}
/*
 * jsonp
 * */
//function sendPostData(obj,url,fun,errfun){
//  if(CONFIG_DEBUG && testData(obj,url,fun)){
//     return;
//  }
//
//  var time = new Date().getTime();
//  var sign = get_sign_str(obj,time,isNeedToken(url))
//  obj.sign =sign;
//  obj.timestamp = time;
//  if(!errfun){
//      errfun = errorHnadler;
//  }
//  $.ajax({
//      url: url,
//      type: 'post',
//      dataType: 'jsonp',
//      data:obj,
//      jsonp: "callback",
//      success:fun,
//      error:errfun,
//  });
//}
function isWeixinBrowser(){
    return /micromessenger/.test(navigator.userAgent.toLowerCase())
}

function sendPostData(obj, url, fun, errfun) {
    //$('#loading_inmation').show();
    var temp = url.split('/api/')[1];
    if (location.href.indexOf('site_id') < 0) {
        obj.site_id = get_string_fromlocal('site_id')
    } else {
        obj.site_id = getUrlParam("site_id");
    }
    
    var time = new Date().getTime();
    var sign = get_sign_str(obj, time, isNeedToken(url))
    obj.sign = sign;
    obj.timestamp = time;
    if (!errfun) {
        errfun = errorHnadler;
    }
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: obj,
        success: function (res) {
            if(res.code == -3 && res.msg.indexOf('签名错误')>=0){
                showUnkownError();
                return ;
            }
            if ( res.code == 'NeedLogin') {
                location.href = ApiUrl+'/wxauth/go?url='+WapSiteUrl+'/index.html?site_id='+obj.site_id;
                return;
            }
            fun(res);
            $('#loading_inmation').hide();

        },
        error: function (res) {
            $('#loading_inmation').hide();
            errfun(res);
        },
    });
    delete obj.sign;
    delete obj.timestamp;
    delete obj.token;
}
function sendtestPostData(obj, url, fun, errfun) {


    var time = new Date().getTime();
    var sign = get_sign_str(obj, time, isNeedToken(url))
    obj.sign = sign;
    obj.timestamp = time;
    if (!errfun) {
        errfun = errorHnadler;
    }
    var str = $
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'text',
        data: obj,
        success: function (result) {
            $('body').html(result);
        },
        error: errfun,
    });
}

function getUrlParam(name, url) {
    //构造一个含有目标参数的正则表达式对象
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    //匹配目标参数
    if (!url) {
        url = window.location.search;
    }
    url = decodeURI(url);
    var r = url.substr(1).match(reg);
    //返回参数值
    if (r != null) return unescape(r[2]);
    return null;
}


function get_sign_str(json_obj, time, need_token) {
    var str = "";
    var keys = [];
    if (need_token) {
        var tok = get_user_token();
        if(tok){
            json_obj.token =tok;
        }

    }

    for (var key in json_obj) {
        keys.push(key);
    }
    keys.sort();
    $.each(keys, function (index, value) {
        var nn = json_obj[value]
        //if(typeof  nn == 'object'){
        //    nn = JSON.stringify(nn);
        //}
        str += value + "=" + nn + "&";
    });

    //alert(str);
    str += "appkey=number9street&timestamp=" + time;

    str = encodeURIComponent(str);
    //alert(str);
    str = hex_md5(str);
    //alert(str);
    return str;
}

/*
var IPAdd;
function getIP() {

    $.ajax({
        url: "http://ip.chinaz.com/getip.aspx",
        type: 'get',
        dataType: 'jsonp',
        success: function (data) {
            IPAdd = data.ip;
        },
    });
}
*/

function goHome(){
    var site_id = get_string_fromlocal('site_id'); 
    location.href = WapSiteUrl + '/index.html?site_id='+site_id+'&v='+Math.random();
}


function errorHnadler(XMLHttpRequest, textStatus, errorThrown) {
    //alert(XMLHttpRequest)
    if(XMLHttpRequest.status == 200){
        tipsAlert('好像哪里不对。。。')
    }else{

    }

}

function getUserInfo(fun, bNew){
    if(typeof(bNew)=="undefined")
        bNew = false;
    if(!bNew){
        var user = get_user_data_from_local('userInfo');
        if(user){
            if(fun){
                fun(user);
            }
            return user;
        }
    }
    sendPostData({}, ApiUrl + "m/user/get", function (result) {
        if (result.code=='SUCCESS') {
            save_user_data_to_local('userInfo', result.data);
            var user = get_user_data_from_local('userInfo');
            if(fun){
                fun(user);
            }
            return user;
        }else{
            tipsAlert(result.msg);
        }
    });
}


function save_string_tolocal(key, value) {
    if (window.localStorage) {
        localStorage.setItem(key, value);
    }
}

function get_string_fromlocal(key) {
    if (window.localStorage) {
        return localStorage.getItem(key);
    }


}

function save_json_tolocal(key, value) {
    if (window.localStorage) {
        var str = JSON.stringify(value);
        localStorage.setItem(key, str);
        return 1;
    }
    return 0;//保存数据失败
}

function get_json_fromlocal(key) {
    if (window.localStorage) {
        //var str = JSON.stringify(value);
        var json = localStorage.getItem(key);
        if (json) {
            return JSON.parse(json);
        }

    }
    return "";//未找到数据
}

function remove_json_fromlocal(key) {
    if (window.localStorage) {
        //var str = JSON.stringify(value);
        var json = localStorage.removeItem(key)
        return 1;

    }
    return 0;//删除失败
}

/*
 * options(
 *
 *
 * */
SwiperUtils = function (opts) {
    var mySwiper;
    var deep = opts.deep;
    var holdPositionAfter = 0;
    var holdPositionBefor = 0;
    var collectswiper = opts.collectswiper;
    var before = true;
    var page = 1;
    var totalpage = 1;

    this.setPage = function (p, tp) {
        page = p;
        totalpage = tp;
    }

    this.setBefore = function (b) {
        before = b;
    }

    this.getBefore  =  function(){
        return before;
    }

    this.gotoTop = function(){
        mySwiper.setWrapperTranslate(0, 0, 0);
    }

    this.appendSlide = function(a,b,c){
        mySwiper.appendSlide(a,b,c);
    }
    this.removeAllSlides = function(){
        mySwiper.removeAllSlides();
    }
    this.resetSwiperStatus = function(){
        if (before) {
            page = 1;
            //mySwiper.removeAllSlides();
            mySwiper.params.onlyExternal = false;
            mySwiper.setWrapperTranslate(0, 0, 0);
            mySwiper.reInit();
        } else {
            mySwiper.params.onlyExternal = false;
            var dd = mySwiper.height - $(collectswiper).height() - $(window).width() / 5;
            if ($(collectswiper).height() < mySwiper.height) {
                dd = 0;
            }
            mySwiper.setWrapperTranslate(0, dd, 0);
        }

    }
    this.setSwiperSlider = function (str) {
        if (before) {
            page = 1;
            //mySwiper.removeAllSlides();
            mySwiper.params.onlyExternal = false;
            mySwiper.setWrapperTranslate(0, 0, 0);
            $(collectswiper).html(str);

            mySwiper.reInit();
        } else {
            mySwiper.params.onlyExternal = false;
            var dd = mySwiper.height - $(collectswiper).height() - $(window).width() / 5;
            if ($(collectswiper).height() < mySwiper.height) {
                //dd = $(collectswiper).css('top');
                dd = 0;
            }
            mySwiper.setWrapperTranslate(0, dd, 0);
            //var pos = mySwiper.getWrapperTranslate('y');

            $(collectswiper).append(str);
            mySwiper.reInit();
            //if(pos > mySwiper.height - $(collectswiper).height()){
            //    pos = mySwiper.height - $(collectswiper).height();
            //}
            //
            //mySwiper.setWrapperTranslate(0,pos ,0);
        }
    }
    mySwiper = new Swiper(opts.container, {
        slidesPerView: 'auto',
        mode: 'vertical',
        watchActiveIndex: true,
        updateOnImagesReady: true,
        visibilityFullFit: true,
        resizeReInit: true,
        autoResize: true,
        //freeMode : true,
        scrollbar: {
            container: '.swiper-scrollbar',
            hide: false,
            dragSize: 300,
        },

        onTouchStart: function () {
            holdPositionAfter = 0;
            holdPositionBefor = 0;
        },
        onResistanceBefore: function (s, pos) {
            holdPositionBefor = pos;
        },
        onResistanceAfter: function (s, pos) {
            holdPositionAfter = pos;
        },
        onTouchEnd: function () {

            if (holdPositionAfter > deep) {
                //mySwiper.setWrapperTranslate(0,$(window).height() - mySwiper.slides[0].getHeight()* mySwiper.slides.length- 100,0)
                if (page >= totalpage) {
                    return;
                }
                before = false;
                var dd = mySwiper.height - $(collectswiper).height() - $(window).width() / 5;
                if ($(collectswiper).height() < mySwiper.height) {
                    //dd = $(collectswiper).css('top');
                    dd = 0;
                }

                mySwiper.setWrapperTranslate(0, dd, 0);
                mySwiper.params.onlyExternal = true
                opts.swpierHandler(before)
            }
            if (holdPositionBefor > deep) {
                before = true;
                mySwiper.setWrapperTranslate(0, $(window).width() / 5, 0);
                mySwiper.params.onlyExternal = true;
                opts.swpierHandler(before)
            }

        }
    });
};
/*
 *
 * yyyy-MM-dd hh:mm:ss
 * */
Date.prototype.Format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

function previewImage(file, imageid, fileid, obj) {
    //var MAXWIDTH  = 100;
    //var MAXHEIGHT = 100;
    //var div = document.getElementById('preview');
    if (file.files && file.files[0]) {
        //var img = document.getElementById(imageid);
        //
        if(file.files[0].size > 1024*1024){
            tipsAlert('上传图片的尺寸不能超过1M!')
            return;
        }
        var reader = new FileReader();
        reader.onload = function (evt) {
            ajaxFileUpload(ApiUrl + 'm/upload/img', imageid, fileid, obj)
            //$(imageid).attr('src',evt.target.result)
        };
        reader.readAsDataURL(file.files[0]);
    }
    else {
        //var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
        //file.select();
        //var src = document.selection.createRange().text;
        //var img = document.getElementById('imghead');
        //img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
        //var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
        //status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
        //div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;margin-left:"+rect.left+"px;"+sFilter+src+"\"'></div>";
    }
}




var tips_content = '<div class="cancel-mask2" style="display:block;">'
    +'<div class="cancel-outMask10" >'
    +'<div class="cancel-wrapper">'
    +'<h3>温馨提示</h3>'
    +'<div class="div1" style="text-align:center;position:relative;">'
    +'<img src="http://'+window.location.host+'/wap/images/logo_80.png" style="position:absolute;width:42px;height:40px;top:10px;left:30px;margin-right:5px;"/>'
    +'<p id="tips_content_txt" style="padding-left:80px;line-height:20px;text-align:left;">确定要取消吗？</p>'
    +'</div>'
    +'<p class="p1"><button class="btn1" id="tips_sure_btn">确定</button></p>'
    +'</div>'
    +'</div>'
    +'</div>';
var tipsinit  = false;
function tipsAlert(msg){
    if(!tipsinit){
        tipsinit = true;
        $('body').append(tips_content);
        $('#tips_sure_btn').click(function(){
            $('.cancel-mask2').hide();
        });
        $(window).bind('resize',function(){
            resize();
        });
    }
    $('#tips_content_txt').text(msg);
    $('.cancel-mask2').show();
    resize();
    function resize(){
        var hei =$(window).height();
        var wid =$(window).width();
        var hh = $('.cancel-mask2 .cancel-outMask10').height();
        var ww = $('.cancel-mask2 .cancel-outMask10').width();
        $('.cancel-mask2 .cancel-outMask10').css('left',wid-ww >> 1 );
        $('.cancel-mask2 .cancel-outMask10').css('top',hei-hh >> 1 );
    }

}

var tips_content1 = '<div class="cancel-mask" style="display:none;">'
    +'	<div class="cancel-outMask" >'
    +'		<div class="cancel-wrapper">'
    +'			<h3>温馨提示</h3>'
    +'			<div class="div1" style="text-align:center;position:relative;">'
    +'				<img src="http://'+window.location.host+'/wap/images/logo_80.png" style="position:absolute;width:42px;height:40px;top:10px;left:30px;margin-right:5px;"/>'
    +'				<div id="msg_content" style="padding-left:80px;line-height:20px;text-align:left;">确定要取消这个订单吗？</div>'
    +'			</div>'
    +'			<p class="p1"><button class="btn1 details4-btn1" id="tips2_sure_btn">确定</button><button  class="btn2" id="tips2_cancel_btn">我再想想</button></p>'
    +'		</div>'
    +'	</div>'
    +'</div>';
var tipsinit1  = false;
/*
 opts{msg:'111',okbtn:'确定',canbtn:'取消'，okfun:fun,canfun:fun,showcan:boolean,showok:boolean}

 */
function show_tips_content2(opts){
    if(!tipsinit1){
        tipsinit1 = true;
        $('body').append(tips_content1);

        $(window).bind('resize',function(){
            resize();
        });
    }
    if(opts.okbtn){
        $("#tips2_sure_btn").text(opts.okbtn);
    }else{
        $("#tips2_sure_btn").text("确定")
    }

    if(opts.canbtn){
        $("#tips2_cancel_btn").text(opts.canbtn);
    }else{
        $("#tips2_cancel_btn").text("取消")
    }
    if(typeof opts.showcan == 'undefined'){
        $("#tips2_cancel_btn").show();
    }else{
        if(opts.showcan){
            $("#tips2_cancel_btn").show();
        }else{
            $("#tips2_cancel_btn").hide();
        }
    }

    if(typeof opts.showok == 'undefined'){
        $("#tips2_sure_btn").show();
    }else{
        if(opts.showok){
            $("#tips2_sure_btn").show();
        }else{
            $("#tips2_sure_btn").hide();
        }
    }

    $("#tips2_cancel_btn").one('click',function(){
        $('.cancel-mask').hide();
        $("#tips2_sure_btn").off('click');
        if(opts.canfun){
            opts.canfun();
        }
    });

    $("#tips2_sure_btn").one('click',function(){
        $('.cancel-mask').hide();
        $("#tips2_cancel_btn").off('click');
        if(opts.okfun){
            opts.okfun();
        }
    });


    $('#msg_content').html(opts.msg);
    $('.cancel-mask').show();
    resize();
    function resize(){
        var hei =$(window).height();
        var wid =$(window).width();
        var hh = $('.cancel-outMask').height();
        var ww = $('.cancel-outMask').width();
        $('.cancel-mask .cancel-outMask').css('left',wid-ww >> 1 );
        $('.cancel-mask .cancel-outMask').css('top',hei-hh >> 1 );
    }

}

/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;
/* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad = "";
/* base-64 pad character. "=" for strict RFC compliance   */
var chrsz = 8;
/* bits per input character. 8 - ASCII; 16 - Unicode      */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s) {
    return binl2hex(core_md5(str2binl(s), s.length * chrsz));
}
function b64_md5(s) {
    return binl2b64(core_md5(str2binl(s), s.length * chrsz));
}
function str_md5(s) {
    return binl2str(core_md5(str2binl(s), s.length * chrsz));
}
function hex_hmac_md5(key, data) {
    return binl2hex(core_hmac_md5(key, data));
}
function b64_hmac_md5(key, data) {
    return binl2b64(core_hmac_md5(key, data));
}
function str_hmac_md5(key, data) {
    return binl2str(core_hmac_md5(key, data));
}

/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test() {
    return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}

/*
 * Calculate the MD5 of an array of little-endian words, and a bit length
 */
function core_md5(x, len) {
    /* append padding */
    x[len >> 5] |= 0x80 << ((len) % 32);
    x[(((len + 64) >>> 9) << 4) + 14] = len;

    var a = 1732584193;
    var b = -271733879;
    var c = -1732584194;
    var d = 271733878;

    for (var i = 0; i < x.length; i += 16) {
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;

        a = md5_ff(a, b, c, d, x[i + 0], 7, -680876936);
        d = md5_ff(d, a, b, c, x[i + 1], 12, -389564586);
        c = md5_ff(c, d, a, b, x[i + 2], 17, 606105819);
        b = md5_ff(b, c, d, a, x[i + 3], 22, -1044525330);
        a = md5_ff(a, b, c, d, x[i + 4], 7, -176418897);
        d = md5_ff(d, a, b, c, x[i + 5], 12, 1200080426);
        c = md5_ff(c, d, a, b, x[i + 6], 17, -1473231341);
        b = md5_ff(b, c, d, a, x[i + 7], 22, -45705983);
        a = md5_ff(a, b, c, d, x[i + 8], 7, 1770035416);
        d = md5_ff(d, a, b, c, x[i + 9], 12, -1958414417);
        c = md5_ff(c, d, a, b, x[i + 10], 17, -42063);
        b = md5_ff(b, c, d, a, x[i + 11], 22, -1990404162);
        a = md5_ff(a, b, c, d, x[i + 12], 7, 1804603682);
        d = md5_ff(d, a, b, c, x[i + 13], 12, -40341101);
        c = md5_ff(c, d, a, b, x[i + 14], 17, -1502002290);
        b = md5_ff(b, c, d, a, x[i + 15], 22, 1236535329);

        a = md5_gg(a, b, c, d, x[i + 1], 5, -165796510);
        d = md5_gg(d, a, b, c, x[i + 6], 9, -1069501632);
        c = md5_gg(c, d, a, b, x[i + 11], 14, 643717713);
        b = md5_gg(b, c, d, a, x[i + 0], 20, -373897302);
        a = md5_gg(a, b, c, d, x[i + 5], 5, -701558691);
        d = md5_gg(d, a, b, c, x[i + 10], 9, 38016083);
        c = md5_gg(c, d, a, b, x[i + 15], 14, -660478335);
        b = md5_gg(b, c, d, a, x[i + 4], 20, -405537848);
        a = md5_gg(a, b, c, d, x[i + 9], 5, 568446438);
        d = md5_gg(d, a, b, c, x[i + 14], 9, -1019803690);
        c = md5_gg(c, d, a, b, x[i + 3], 14, -187363961);
        b = md5_gg(b, c, d, a, x[i + 8], 20, 1163531501);
        a = md5_gg(a, b, c, d, x[i + 13], 5, -1444681467);
        d = md5_gg(d, a, b, c, x[i + 2], 9, -51403784);
        c = md5_gg(c, d, a, b, x[i + 7], 14, 1735328473);
        b = md5_gg(b, c, d, a, x[i + 12], 20, -1926607734);

        a = md5_hh(a, b, c, d, x[i + 5], 4, -378558);
        d = md5_hh(d, a, b, c, x[i + 8], 11, -2022574463);
        c = md5_hh(c, d, a, b, x[i + 11], 16, 1839030562);
        b = md5_hh(b, c, d, a, x[i + 14], 23, -35309556);
        a = md5_hh(a, b, c, d, x[i + 1], 4, -1530992060);
        d = md5_hh(d, a, b, c, x[i + 4], 11, 1272893353);
        c = md5_hh(c, d, a, b, x[i + 7], 16, -155497632);
        b = md5_hh(b, c, d, a, x[i + 10], 23, -1094730640);
        a = md5_hh(a, b, c, d, x[i + 13], 4, 681279174);
        d = md5_hh(d, a, b, c, x[i + 0], 11, -358537222);
        c = md5_hh(c, d, a, b, x[i + 3], 16, -722521979);
        b = md5_hh(b, c, d, a, x[i + 6], 23, 76029189);
        a = md5_hh(a, b, c, d, x[i + 9], 4, -640364487);
        d = md5_hh(d, a, b, c, x[i + 12], 11, -421815835);
        c = md5_hh(c, d, a, b, x[i + 15], 16, 530742520);
        b = md5_hh(b, c, d, a, x[i + 2], 23, -995338651);

        a = md5_ii(a, b, c, d, x[i + 0], 6, -198630844);
        d = md5_ii(d, a, b, c, x[i + 7], 10, 1126891415);
        c = md5_ii(c, d, a, b, x[i + 14], 15, -1416354905);
        b = md5_ii(b, c, d, a, x[i + 5], 21, -57434055);
        a = md5_ii(a, b, c, d, x[i + 12], 6, 1700485571);
        d = md5_ii(d, a, b, c, x[i + 3], 10, -1894986606);
        c = md5_ii(c, d, a, b, x[i + 10], 15, -1051523);
        b = md5_ii(b, c, d, a, x[i + 1], 21, -2054922799);
        a = md5_ii(a, b, c, d, x[i + 8], 6, 1873313359);
        d = md5_ii(d, a, b, c, x[i + 15], 10, -30611744);
        c = md5_ii(c, d, a, b, x[i + 6], 15, -1560198380);
        b = md5_ii(b, c, d, a, x[i + 13], 21, 1309151649);
        a = md5_ii(a, b, c, d, x[i + 4], 6, -145523070);
        d = md5_ii(d, a, b, c, x[i + 11], 10, -1120210379);
        c = md5_ii(c, d, a, b, x[i + 2], 15, 718787259);
        b = md5_ii(b, c, d, a, x[i + 9], 21, -343485551);

        a = safe_add(a, olda);
        b = safe_add(b, oldb);
        c = safe_add(c, oldc);
        d = safe_add(d, oldd);
    }
    return Array(a, b, c, d);

}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t) {
    return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s), b);
}
function md5_ff(a, b, c, d, x, s, t) {
    return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t) {
    return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t) {
    return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t) {
    return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data) {
    var bkey = str2binl(key);
    if (bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

    var ipad = Array(16), opad = Array(16);
    for (var i = 0; i < 16; i++) {
        ipad[i] = bkey[i] ^ 0x36363636;
        opad[i] = bkey[i] ^ 0x5C5C5C5C;
    }

    var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
    return core_md5(opad.concat(hash), 512 + 128);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y) {
    var lsw = (x & 0xFFFF) + (y & 0xFFFF);
    var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
    return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt) {
    return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str) {
    var bin = Array();
    var mask = (1 << chrsz) - 1;
    for (var i = 0; i < str.length * chrsz; i += chrsz)
        bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (i % 32);
    return bin;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin) {
    var str = "";
    var mask = (1 << chrsz) - 1;
    for (var i = 0; i < bin.length * 32; i += chrsz)
        str += String.fromCharCode((bin[i >> 5] >>> (i % 32)) & mask);
    return str;
}

/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray) {
    var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
    var str = "";
    for (var i = 0; i < binarray.length * 4; i++) {
        str += hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8 + 4)) & 0xF) +
            hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8  )) & 0xF);
    }
    return str;
}

/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray) {
    var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var str = "";
    for (var i = 0; i < binarray.length * 4; i += 3) {
        var triplet = (((binarray[i >> 2] >> 8 * ( i % 4)) & 0xFF) << 16)
            | (((binarray[i + 1 >> 2] >> 8 * ((i + 1) % 4)) & 0xFF) << 8 )
            | ((binarray[i + 2 >> 2] >> 8 * ((i + 2) % 4)) & 0xFF);
        for (var j = 0; j < 4; j++) {
            if (i * 8 + j * 6 > binarray.length * 32) str += b64pad;
            else str += tab.charAt((triplet >> 6 * (3 - j)) & 0x3F);
        }
    }
    return str;
}
