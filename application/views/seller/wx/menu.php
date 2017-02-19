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
      <h3>自定义菜单管理</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>自定义菜单列表</span></a></li>
      <?php if(empty($default_menu)):?>
        <li><a href="<?php echo SELLER_SITE_URL.'/reply/txt_add';?>"><span>添加自定义菜单</span></a></li>
      <?php endif;?>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form method="post" id='form_admin' action="<?php echo SELLER_SITE_URL.'/reply/del'?>">
    <table class="table tb-type2">
      <thead>
        
        <tr class="thead">
          <th class="w24"></th>
          <th>标题</th>
          <th>类型</th>
          <th class="align-center">创建时间</th>
          <th class="align-center">是否生效</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($list['rows']) && is_array($list['rows'])): ?>
        <?php foreach($list['rows'] as $k => $v): ?>
        <tr class="hover">
          <td class="w24">
            
          </td>
          <td><?php echo $v['title'];?></td>
          <td><?php echo $v['type']==1?'默认菜单':'默认菜单(历史记录)';?></td>
          <td class="align-center"><?php echo date('Y-m-d H:i:s', $v['createtime']);?></td>
          <td class="align-center"><?php echo $v['status']==1?'<div class="label label-success">已在微信端生效</div>':'<div class="label label-danger">未在微信端生效</div>';?></td>
          <td class="w150 align-center">
            <?php if($v['status']==1):?>
            <a href="<?php echo SELLER_SITE_URL.'/menu/add?id='.$v['id']; ?>">编辑</a>
            <?php else:?>
              <a href="<?php echo SELLER_SITE_URL.'/menu/add?id='.$v['id']; ?>">查看</a> | 
              <a href="<?php echo SELLER_SITE_URL.'/menu/push?id='.$v['id']; ?>">推送到微信端</a> | 
              <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo SELLER_SITE_URL.'/menu/del?id='.$v['id']; ?>'}">删除</a>
            <?php endif;?>
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
          <td></td>
          <td colspan="16"></a>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

</body>
</html>
