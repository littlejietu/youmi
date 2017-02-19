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
      <h3><?php echo $arrParam['item_type']==1?'文本':'图文';?>回复管理</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span><?php echo $arrParam['item_type']==1?'文本':'图文';?>回复列表</span></a></li>
      <li><a href="<?php echo SELLER_SITE_URL.'/reply/'.($arrParam['item_type']==1?'txt_add':'imgtxt_add');?>"><span>添加<?php echo $arrParam['item_type']==1?'文本':'图文';?>回复</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form method="post" id='form_admin' action="<?php echo SELLER_SITE_URL.'/reply/del'?>">
    <input type="hidden" name="item_type" value="<?php echo $arrParam['item_type'];?>">
    <table class="table tb-type2">
      <thead>
        
        <tr class="thead">
          <th class="w24"></th>
          <th>规则名称</th>
          <th>关键字</th>
          <th class="align-center">排序</th>
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
          <td><?php echo $v['name'];?></td>
          <td>
          <?php $arrKey = json_decode(htmlspecialchars_decode($v['keywords']),true);
          
          foreach ($arrKey as $kk => $vv):
            $type = '等于';
            if($vv['type']==1)
              $type = '等于';
            elseif($vv['type']==2)
              $type = '包含';
            elseif($vv['type']==3)
              $type = '正则';
            echo '<span title="'.$type.'" class="label-default label">'.$vv['content'].'</span>&nbsp;';
          endforeach;?>
          </td>
          <td class="align-center"><?php echo $v['sort'];?></td>
          <td class="w150 align-center">
            <a href="<?php echo SELLER_SITE_URL.'/reply/'.($v['item_type']==1?'txt_add':'imgtxt_add').'?id='.$v['id']; ?>">编辑</a> | 
            <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo SELLER_SITE_URL.'/reply/del?id='.$v['id']; ?>'}">删除</a>  
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
