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
      <h3>添加礼品</h3>
      <ul class="tab-base">
        <li><a href="<?php echo SELLER_SITE_URL.'/gift';?>"><span>礼品列表</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span>添加礼品</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/gift/change';?>"><span>兑换列表</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo SELLER_SITE_URL.'/gift/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="name">名称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="name" name="name" class="txt" value="<?php echo !empty($info)?$info['name']:'';?>" ></td>
          <td class="vatop tips">请输入名称</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="org_price">原价:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="org_price" name="org_price" class="txt" value="<?php echo !empty($info)?$info['org_price']:'';?>"></td>
          <td class="vatop tips">请输入原价</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="integral">积分:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="integral" name="integral" class="txt" value="<?php echo !empty($info)?$info['integral']:'';?>"></td>
          <td class="vatop tips">请输入积分</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="no">礼品编号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="no" name="no" class="txt" value="<?php echo !empty($info)?$info['no']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>

        <tr>
          <td colspan="2" class="required"><label class="validation" for="stock_num">库存:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="stock_num" name="stock_num" class="txt" value="<?php echo !empty($info)?$info['stock_num']:'';?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="img">图片:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <div class="upload_block">
              <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                <div class="type-file-preview"><img id="preview_img" src="<?php if (!empty($info['img'])) echo BASE_SITE_URL.'/'.$info['img'];?>" onload="javascript:DrawImage(this,500,500);"></div>
              </span>
              <div class="f_note">
                  <input type="hidden"  name="img" id="img" value="<?php if( !empty($info['img']) ) echo $info['img']; else echo ''; ?>">
                  <em><i class="icoPro16"></i></em>
                  <div class="file_but">
                      <input type="hidden" name="orig_img" value="<?php if( !empty($info['img']) ) echo $info['img']?>"><input id="img_upload" name="img_upload" value="上传图片" type="file" >
                  </div>
              </div>
            </div>
          </td>
          <td class="vatop tips">系统支持的图片格式为 gif,jpg,jpeg,png</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="retail_license">限制每会员兑换数量:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <label><input type="checkbox" name="is_limit_per_num" value="1"<?php if(!empty($info) && $info['is_limit_per_num']==1) echo ' checked';?>>是</label>
            &nbsp;最多数量 <input type="text" name="limit_per_num" value="<?php if(!empty($info['limit_per_num'])) echo $info['limit_per_num'];?>" class="w36"> 个
          </td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tbody id="title_status">
        <tr>
          <td colspan="2" class="required"><label for="status">状态: </label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <input type="radio" name="status"  id="status" value="1" <?php if ( empty($info['status']) || (isset($info['status']) && $info['status'] == 1)) echo 'checked';?>>正常　
            <input type="radio" name="status"  id="status" value="2" <?php if (isset($info['status']) && $info['status'] == 2) echo 'checked';?>>禁用</td>
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
        	name : {
                required : true
            },
            org_price:{
              required : true
            },
            integral : {
              required : true
            },
            no : {
              required : true
            },
            stock_num :{
              required : true
            },
            img:{
              required : true
            }
        },
        messages : {
            name : {
              required : '名称不能为空'
            },
            org_price :{
              required : '原价不能为空'
            },
            integral :{
              required : '积分不能为空'
            },
            no:{
              required : '礼品编号不能为空'
            },
            stock_num:{
              required : '库存不能为空'
            },
            img:{
              required : '请上传图片'
            }

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
});
</script>
</body>
</html>
