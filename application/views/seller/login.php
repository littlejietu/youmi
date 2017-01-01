<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>油站管理中心登录</title>
    <?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,common.js','js');?>
    <?php echo _get_html_cssjs('seller_css','base.css,seller_center.css','css');?>
    <?php echo _get_html_cssjs('font','font-awesome/css/font-awesome.min.css','css');?>
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
  </div>
  <form id="form_login" action="<?php echo SELLER_SITE_URL;?>/login/login" method="post" >
    <div class="input">
      <label>用户名</label>
      <span class="repuired"></span>
      <input name="user_name" id="user_name" type="text" autocomplete="off" class="text" autofocus>
      <span class="ico"><i class="icon-user"></i></span> </div>
    <div class="input">
      <label>密码</label>
      <span class="repuired"></span>
      <input name="pwd" type="password" autocomplete="off" class="text">
      <span class="ico"><i class="icon-key"></i></span> </div>
    <div class="input">
      <label>验证码</label>
      <span class="repuired"></span>
      <input type="text" name="captcha" id="captcha" onclick="onCaptchaShow();" autocomplete="off" class="text" style="width: 80px;" maxlength="4" size="10" />
      <div class="code">
        <div class="arrow"></div>
        <div class="code-img">
            <a href="javascript:void(0)" nctype="btn_change_seccode"><img src="<?php echo BASE_SITE_URL;?>/public/common/captcha_seller?<?php echo rand(10000,9999);?>" onclick="this.src='<?php echo BASE_SITE_URL;?>/public/common/captcha_seller?'+Math.random()" name="codeimage" border="0" id="codeimage"></a>
        </div>
        <a href="JavaScript:void(0);" id="hide" onclick="onCaptchaHide();" class="close" title=""><i></i></a>
          <a href="JavaScript:void(0);" class="change" onclick="onCaptchaUp()" nctype="btn_change_seccode" title=""><i></i></a>
      </div>
      <span class="ico"><i class="icon-qrcode"></i></span>
      <input type="submit" class="login-submit" value="商家登录">
    </div>
  </form>
  <script>

  $("#captcha").focus(function(){
        $(".code").fadeIn("fast");
  });
  $("#captcha").nc_placeholder();

  function onCaptchaShow(){
      $(".code").show();
  }
  function onCaptchaHide(){
    $(".code").hide();
  }
  function onCaptchaUp(){
    $("#codeimage").attr('src','<?php echo BASE_SITE_URL;?>/public/common/captcha_seller?'+Math.random());
  }

  if(top.location!=this.location) top.location=this.location;
  $('#user_name').focus();

  </script>
</div>
</body>
</html>
