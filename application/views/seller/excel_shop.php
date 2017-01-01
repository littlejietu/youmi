<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家中心</title>
<?php echo _get_html_cssjs('seller_css','base.css,seller_center.css,perfect-scrollbar.min.css,jquery.qtip.min.css','css');?>
<?php echo _get_html_cssjs('font','font-awesome/css/font-awesome.min.css','css');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
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
      <div class="column-title" id="main-nav"><span class="ico-goods"></span>
        <h2>商品</h2>
      </div>
      <div class="column-menu">
        <ul id="seller_center_left_menu">
            <li class="<?php if(empty($arrParam['status'])) echo 'current';?>"> <a href="<?php echo SELLER_SITE_URL;?>/excel/export_by_shop"> 导出销售报表 </a> </li>
        </ul>
      </div>
      <div class="column-menu">
        <ul id="seller_center_left_menu">
        </ul>
      </div>
    </div>
  </div>
  <div id="layoutRight" class="ncsc-layout-right">
    <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>销售报表<i class="icon-angle-right"></i>导出销售报表</div>
    <div class="main-content" id="mainContent">
<div class="tabmenu">
  <ul class="tab pngFix">
  <li class="active"><a href="<?php echo SELLER_SITE_URL;?>/excel/export_by_shop">导出销售报表</a></li></ul>
</div>
<form id="adv_form" method="post" action="<?php echo SELLER_SITE_URL.'/excel/export_excel_shop'?>">
<tr>
          <td colspan="2" class="required"><label class="validation" for="start_time"><?php echo lang('start_time');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" name="start_time" id="start_time" class="txt date"></td>
          <td class="vatop tips"></td>
         </tr> 
         <tr>
          <td colspan="2" class="required"><label class="validation" for="end_time"><?php echo lang('end_time');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" name="end_time" id="end_time" class="txt date"></td>
          <td class="vatop tips"></td>
         </tr> 
         <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo lang('field_name');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" name="field_name" id="field_name" class="txt" value=""></td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo lang('export');?></span></a></td>
        </tr>
      </tfoot>
      </form>
  </div>
</div>

<?php echo _get_html_cssjs('seller_js','common_select.js,jquery.mousewheel.js,shop_goods_add.step1.js,jquery.cookie.js,perfect-scrollbar.min.js,jquery.qtip.min.js,compare.js,store_goods_list.js,jquery.poshytip.min.js','js');?>
<?php $this->load->view('seller/inc/footer');?>

<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/jquery.ui.js','js');?>
<script type="text/javascript">
$(function(){
    $('#start_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#end_time').datepicker({dateFormat: 'yy-mm-dd'});

});
</script>

<script type="text/javascript">
$(function(){
    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#adv_form").valid()){
            $("#adv_form").submit();
        }
    });

    $('#adv_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.parentsUntil('tr').parent().prev().find('td:first'));
        },
        rules : {
            start_time: {
                required : true,
            },
            end_time: {
                required : true,
            },
        },
        messages : {
        	start_time: {
                required: '请选择开始日期',
            },
            end_time: {
                required: '请选择截止日期',
            },
        }
    });
});
</script>
