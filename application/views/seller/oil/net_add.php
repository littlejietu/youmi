<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('seller').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('seller_css','perfect-scrollbar.min.css','css');?>

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
      <h3>网点管理</h3>
      <ul class="tab-base">
        <li><a href="<?php echo SELLER_SITE_URL.'/net';?>"><span><?php echo lang('nc_manage');?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo !empty($info)? lang('nc_edit'):lang('nc_new'); ?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="form1" method="post" action="<?php echo SELLER_SITE_URL.'/net/save'?>">
    <input type="hidden" name="id" id="id" value="<?php echo !empty($info)?$info['id']:0; ?>">
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="name">网点名称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo !empty($info)?$info['name']:''; ?>" name="name" id="name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="parent_id">上级网点:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <select name="parent_id" id="parent_id">
              <option value="0"><?php echo lang('nc_please_choose');?>...</option>
              <?php if(!empty($list) && is_array($list)): ?>
                <?php foreach($list as $k => $v): ?>
                  <option <?php if($parent_id == $v['id'] || (!empty($info) && $info['parent_id']==$v['id'])) echo 'selected="selected"';?> value="<?php echo $v['id'];?>"><?php echo $v['space'].$v['name'];?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
          <td class="vatop tips"><?php echo lang('article_class_add_sup_class_notice');?></td>
          
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="sort"><?php echo lang('nc_sort');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo !empty($info)?$info['sort']:1; ?>" name="sort" id="sort" class="txt"></td>
          <td class="vatop tips"><?php echo lang('article_class_add_update_sort');?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo lang('nc_submit');?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#form1").valid()){
     $("#form1").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#form1').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            name : {
                required : true,
                remote   : {                
                url :'<?php echo SELLER_SITE_URL.'/net/ajax?branch=check_net_name'?>',
                type:'get',
                data:{
                    name : function(){
                        return $('#name').val();
                    },
                    id : function() {
                        return $('#id').val();
                    }
                  }
                }
            },
            sort : {
                number   : true
            }
        },
        messages : {
           name : {
                required : '<?php echo lang('article_class_add_name_null');?>',
                remote   : '<?php echo lang('article_class_add_name_exists');?>'
            },
            sort  : {
                number   : '<?php echo lang('article_class_add_sort_int');?>'
            }
        }
    });
});
</script>
</body>
</html>