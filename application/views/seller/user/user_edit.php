<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<?php echo _get_html_cssjs('seller_js','area_array.js','js');?>
<link href="<?php echo _get_cfg_path('seller').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('seller_css','perfect-scrollbar.min.css','css');?>
<?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>

<?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('seller_js','perfect-scrollbar.min.js','js');?>

</head>
<body>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>会员管理</h3>
      <ul class="tab-base">
        <li><a href="<?php echo SELLER_SITE_URL.'/user'?>" ><span>管理</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span>编辑</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="user_form" enctype="multipart/form-data" method="post" >
    <input type="hidden" name="member_id" value="<?php if (!empty($info['user_id'])) echo $info['user_id'];?>" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label>会员:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php if (!empty($info['user_name'])) echo $info['user_name'];?></td>
          <td class="vatop tips"></td>
        </tr>
       
        <tr>
          <td colspan="2" class="required"><label for="member_passwd">密码:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="member_passwd" name="member_passwd" class="txt"></td>
          <td class="vatop tips">留空表示不修改密码</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="nickname">昵称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['nickname'])) echo $info['nickname'];?>" id="nickname" name="nickname" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="member_truename">姓名:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['name'])) echo $info['name'];?>" id="member_truename" name="member_truename" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="mobile">手机:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['mobile'])) echo $info['mobile'];?>" id="mobile" name="mobile" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="birthday">生日:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><ul>
            <li>
              <label for="birthday"></label><input type="text" name="birthday_time" id="birthday_time" value="<?php echo !empty($info['birthday'])?date('Y-m-d',$info['birthday']):'';?>">
            </li>
          </ul></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label>性别:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><ul>
            <li>
              <input type="radio" <?php if($info['sex'] == 0){ ?>checked="checked"<?php } ?> value="0" name="member_sex" id="member_sex0">
              <label for="member_sex0">保密</label>
            </li>
            <li>
              <input type="radio" <?php if($info['sex'] == 1){ ?>checked="checked"<?php } ?> value="1" name="member_sex" id="member_sex1">
              <label for="member_sex1">男</label>
            </li>
            <li>
              <input type="radio" <?php if($info['sex'] == 2){ ?>checked="checked"<?php } ?> value="2" name="member_sex" id="member_sex2">
              <label for="member_sex2">女</label>
            </li>
          </ul></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="car_no">车牌号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['car_no'])) echo $info['car_no'];?>" id="car_no" name="car_no" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="car_model">车型:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['car_model'])) echo $info['car_model'];?>" id="car_model" name="car_model" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
       <tr>
          <td colspan="2" class="required"><label for="invoice_title">发票抬头:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['invoice_title'])) echo $info['invoice_title'];?>" id="invoice_title" name="invoice_title" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
 
      
        <tr>
          <td colspan="2" class="required"><label>是否正式会员:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><ul>
            <li>
              <input type="radio" <?php if($info['member_status'] == 0){ ?>checked="checked"<?php } ?> value="0" name="member_status" id="member_status1">
              <label for="member_status1">不是</label>
            </li>
            <li>
              <input type="radio" <?php if($info['member_status'] == 1){ ?>checked="checked"<?php } ?> value="1" name="member_status" id="member_status2">
              <label for="member_status2">是</label>
            </li>
            <li>
              <input type="radio" <?php if($info['member_status'] == 2){ ?>checked="checked"<?php } ?> value="2" name="member_status" id="member_status3">
              <label for="member_status3">不通过</label>
            </li>
            <li>
              <input type="radio" <?php if($info['member_status'] == 3){ ?>checked="checked"<?php } ?> value="3" name="member_status" id="member_status4">
              <label for="member_status4">申请</label>
            </li>
          </ul></td>
          <td class="vatop tips"></td>
        </tr>
        

       

        
        <tr>
          <td colspan="2" class="required"><label>账户状态:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
          	<label for="memberstate_1" class="cb-enable <?php if($info['status'] == '1'){ ?>selected<?php } ?>" ><span>正常</span></label>
            <label for="memberstate_2" class="cb-disable <?php if($info['status'] == '2'){ ?>selected<?php } ?>" ><span>锁定</span></label>
            <input id="memberstate_1" name="memberstate"  <?php if($info['status'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="memberstate_2" name="memberstate" <?php  if($info['status'] == '2'){ ?>checked="checked"<?php } ?> value="2" type="radio"></td>
          <td class="vatop tips"></td>
        </tr>

      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a></td>
        </tr>
      </tfoot>
    </table>
      
  </form>
</div>
<script src="<?php echo _get_cfg_path('lib')?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>

<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/i18n/zh-CN.js,jquery-ui/jquery.ui.js','js');?>
<script type="text/javascript">
$(function(){
    $('#birthday_time').datepicker({dateFormat: 'yy-mm-dd'});
});
</script>
<script type="text/javascript">

$(function(){
$("#submitBtn").click(function(){
    if($("#user_form").valid()){
     $("#user_form").submit();
	}
	});
    $('#user_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            member_passwd: {
                maxlength: 20,
                minlength: 6
            },
        },
        messages : {
            member_passwd : {
                maxlength: '密码长度应在6-20个字符之间',
                minlength: '密码长度应在6-20个字符之间'
            },
        }
    });
});
</script> 

</body>
</html>
