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
      <h3>收银员管理</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/site';?>"><span>返回加油站</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>收银员列表</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/cashier/add?site_id='.(!empty($arrParam['site_id'])?$arrParam['site_id']:'');?>"><span>添加收银员</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" action="" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
         <td><select name="site_id" class="querySelect">
              <option value="">全部</option>
              <?php foreach($site_list as $k=>$v):?>
              <option value="<?php echo $v['id']?>"<?php echo !empty($arrParam['site_id'])&&$arrParam['site_id']==$v['id']?' selected':'';?>><?php echo $v['site_name']?></option>
            <?php endforeach;?>
            </select>
            <select name="search_field_name" >
              <option  value="name"<?php if(!empty($arrParam['search_field_name']) && $arrParam['search_field_name']=='name') echo ' selected';?>>姓名</option>
              <option  value="mobile"<?php if(!empty($arrParam['search_field_name']) && $arrParam['search_field_name']=='mobile') echo ' selected';?>>手机号</option>
            </select>
            <input type="text" value="<?php if (!empty($arrParam['search_field_value'])) echo $arrParam['search_field_value'];?>" name="search_field_value" class="txt">

            <a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="查询">&nbsp;</a>
         </td>
        </tr>
        
      </tbody>
    </table>
  </form>
  <form method="post" id='form_admin' action="<?php echo SELLER_SITE_URL.'/cashier/del'?>">
    <table class="table tb-type2">
      <thead>
        
        <tr class="thead">
          <th class="w24"></th>
          <th>收银员</th>
          <th>手机号</th>
          <th class="align-center">加油站</th>
          <th class="align-center">登录次数</th>
          <th class="align-center">最后登录时间</th>
          <th class="align-center">状态</th>
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
          <td><?php echo $v['mobile'];?></td>
          <td class="align-center"><?php echo $v['site_name'];?></td>
          <td class="align-center"><?php echo $v['login_num'];?></td>
          <td class="align-center"><?php if(!empty($v['login_time'])) echo date('Y-m-d H:i:s',$v['login_time']);?></td>
          <td class="align-center"><?php echo $v['status']==1?'正常':'禁用';?></td>
          <td class="w150 align-center">
            <a href="<?php echo SELLER_SITE_URL.'/cashier/add?id='.$v['id']; ?>">编辑</a> | 
            <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo SELLER_SITE_URL.'/cashier/del?id='.$v['id']; ?>'}">删除</a>
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
<script type="text/javascript">
$(function(){
    $('#btnSubmit').click(function(){
      $('#formSearch').submit();
    });
});
</script>
</body>
</html>
