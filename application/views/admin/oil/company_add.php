<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>

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
      <h3>公司管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo ADMIN_SITE_URL.'/company';?>"><span>公司列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加公司</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo ADMIN_SITE_URL.'/company/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="company">公司名称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="company" name="company" class="txt" value="<?php echo !empty($info)?$info['company']:'';?>" ></td>
          <td class="vatop tips">请输入公司名称</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="company_long">公司全称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="company_long" name="company_long" class="txt" value="<?php echo !empty($info)?$info['company_long']:'';?>"></td>
          <td class="vatop tips">请输入公司全称</td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="user_name">管理员:</label></td>
        </tr>
        
        <tr class="noborder">
          <td class="vatop rowform">
            <?php if(empty($info)):?>
              <input type="text" value="" name="user_name" id="user_name" class="txt">
            <?php else:?>
              <?php echo $info['username'];?><input type="hidden" value="<?php echo $info['admin_id'];?>" name="admin_id">
            <?php endif;?>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2"><label for="user_pwd">密码:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="user_pwd" name="user_pwd" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="wx_appid">微信Appid:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="wx_appid" name="wx_appid" class="txt" value="<?php echo !empty($info)?$info['wx_appid']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="wx_mchid">微信子商户id:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="wx_mchid" name="wx_mchid" class="txt" value="<?php echo !empty($info)?$info['wx_mchid']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="product_id">套餐产品:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          <select name="product_id">
            <option value="0">请选择套餐</option>
          <?php foreach($product_list as $v){?>
            <option value="<?php echo $v['id'];?>"<?php if(!empty($info) && $info['product_id']==$v['id']) echo ' selected';?>><?php echo $v['name'];?></option>
          <?php }?>
          </select>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="prd_start_time">产品开始时间:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['prd_start_time'])) echo date('Y-m-d',$info['prd_start_time']);?>" name="prd_start_time" id="prd_start_time" class="txt date"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="prd_end_time">产品结束时间:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php if (!empty($info['prd_end_time'])) echo date('Y-m-d',$info['prd_end_time']);?>" name="prd_end_time" id="prd_end_time" class="txt date"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="linkman">联系人:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="linkman" name="linkman" class="txt" value="<?php echo !empty($info)?$info['linkman']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="phone">电话:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="phone" name="phone" class="txt" value="<?php echo !empty($info)?$info['phone']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tbody id="title_status">
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
        	company : {
                required : true,
                
				maxlength: 20,
                remote   : {                
                url :"/admin/admin/ajax_check_name",
                type:'get',
                data:{
                	company : function(){
                        return $('#company').val();
                    },
                  }
                }
            },
            company_long : {
                required : true,
				minlength: 6,
				maxlength: 20
            },
            company_long : {
                required : true,
                equalTo  : '#company_long'
            },
            gid : {
                required : true
            }        
        },
        messages : {
            company : {
                required : '公司名称不能为空',

				remote	 : '该公司名称已存在'
            },
            company_long : {
                required : '公司全称不能为空',
            },

        }
	});
});
</script>
<script type="text/javascript">
$(function(){
    $('#prd_start_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#prd_end_time').datepicker({dateFormat: 'yy-mm-dd'});


    // $('#ap_id').change(function(){
    //  var select   = document.getElementById("ap_id");
    // });
});
</script>
</body>
</html>
