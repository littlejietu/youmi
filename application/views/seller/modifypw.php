<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>商家中心</title>
    <?php echo _get_html_cssjs('seller_css','base.css,seller_center.css,perfect-scrollbar.min.css,jquery.qtip.min.css','css');?>
    <?php echo _get_html_cssjs('font','font-awesome/css/font-awesome.min.css','css');?>
    <!--[if IE 7]>
    <?php echo _get_html_cssjs('font','font-awesome/font-awesome-ie7.min.css','css');?>
    <![endif]-->
    <script>
        var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';
        var _CHARSET = '<?php echo strtolower(CHARSET);?>';
        var SITEURL = '<?php echo BASE_SITE_URL;?>';
    </script>
    <?php echo _get_html_cssjs('seller_js','jquery.js,seller.js,waypoints.js,jquery-ui/jquery.ui.js,jquery.validation.min.js,common.js,member.js','js');?>
    <script type="text/javascript" src="<?php echo _get_cfg_path('lib');?>dialog/dialog.js" id="dialog_js" charset="utf-8"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <?php echo _get_html_cssjs('seller_js','html5shiv.js,respond.min.js','js');?>
    <![endif]-->
    <!--[if IE 6]>
    <?php echo _get_html_cssjs('seller_js','IE6_MAXMIX.js,IE6_PNG.js','js');?>
    <script>
        DD_belatedPNG.fix('.pngFix');
    </script>
    <script>
        // <![CDATA[
if((window.navigator.appName.toUpperCase().indexOf("MICROSOFT")>=0)&&(document.execCommand))
try{
document.execCommand("BackgroundImageCache", false, true);
   }
catch(e){}
// ]]>
</script>
<![endif]-->
</head>

<body>
<?php echo _get_html_cssjs('seller_js','ToolTip.js','js');?>
<div id="toolTipLayer" style="position: absolute; z-index: 999; display: none; visibility: visible; left: 172px; top: 365px;"></div>
<?php $this->load->view('seller/inc/header');?>
<div class="ncsc-layout wrapper">
    <div id="layoutLeft" class="ncsc-layout-left">
        <div id="sidebar" class="sidebar">
            <div class="column-title" id="main-nav"><span class="ico-order"></span>
                <h2>安全中心</h2>
            </div>

        </div>
    </div>
    <div id="layoutRight" class="ncsc-layout-right">
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>安全中心<i class="icon-angle-right"></i>修改密码</div>
        <div class="main-content" id="mainContent">

            <div class="page">
                <form id="admin_form" method="post" action='<?php echo SELLER_SITE_URL.'/seller/modifypw'?>' name="adminForm">
                    <input type="hidden" name="form_submit" value="ok" />
                    <table class="table tb-type2">
                        <tbody>
                        <tr class="noborder">
                            <td colspan="2" class="required"><label class="validation" for="old_pw">原密码<!-- 原密码 -->:</label></td>
                        </tr>
                        <tr class="noborder">
                            <td class="vatop rowform"><input id="old_pw" name="old_pw" class="infoTableInput" type="password"></td>
                            <td class="vatop tips"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="required"><label class="validation" for="new_pw">新密码<!-- 新密码 -->:</label></td>
                        </tr>
                        <tr class="noborder">
                            <td class="vatop rowform"><input id="new_pw" name="new_pw" class="infoTableInput" type="password"></td>
                            <td class="vatop tips"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="required"><label class="validation" for="new_pw2">确认密码<!-- 确认密码-->:</label></td>
                        </tr>
                        <tr class="noborder">
                            <td class="vatop rowform"><input id="new_pw2" name="new_pw2" class="infoTableInput" type="password"></td>
                            <td class="vatop tips"></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr class="tfoot">
                            <td colspan="2" >
                                <br /><!--<a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a>-->
                                <input type="submit"  class="btn" id="submitBtn" />
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
            <script>
                //按钮先执行验证再提交表单
                $("#submitBtn").click(function(){

                    $("#admin_form").submit();
                });
                //
                $(document).ready(function(){
                    $("#admin_form").validate({
                        errorPlacement: function(error, element){
                            error.appendTo(element.parent().parent().prev().find('td:first'));
                        },
                        rules : {
                            old_pw : {
                                required : true
                            },
                            new_pw : {
                                required : true,
                                minlength: 6,
                                maxlength: 20
                            },
                            new_pw2 : {
                                required : true,
                                minlength: 6,
                                maxlength: 20,
                                equalTo: '#new_pw'
                            }
                        },
                        messages : {
                            old_pw : {
                                required : '<?php echo $lang['admin_add_password_null'];?>'
                            },
                            new_pw : {
                                required : '<?php echo $lang['admin_add_password_null'];?>',
                                minlength: '<?php echo $lang['admin_add_password_max'];?>',
                                maxlength: '<?php echo $lang['admin_add_password_max'];?>'
                            },
                            new_pw2 : {
                                required : '<?php echo $lang['admin_add_password_null'];?>',
                                minlength: '<?php echo $lang['admin_add_password_max'];?>',
                                maxlength: '<?php echo $lang['admin_add_password_max'];?>',
                                equalTo:   '<?php echo $lang['admin_edit_repeat_error'];?>'
                            }
                        }
                    });
                });
            </script>







            <?php echo _get_html_cssjs('seller_js','jquery.poshytip.min.js,/jquery-ui/i18n/zh-CN.js','js');?>
        </div>
    </div>
</div>
<?php echo _get_html_cssjs('seller_js','common_select.js,jquery.mousewheel.js,shop_goods_add.step1.js,jquery.cookie.js,perfect-scrollbar.min.js,jquery.qtip.min.js,compare.js,store_goods_list.js,jquery.poshytip.min.js','js');?>
<?php $this->load->view('seller/inc/footer');?>
</body>
</html>
