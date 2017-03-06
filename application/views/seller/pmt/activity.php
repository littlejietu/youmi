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
      <h3>活动管理</h3>
      <ul class="tab-base">
        <li><a href="<?php echo SELLER_SITE_URL.'/activity';?>" class="<?php echo empty($arrParam['type'])?'current':'';?>"><span>活动列表</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=1';?>" class="<?php if(!empty($arrParam['type']) && $arrParam['type']==1 ) echo 'current';?>"><span>满立减</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=2';?>" class="<?php if(!empty($arrParam['type']) && $arrParam['type']==2 ) echo 'current';?>"><span>满立折</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=3';?>" class="<?php if(!empty($arrParam['type']) && $arrParam['type']==3 ) echo 'current';?>"><span>限时折扣</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity/add';?>"><span>添加活动</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='formSearch' action="">
    <table class="tb-type1 noborder search">
      <tbody>
      <tr>
        <td><label>活动名称</label><input class="txt" type="text" name="act_name" value="<?php if (isset($arrParam['act_name'])){echo $arrParam['act_name'];}?>" >&nbsp;&nbsp;
        活动类型
            <select name="type">
              <option value="">全部</option>
              <option value="1"<?php if (isset($arrParam['type']) && $arrParam['type']==1){echo " selected";}?>>满立减</option>
              <option value="2"<?php if (isset($arrParam['type']) && $arrParam['type']==2){echo " selected";}?>>满立折</option>
              <option value="3"<?php if (isset($arrParam['type']) && $arrParam['type']==3){echo " selected";}?>>限时折扣</option>
            </select>
        参与站点<select name="site_id">
                <option value="">请选择</option>
                <?php foreach($site_list as $v):?>
                <option <?php echo (isset($arrParam['site_id']) && $arrParam['site_id']==$v['id'])?'selected':''?>  value="<?php echo $v['id']?>"><?php echo $v['site_name']?></option>
                <?php endforeach;?>
            </select>
        参与对象 <select name="level_id" class="w100">
            <option value="">请选择</option>
            <?php foreach($level_list as $v):?>
                <option <?php echo (isset($arrParam['level_id']) && $arrParam['level_id']==$v['level_id'])?'selected':''?>  value="<?php echo $v['level_id']?>"><?php echo $v['level_name']?></option>
                <?php endforeach;?>
            </select>

            <select name="search_time" >
              <option  value="start_time"<?php if(!empty($arrParam['search_time']) && $arrParam['search_time']=='start_time') echo ' selected';?>>开始时间</option>
              <option  value="end_time"<?php if(!empty($arrParam['search_time']) && $arrParam['search_time']=='end_time') echo ' selected';?>>结束时间</option>
            </select>
            <input class="txt date" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label for="time1">~</label>
              <input class="txt date" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/>

            <a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="<?php echo lang('nc_query');?>">&nbsp;</a>
        </td>  
      </tr>
      </tbody>
    </table>
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg">活动列表</th>
        </tr>
        <tr class="thead">
          <th></th>
          <th>活动</th>
          <th class="align-center">类型</th>
          <th class="align-center">参与站点</th>
          <th class="align-center">参与对象</th>
          <th class="align-center">开始时间</th>
          <th class="align-center">结束时间</th>
          <th class="align-center">活动时段</th>
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
          <td><?php echo $v['title'];?></td>
          <td class="align-center"><?php echo $v['activity_name'];?></td>
          <td class="align-center"><?php echo $v['site_names'];?></td>
          <td class="align-center"><?php echo $v['user_level_name'];?></td>
          <td class="align-center"><?php echo $v['start_time'] ? date('Y-m-d H:i',$v['start_time']) : ''; ?></td>
          <td class="align-center"><?php echo $v['end_time'] ? date('Y-m-d H:i',$v['end_time']) : ''; ?></td>
          <td class="align-center"><?php echo $v['period_time']?></td>
          <td class="align-center"><?php 
            if($v['status'] == 1)
              echo '正常';
            else
              echo '禁用';?>
           </td>
          <td class="w150 align-center">
            <a href="<?php echo SELLER_SITE_URL.'/activity/add?id='.$v['id']; ?>">编辑</a> | 
            <a href="javascript:void(0)" onclick="if(confirm('您确定要删除吗?')){location.href='<?php echo SELLER_SITE_URL.'/activity/del?id='.$v['id']; ?>'}">删除</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="no_data">
          <td colspan="10"><?php echo lang('nc_no_record');?></td>
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
