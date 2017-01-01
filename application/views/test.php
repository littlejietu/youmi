<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>商家管理中心登录</title>
    <?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js','js');?>
    <?php echo _get_html_cssjs('seller_css','base.css,seller_center.css,font-awesome.min.css','css');?>
    <script language="JavaScript" type="text/javascript">
        window.onload = function() {
            tips = new Array(2);
            tips[0] = document.getElementById("loginBG01");
            tips[1] = document.getElementById("loginBG02");
            index = Math.floor(Math.random() * tips.length);
            tips[index].style.display = "block";
        };
    </script>
</head>
<body>
<div id="loginBG01" class="ncsc-login-bg">
    <p class="pngFix"></p>
</div>
<div id="loginBG02" class="ncsc-login-bg">
    <p class="pngFix"></p>
</div>
<div class="ncsc-login-container">
    <div class="ncsc-login-title">
        <h2>商家管理中心</h2>
    <span>请输入您注册商铺时申请的商家名称<br/>
    登录密码为商城用户通用密码</span></div>
    <form id="form_login" action="/api/common/reg_deliver" method="post" >
        <input type="hidden" value="android" name="client_type">
        <input type="hidden" value="1" name="platform_id">
        <div class="input">
            <label>用户名</label>
            <span class="repuired"></span>
            <input name="uin" type="text" autocomplete="off" class="text" autofocus>
            <span class="ico"><i class="icon-user"></i></span> </div>
        <div class="input">
            <label>验证码</label>
            <span class="repuired"></span>
            <input type="text" name="code" id="captcha" onclick="onCaptchaShow();" autocomplete="off" class="text" style="width: 80px;" maxlength="4" size="10" />
            <div class="code">
                <div class="arrow"></div>
                <div class="code-img">
                    <a href="javascript:void(0)" nctype="btn_change_seccode"><img src="Login/captcha?<?php echo rand(10000,9999);?>" onclick="this.src='Login/captcha?'+Math.random()" name="codeimage" border="0" id="codeimage"></a>
                </div>
                <a href="JavaScript:void(0);" id="hide" onclick="onCaptchaHide();" class="close" title=""><i></i></a>
                <a href="JavaScript:void(0);" class="change" onclick="onCaptchaUp()" nctype="btn_change_seccode" title=""><i></i></a>
            </div>
            <span class="ico"><i class="icon-qrcode"></i></span>
            <input type="submit" class="login-submit" value="商家登录">
        </div>
    </form>
    <script>
        function onCaptchaShow(){
            $(".code").show();
        }
        function onCaptchaHide(){
            $(".code").hide();
        }
        function onCaptchaUp(){
            $("#codeimage").attr('src','Login/captcha?'+Math.random());
        }

        /**
         * ajax通用方法（同步模式）
         *
         * @parm:       url--请求的地址（注意要用相对地址）
         *              parmArr--JOSN格式（{act:"getLeft",id:45,cid:145}）
         * @autor:
         * @createtime: 2014-08-18
         */
        function ajaxMain(url,parmArr){
            if(url != ''){
                var rel = $.ajax({
                    //dataType:"json",
                    type: "post",
                    url: url,
                    async:false,
                    data:parmArr,
                    success: function (data) {
                        return data;
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                }).responseText;
            }
            return rel;
        }

    </script>
</div>
</body>
</html>
