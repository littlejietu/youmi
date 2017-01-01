/**
 * Created by mirong on 2016/11/8.
 */
var CONFIG_DEBUG = true;
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




function testMobile(str) {
    return /^1[3|4|5|7|8]\d{9}$/.test(str)
}
function get_total_page(count, pagesize) {
    var total = parseInt((parseInt(count) + parseInt(pagesize) - 1) / parseInt(pagesize));
    return total;
}


var urlToken;
function get_user_token() {
    if (getTokenFromUrl()) {
        return urlToken;
    }
    return get_user_data_from_local('token');
}


function getTokenFromUrl() {
    if (!urlToken) {
        urlToken = getUrlParam('token');
    }
    return urlToken;
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
    var key = get_string_fromlocal('number9streetkey');
    return key;
}

function isNeedToken(url) {
    if (url.indexOf('api/cashier/login') >= 0) {
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
function sendPostData(obj, url, fun, errfun) {
    //$('#loading_inmation').show();
    var temp = url.split('/api/')[1];
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
                location.href = CashierSiteUrl+'/login.html';
                return;
            }
            fun(res);
            $('#loading_inmation').hide();

        },
        error: function (res) {
            $('#loading_inmation').hide();
            errfun(res);
        }
    });
    delete obj.sign;
    delete obj.timestamp;
    delete obj.token;
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
    location.href = WapSiteUrl + '/index.html';
}


function errorHnadler(XMLHttpRequest, textStatus, errorThrown) {
    //alert(XMLHttpRequest)
    if(XMLHttpRequest.status == 200){
        tipsAlert('好像哪里不对。。。')
    }else{

    }

}

function getUserInfo(fun){
    var user = get_user_data_from_local('cashierInfo');
    if(user){
        if(fun){
            fun(user);
        }
        return user;
    }
    sendPostData({}, ApiUrl + "m/user/get", function (result) {
        if (result.code == 1 || result.code=='SUCCESS') {
            save_user_data_to_local('userInfo', result.data);
            if(fun){
                fun(result.data);
            }
            return result.data;
        }else{
            tipsAlert(result.msg);
        }
    });
}


function save_string_tolocal(key, value) {
    if (window.localStorage) {
        localStorage.setItem(key, value);
    }else{
        $.cookie(key,value,CookieOption);
    }
}

function get_string_fromlocal(key) {
    if (window.localStorage) {
        return localStorage.getItem(key);
    }else{
        return  $.cookie(key);
    }


}

function save_json_tolocal(key, value) {
    var str = JSON.stringify(value);
    if (window.localStorage) {
        localStorage.setItem(key, str);
    }else{
        $.cookie(key,str,CookieOption);
    }
    return 1;//保存数据失败
}

function get_json_fromlocal(key) {
    var json = "";
    if (window.localStorage) {
        //var str = JSON.stringify(value);
        json = localStorage.getItem(key);
    }else{
        json = $.cookie(key);
    }

    if (json!="") {
        if (typeof (JSON) == 'object' && JSON.parse)
            return JSON.parse(json);
        else
            return json_parse(json);
    }
    return "";//未找到数据
}

function remove_json_fromlocal(key) {
    if (window.localStorage) {
        //var str = JSON.stringify(value);
        var json = localStorage.removeItem(key)
        return 1;

    }else{
        $.cookie(key,null);
    }
    return 0;//删除失败
}




var tips_content = '<div class="cancel-mask2" style="display:block;">'
    +'<div class="cancel-outMask10" >'
    +'<div class="cancel-wrapper">'
    +'<h3>温馨提示</h3>'
    +'<div class="div1" style="text-align:center;position:relative;">'
    +'<img src="http://'+window.location.host+'/wap/mine/images/pic.png" style="position:absolute;width:42px;height:40px;top:10px;left:30px;margin-right:5px;"/>'
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

function tipsAlertClose(){
    if(tipsinit){
        $('.cancel-mask2').hide();
    }
}

var tips_content1 = '<div class="cancel-mask" style="display:none;">'
    +'  <div class="cancel-outMask" >'
    +'      <div class="cancel-wrapper">'
    +'          <h3>温馨提示</h3>'
    +'          <div class="div1" style="text-align:center;position:relative;">'
    +'              <img src="http://'+window.location.host+'/wap/mine/images/pic.png" style="position:absolute;width:42px;height:40px;top:10px;left:30px;margin-right:5px;"/>'
    +'              <div id="msg_content" style="padding-left:80px;line-height:20px;text-align:left;">确定要取消这个订单吗？</div>'
    +'          </div>'
    +'          <p class="p1"><button class="btn1 details4-btn1" id="tips2_sure_btn">确定</button><button  class="btn2" id="tips2_cancel_btn">我再想想</button></p>'
    +'      </div>'
    +'  </div>'
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
