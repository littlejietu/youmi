<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>
<?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>

<?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('admin_js','perfect-scrollbar.min.js','js');?>

</head>
<body>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo lang('nc_operation_set')?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo lang('nc_operation_set');?></span></a></li>
        
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" name="settingForm" id="settingForm" action="<?php echo ADMIN_SITE_URL.'/operation/save'?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        
        <tr>
          <td colspan="2" class="required">站点名称: </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <input name="site_name"  value="<?php if(!empty($list['site_name'])) echo $list['site_name'];?>" type="text" >
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required">公众号第三方平台AppID: </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <input name="component_appid"  value="<?php if(!empty($list['component_appid'])) echo $list['component_appid'];?>" type="text" >
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required">公众号第三方平台AppSecret: </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <input name="component_appsecret"  value="<?php if(!empty($list['component_appsecret'])) echo $list['component_appsecret'];?>" type="text" >
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required">公众号消息校验Token: </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <input name="component_message_token"  value="<?php if(!empty($list['component_message_token'])) echo $list['component_message_token'];?>" type="text" >
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required">公众号消息加解密Key: </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <input name="component_message_key"  value="<?php if(!empty($list['component_message_key'])) echo $list['component_message_key'];?>" type="text" >
          <td class="vatop tips"></td>
        </tr>
        
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo lang('nc_submit');?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

<script src="<?php echo _get_cfg_path('lib')?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>

<script type="text/javascript">
<?php $timestamp = time();?>
$(function() {
  upload_file('start_ad','start_ad','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
});
</script>
<script>

$(function(){$("#submitBtn").click(function(){
    if($("#settingForm").valid()){
     $("#settingForm").submit();
  }
  });
});
//
$(document).ready(function(){
  $("#settingForm").validate({
    errorPlacement: function(error, element){
      error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
        },
        messages : {
        }
  });
});
</script>
</body>
</html>