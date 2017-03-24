<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>收银员</title>
<?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('seller').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('seller_css','perfect-scrollbar.min.css','css');?>
<?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>

<?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('seller_js','perfect-scrollbar.min.js','js');?>

</head>
<body>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>收银员管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/cashier?site_id='.$arrParam['site_id'];?>"><span>收银员列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加收银员</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo SELLER_SITE_URL.'/cashier/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="site_id">加油站:</label></td>
        </tr>
        <tr class="noborder">
          <td colspan="2">
            <select name="site_id" id="site_id" class="querySelect">
              <option value="">全部</option>
              <?php foreach($site_list as $k=>$v):?>
              <option value="<?php echo $v['id']?>"<?php if(!empty($info) && $info['site_ids']==$v['id']) echo ' selected'; else echo !empty($arrParam['site_id'])&&$arrParam['site_id']==$v['id']?' selected':'';?>><?php echo $v['site_name']?></option>
            <?php endforeach;?>
            </select>
          </td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="name">姓名:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="name" name="name" class="txt" value="<?php echo !empty($info)?$info['name']:'';?>" ></td>
          <td class="vatop tips">请输入姓名</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="mobile">手机:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="mobile" name="mobile" class="txt" value="<?php echo !empty($info)?$info['mobile']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="user_name">用户名:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <?php if(!empty($info)):
              echo $info['username'];
             else:?>
              <input type="text" value="" name="user_name" id="user_name" class="txt">
            <?php endif;?>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="user_pwd">密码:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="user_pwd" name="user_pwd" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><label for="status">状态: </label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <input type="radio" name="status"  id="status" value="1" <?php if ( empty($info['status']) || (isset($info['status']) && $info['status'] == 1)){?>checked="checked"<?php }?>>正常　
            <input type="radio" name="status"  id="status" value="2" <?php if (isset($info['status']) && $info['status'] == 2){?>checked="checked"<?php }?>>禁用</td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>

      <tfoot>
        <tr class="tfoot">
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/jquery.ui.js','js');?>
<script>
//按钮先执行验证再提交表
$(document).ready(function(){
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#add_form").valid()){
	     $("#add_form").submit();
		}
	});
	
	$("#add_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
          site_id : {
              required : true
          },
        	name : {
              required : true,
			        maxlength: 20
          },
          mobile : {
              required : true,
			        minlength: 6,
			        maxlength: 20
          },
          user_name : {
            required : true,
            remote   : {                
              url :"/seller/cashier/ajax_check_name",
              type:'get',
              data:{
                user_name : function(){
                      return $('#user_name').val();
                  },
                }
              }
          }
      
        },
        messages : {
          site_id : {
              required : '加油站不能为空'
          },
          name : {
              required : '姓名不能为空'
          },
          mobile : {
              required : '手机号不能为空',
          },
          user_name : {
              required : '用户名不能为空',
              remote   : '用户名已存在'
          }

        }
	});
});
</script>

</body>
</html>
