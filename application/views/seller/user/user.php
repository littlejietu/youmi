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

<div class="page">  <div class="fixed-bar">
    <div class="item-title">
      <h3>会员管理</h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span>管理</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
    <input type="hidden" value="member" name="act">
    <input type="hidden" value="member" name="op">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
        <td><select name="user_level" class="querySelect">
              <option value="0">全部</option>
              <?php foreach($site_list as $k=>$v):?>
              <option value="<?php echo $v['id']?>"<?php echo !empty($arrParam['site_id'])&&$arrParam['site_id']==$v['id']?' selected':'';?>><?php echo $v['site_name']?></option>
            <?php endforeach;?>
            </select>
         </td>
          <td>
          <select name="user_level" >
            <option  value="">全部</option>
            <option  value="level_id"<?php if(!empty($arrParam['user_level']) && $arrParam['user_level']=='level_id') echo ' selected';?>>等级数</option>
            <option  value="2"<?php if(!empty($arrParam['user_level']) && $arrParam['user_level']==2) echo ' selected';?>>等级名</option>
            <option  value="3"<?php if(!empty($arrParam['user_level']) && $arrParam['user_level']==3) echo ' selected';?>>企业</option>
          </select>
          <input type="text" value="<?php if (!empty($cKey)){echo $cKey;}?>" name="search_field_value" class="txt">
          <select name="status" >
            <option  value="">状态</option>
            <option  value="1"<?php if(!empty($arrParam['status']) && $arrParam['status']==1) echo ' selected';?>>正常</option>
            <option  value="2"<?php if(!empty($arrParam['status']) && $arrParam['status']==2) echo ' selected';?>>锁定</option>
          </select>
          
          
          <select name="search_field_name" style="display: none">
              <option  value="">会员</option>
          </select>
          </td>
         
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="查询"></a></td>
        </tr>
      </tbody>
    </table>
  </form>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5>操作提示</h5>
        <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li>通过会员管理，你可以进行查看、编辑会员资料等操作</li>
            <li>你可以根据条件搜索会员，然后选择相应的操作</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method="post" id="form_member" action="<?php echo SELLER_SITE_URL.'/user/del'?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2 nobdb">
      <thead>
        <tr class="thead">
          <th>&nbsp;</th>
          <th>会员</th>
          <th>手机</th>
          <th>性别</th>
          <th>正式会员</th>
          <th>生日</th>
          <th>会员等级</th>
          <th>成为会员时间</th>
          <th>状态</th>
          <th class="align-center">操作</th>
        </tr>
      <tbody>
        <?php if(!empty($user_list['rows']) && is_array($user_list['rows'])){ ?>
        <?php foreach($user_list['rows'] as $k => $v){ ?>
        <tr class="hover member">
          <td><input type="checkbox" name='del_id[]' value="<?php echo $v['user_id']; ?>" class="checkitem"></td>
          <td><p class="name"><a href="<?php echo BASE_SITE_URL.'/m/'.$v['user_id'];?>" target="_blank"><strong><?php echo $v['user_name']; ?></strong>(昵称: <?php echo $v['nickname']; ?><?php if(!empty($v['name'])):?>&nbsp;&nbsp;&nbsp;&nbsp;姓名:<?php echo $v['name']; ?><?php endif;?>)</a></p>
              <p class="smallfont">注册时间:&nbsp;<?php echo date('Y-m-d H:i:s',$v['reg_time']); ?></p>
          </td>
          <td><?php echo $v['mobile']; ?></td>
          <td><?php if($v['sex'] == '1') echo '男'; elseif($v['sex'] == '2') echo '女';elseif($v['sex'] == '0') echo '未知'; ?></td>
          <td><?php if($v['member_status'] == '0') echo '不是'; elseif($v['member_status'] == '1') echo '是';elseif($v['member_status'] == '2') echo '不通过';elseif($v['member_status'] == '3') echo '申请'; ?></td>
          <td><?php echo date('Y-m-d',$v['birthday']); ?></td>
          <td><?php echo $v['user_level']; ?></td>
          <td><?php echo date('Y-m-d H:i:s',$v['member_time']); ?></td>
          <td><?php if($v['status'] == '1') echo '正常'; else echo '锁定'; ?></td>
          <td class="align-center"><a href="<?php echo SELLER_SITE_URL.'/user/edit?id='.$v['user_id']?>">编辑</a> <!-- | <a href="/seller/message/send?user_name=<?php echo $v['user_name']?>">通知</a> --></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="11">没有符合条件的记录</td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot class="tfoot">
        <?php if(!empty($user_list) && is_array($user_list)){ ?>
        <tr>
        <td class="w24"><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16">
          <label for="checkallBottom">全选</label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('您确定要删除吗?')){$('#form_member').submit();}"><span>删除</span></a>
            <div class="pagination"> <?php echo $user_list['pages'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
<script>
$(function(){
    $('#ncsubmit').click(function(){
    	$('input[name="op"]').val('member');$('#formSearch').submit();
    });	
});
</script>
</body>
</html>