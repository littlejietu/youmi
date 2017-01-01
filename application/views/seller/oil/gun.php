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
      <h3>油枪管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/site';?>"><span>返回加油站</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>油枪列表</span></a></li>
      <li><a href="<?php echo SELLER_SITE_URL.'/gun/add?site_id='.$arrParam['site_id'];?>"><span>添加油枪</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form method="post" id='form_admin' action="<?php echo SELLER_SITE_URL.'/gun/del'?>">
    <table class="table tb-type2">
      <thead>
        
        <tr class="thead">
          <th class="w24"></th>
          <th>油枪号</th>
          <th>油品</th>
          <th class="align-center">泵码</th>
          <th class="align-center">加油站</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($list['rows']) && is_array($list['rows'])): ?>
        <?php foreach($list['rows'] as $k => $v): ?>
        <tr class="hover">
          <td class="w24">
            <input type="checkbox" name="del_id[]" value="<?php echo $v['id']; ?>" class="checkitem" onclick="javascript:chkRow(this);">
          </td>
          <td><?php echo $v['gun_no'];?></td>
          <td><?php echo $v['oil_no'];?></td>
          <td class="align-center"><?php echo $v['pump_no'];?></td>
          <td class="align-center"><?php echo $v['site_name'];?></td>
          <td class="w150 align-center">
            <a href="<?php echo SELLER_SITE_URL.'/gun/add?id='.$v['id']; ?>">编辑</a> | 
            <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo SELLER_SITE_URL.'/gun/del?id='.$v['id']; ?>'}">删除</a>  
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="no_data">
          <td colspan="10">没有符合条件的记录</td>
        </tr>
      <?php endif; ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td><input type="checkbox" class="checkall" id="checkallBottom" name="chkVal"></td>
          <td colspan="16"><label for="checkallBottom">全选</label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('删除')){$('#form_admin').submit();}"><span>批量删除</span></a>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

</body>
</html>
