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
      <h3>等级设置</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>等级列表</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
    <form id="form1" action="<?php echo SELLER_SITE_URL.'/level/save'?>" method="post">
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td>时间周期：在<input type="text" name="level_day" value="<?php echo empty($info['level_day'])?30:$info['level_day'];?>" style="width: 30px" size="3">天内的会员等级  <a class="btns" herf="javascript:void(0)" id="btnSave"><span>确定</span></a></td>
        </tr>
      </tbody>
    </table>
    </form>
    <table class="table tb-type2">
      <thead>
        
        <tr class="thead">
          <th class="w24"></th>
          <th>名称</th>
          <th>需要积分数</th>
          <!-- <th class="align-center">升级后用语</th> -->
        </tr>
      </thead>
      <tbody>

        <?php foreach($list as $k => $v): ?>
        <tr class="hover">
          <td class="w24">
            
          </td>
          <td><span title="<?php echo lang('nc_editable');?>" required="1" fieldid="<?php echo $v['level_id'];?>" ajax_branch='level_name' fieldname="level_name" nc_type="inline_edit" class="editable "><?php echo $v['level_name'];?></span></td>

          <td><span title="<?php echo lang('nc_editable');?>" ajax_branch='integral_num' datatype="number" fieldid="<?php echo $v['level_id'];?>" fieldname="integral_num" nc_type="inline_edit" class="editable"><?php echo $v['integral_num'];?></span></td>
          <!-- <td class="align-center"><span title="<?php //echo lang('nc_editable');?>" required="1" fieldid="<?php //echo $v['level_id'];?>" ajax_branch='next_msg' fieldname="next_msg" nc_type="inline_edit" class="editable "><?php //echo $v['next_msg'];?></span></td> -->
         
        </tr>
        <?php endforeach; ?>

      </tbody>
      
    </table>

</div>
<script  type="text/javascript" src="<?php echo _get_cfg_path('seller_js');?>jquery.edit.js" charset="utf-8"></script>

<script type="text/javascript">
$(function(){
    $('#btnSave').click(function(){
      $('#form1').submit();
    });
});
</script>
</body>
</html>
