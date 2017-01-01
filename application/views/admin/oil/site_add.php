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
      <h3>加油站管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo ADMIN_SITE_URL.'/site?company_id='.(!empty($company_id)?$company_id:0);?>"><span>加油站列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加加油站</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo ADMIN_SITE_URL.'/site/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr>
          <td colspan="2" class="required"><label for="company_id">所属公司:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <?php if(!empty($company_id)):?>
              <input type="hidden" value="<?php echo $company_id;?>" name="company_id" />
              <?php echo $company_name;?>
            <?php else:?>
              <select name="company_id">
                <?php foreach ($company_list as $key => $value):?>
                <option value="<?php echo $value['id']?>"<?php if(!empty($info)&&$info['company_id']==$value['id']) echo ' selected';?>><?php echo $value['company']?></option>
                <?php endforeach;?>
              </select>
            <?php endif;?>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="site_name">油站名称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="site_name" name="site_name" class="txt" value="<?php echo !empty($info)?$info['site_name']:'';?>" ></td>
          <td class="vatop tips">请输入油站名称</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="site_long">油站全称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="site_long" name="site_long" class="txt" value="<?php echo !empty($info)?$info['site_long']:'';?>"></td>
          <td class="vatop tips">请输入油站全称</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="public_name">公众号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="public_name" name="public_name" class="txt" value="<?php echo !empty($info)?$info['public_name']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="reg_address">注册地址:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="reg_address" name="reg_address" class="txt" value="<?php echo !empty($info)?$info['reg_address']:'';?>"></td>
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
        <tr>
          <td colspan="2" class="required"><label for="email">邮箱:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="email" name="email" class="txt" value="<?php echo !empty($info)?$info['email']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="qq">QQ:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="qq" name="qq" class="txt" value="<?php echo !empty($info)?$info['qq']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="product_license">成品油许可证:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <div class="upload_block">
              <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                <div class="type-file-preview"><img id="preview_img" src="<?php if (!empty($info['product_license'])) echo BASE_SITE_URL.'/'.$info['product_license'];?>" onload="javascript:DrawImage(this,500,500);"></div>
              </span>
              <div class="f_note">
                  <input type="hidden"  name="img" id="img" value="<?php if( !empty($info['img']) ) echo $info['img']; else echo ''; ?>">
                  <em><i class="icoPro16"></i></em>
                  <div class="file_but">
                      <input type="hidden" name="orig_img" value="<?php if( !empty($info['product_license']) ) echo $info['product_license']?>"><input id="img_upload" name="img_upload" value="上传图片" type="file" >
                  </div>
              </div>
            </div>
          </td>
          <td class="vatop tips">系统支持的图片格式为 gif,jpg,jpeg,png</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="retail_license">零售许可证:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <div class="upload_block">
              <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                <div class="type-file-preview"><img id="retail_license_preview_img" src="<?php if (!empty($info['retail_license'])) echo BASE_SITE_URL.'/'.$info['retail_license'];?>" onload="javascript:DrawImage(this,500,500);"></div>
              </span>
              <div class="f_note">
                  <input type="hidden"  name="retail_license_img" id="retail_license_img" value="<?php if( !empty($info['img']) ) echo $info['img']; else echo ''; ?>">
                  <em><i class="icoPro16"></i></em>
                  <div class="file_but">
                      <input type="hidden" name="retail_license_orig_img" value="<?php if( !empty($info['retail_license']) ) echo $info['retail_license']?>"><input id="retail_license_img_upload" name="retail_license_img_upload" value="上传图片" type="file" >
                  </div>
              </div>
            </div>
          </td>
          <td class="vatop tips">系统支持的图片格式为 gif,jpg,jpeg,png</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="risk_license">危化证:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <div class="upload_block">
              <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                <div class="type-file-preview"><img id="risk_license_preview_img" src="<?php if (!empty($info['risk_license'])) echo BASE_SITE_URL.'/'.$info['risk_license'];?>" onload="javascript:DrawImage(this,500,500);"></div>
              </span>
              <div class="f_note">
                  <input type="hidden"  name="risk_license_img" id="risk_license_img" value="<?php if( !empty($info['img']) ) echo $info['img']; else echo ''; ?>">
                  <em><i class="icoPro16"></i></em>
                  <div class="file_but">
                      <input type="hidden" name="risk_license_orig_img" value="<?php if( !empty($info['risk_license']) ) echo $info['risk_license']?>"><input id="risk_license_img_upload" name="risk_license_img_upload" value="上传图片" type="file" >
                  </div>
              </div>
            </div>
          </td>
          <td class="vatop tips">系统支持的图片格式为 gif,jpg,jpeg,png</td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><label for="net_id">网点id:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="net_id" name="net_id" class="txt" value="<?php echo !empty($info)?$info['net_id']:'';?>"></td>
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
        	site_name : {
                required : true,

				maxlength: 20,
                remote   : {                
                url :"/admin/admin/ajax_check_name",
                type:'get',
                data:{
                	site_name : function(){
                        return $('#site_name').val();
                    },
                  }
                }
            },
            site_long : {
                required : true,
				minlength: 6,
				maxlength: 20
            },
            site_long : {
                required : true,
                equalTo  : '#site_long'
            },
            gid : {
                required : true
            }        
        },
        messages : {
            site_name : {
                required : '加油站名称不能为空'
            },
            site_long : {
                required : '加油站全称不能为空',
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
<script src="<?php echo _get_cfg_path('lib')?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<script type="text/javascript">
<?php $timestamp = time();?>
$(function() {
  upload_file('img','img','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
  upload_file('retail_license_img','img','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
  upload_file('risk_license_img','img','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
});
</script>
</body>
</html>
