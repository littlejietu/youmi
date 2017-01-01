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
      <h3>礼品管理</h3>
      <ul class="tab-base">
        <li><a href="<?php echo SELLER_SITE_URL.'/gift';?>"><span>礼品列表</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/gift/add';?>"><span>添加礼品</span></a></li>
        <li><a href="javascript:void(0);" class="current"><span>兑换列表</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='formSearch' action="">
  <table class="tb-type1 noborder search">
      <tbody>
        <tr>
         <th>
            名称
          </th>
         <td><input type="text" value="<?php if (!empty($arrParam['search_field_value'])) echo $arrParam['search_field_value'];?>" name="search_field_value" class="txt"></td>
         
          <td class="w24"></td>
          <th>竞换时间</th>
          <td><input class="txt date" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label>~</label>
              <input class="txt date" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/></td>
          <th>兑换方式</th>
          <td><select name="change_type" >
              <option value="">请选择</option>
              <option  value="1"<?php if(!empty($arrParam['change_type']) && $arrParam['change_type']==1) echo ' selected';?>>实物券</option>
              <option  value="2"<?php if(!empty($arrParam['change_type']) && $arrParam['change_type']==2) echo ' selected';?>>积分兑换</option>
              </select></td>
          <th>参与站点</th>
          <td><select name="site_id">
                <option value="">请选择</option>
                <?php foreach($site_list as $v):?>
                <option <?php echo (isset($arrParam['site_id']) && $arrParam['site_id']==$v['id'])?'selected':''?>  value="<?php echo $v['id']?>"><?php echo $v['site_name']?></option>
                <?php endforeach;?>
            </select></td>
          <td><a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="查询">&nbsp;</a></td>
        </tr>
      </tbody>
    </table>
    </form>
  <form method="post" id='form_admin' action="<?php echo SELLER_SITE_URL.'/gift/del'?>">
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th class="w24"></th>
          <th class="w24">兑换号</th>
          <th class="w24">用户名</th>
          <th>礼品名称</th>
          <th class="w128 align-center">礼品</th>
          <th class="align-center">积分</th>
          <th class="align-center">兑换时间</th>
          <th class="w60">兑换方式</th>
          <th class="w24">兑换站点</th>
          <th class="align-center">状态</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($list['rows']) && is_array($list['rows'])): ?>
        <?php foreach($list['rows'] as $k => $v): ?>
        <tr class="hover">
          <td class="w24">
            <input type="checkbox" name="del_id[]" value="<?php echo $v['id']; ?>" class="checkitem" onclick="javascript:chkRow(this);">
          </td>
          <td><?php echo $v['change_id'];?></td>
          <td><?php echo $v['user_name'];?></td>
          <td><?php echo $v['name'];?></td>
          <td class="align-center"><img src="<?php echo BASE_SITE_URL.'/'.$v['img'];?>" width="100"></td>
          <td class="align-center"><?php echo $v['integral'];?></td>
          <td class="align-center"><?php echo $v['change_time'] ? date('Y-m-d H:i:s',$v['change_time']) : ''; ?></td>
          <td class="align-center"><?php if($v['change_type']==1) echo '实物券兑换';elseif($v['change_type']==2) echo '积分兑换';?></td>
          <td class="align-center"><?php echo $v['site_name'];?></td>
          <td class="align-center"><?php 
            if($v['status'] == 0)
              echo '未兑换';
            elseif($v['status'] == 1)
              echo '已兑换';
            else
              echo '已过期';?>
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
          <td></td>
          <td colspan="16">
            <div class="pagination"> <?php echo $list['pages'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/jquery.ui.js','js');?>
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
