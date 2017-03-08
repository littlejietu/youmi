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
      <h3>加油站管理</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>加油站列表</span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/site/add?company_id='.(!empty($arrParam['company_id'])?$arrParam['company_id']:0);?>"><span>添加加油站</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='formSearch' action="">
  <table class="tb-type1 noborder search">
      <tbody>
        <tr>
         <th>
            <select name="search_field_name" >
              <option  value="site_name"<?php if(!empty($arrParam['search_field_name']) && $arrParam['search_field_name']=='site_name') echo ' selected';?>>油站名称</option>
              <option  value="linkman"<?php if(!empty($arrParam['search_field_name']) && $arrParam['search_field_name']=='linkman') echo ' selected';?>>联系人</option>
              <option  value="phone"<?php if(!empty($arrParam['search_field_name']) && $arrParam['search_field_name']=='phone') echo ' selected';?>>电话</option>
            </select>
          </th>
         <td><input type="text" value="<?php if (!empty($arrParam['search_field_value'])) echo $arrParam['search_field_value'];?>" name="search_field_value" class="txt"></td>
         
          <td class="w24"></td>
          <th><select name="search_time" >
              <option  value="addtime"<?php if(!empty($arrParam['search_time']) && $arrParam['search_time']=='addtime') echo ' selected';?>>添加时间</option>
              </select></th>
          <td><input class="txt date" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label>~</label>
              <input class="txt date" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/></td>
          <td><a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="查询">&nbsp;</a></td>
        </tr>
      </tbody>
    </table>
    </form>
  <form method="post" id='form_admin' action="<?php echo ADMIN_SITE_URL.'/site/del'?>">
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"><?php echo !empty($company_name)?$company_name:'所有';?></th>
        </tr>
        <tr class="thead">
          <th class="w24"></th>
          <th>油站名称</th>
          <th class="align-center">联系人</th>
          <th class="align-center">电话</th>
          <th class="align-center">QQ</th>
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
          <td><?php echo $v['site_name'];?></td>
          <td class="align-center"><?php echo $v['linkman'];?></td>
          <td class="align-center"><?php echo $v['phone'];?></td>
          <td class="align-center"><?php echo $v['qq'];?></td>
          <td class="align-center"><?php 
            if($v['status'] == 1)
              echo '正常';
            else
              echo '禁用';?>
           </td>
          <td class="w150 align-center">
            <a href="<?php echo ADMIN_SITE_URL.'/site/add?id='.$v['id']; ?>">编辑</a> | 
            <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo ADMIN_SITE_URL.'/site/del?id='.$v['id']; ?>'}">删除</a> | 
            <a href="<?php echo ADMIN_SITE_URL.'/site/add?id='.$v['id']; ?>">查看</a>
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
        <?php if(!empty($list) && is_array($list)){ ?>
        <tr class="tfoot">
          <td><input type="checkbox" class="checkall" id="checkallBottom" name="chkVal"></td>
          <td colspan="16"><label for="checkallBottom">全选</label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('删除')){$('#form_admin').submit();}"><span>批量删除</span></a>
            <div class="pagination"> <?php echo $list['pages'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/i18n/zh-CN.js,jquery-ui/jquery.ui.js','js');?>
<script type="text/javascript">
$(function(){
    $('#time1').datepicker({dateFormat: 'yy-mm-dd'});
    $('#time2').datepicker({dateFormat: 'yy-mm-dd'});
    $('#btnSubmit').click(function(){
      $('#formSearch').submit();
    });
});
</script>
</body>
</html>
