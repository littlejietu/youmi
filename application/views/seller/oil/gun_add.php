<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
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
      <h3>油枪管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/gun?site_id='.$arrParam['site_id'];?>"><span>油枪列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加油枪</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo SELLER_SITE_URL.'/gun/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <input type="hidden" name="site_id" value="<?php echo $arrParam['site_id'];?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2"><b><?php echo $company_site;?></b></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="gun_no">油枪号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="gun_no" name="gun_no" class="txt" value="<?php echo !empty($info)?$info['gun_no']:'';?>" ></td>
          <td class="vatop tips">请输入油枪号</td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="oil_no">油品:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">

            <select name="oil_no">
                <option value="">请选择</option>
                <?php foreach($oilprice_list as $v):?>
                <option <?php echo (!empty($info) && $info['oil_no']==$v['oil_no'])?'selected':''?>  value="<?php echo $v['oil_no']?>"><?php echo $v['oil_no']?></option>
                <?php endforeach;?>
            </select>
          </td>
          <td class="vatop tips">请输入油品名称<a href="<?php echo SELLER_SITE_URL.'/price?site_id='.$arrParam['site_id']; ?>">油品管理</a></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="pump_no">泵码:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="pump_no" name="pump_no" class="txt" value="<?php echo !empty($info)?$info['pump_no']:'';?>"></td>
          <td class="vatop tips">请输入油品价格</td>
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
        	oil_no : {
                required : true,

				maxlength: 20,
                remote   : {                
                url :"/admin/admin/ajax_check_name",
                type:'get',
                data:{
                	oil_no : function(){
                        return $('#site_name').val();
                    },
                  }
                }
            },
            price : {
                required : true,
				minlength: 6,
				maxlength: 20
            },
            price : {
                required : true,
                equalTo  : '#price'
            },
            gid : {
                required : true
            }        
        },
        messages : {
            oil_no : {
                required : '油品名称不能为空'
            },
            price : {
                required : '油品价格不能为空',
            },

        }
	});
});
</script>

</body>
</html>
