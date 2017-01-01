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
      <h3>注册送礼</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/activity_reg';?>"><span>活动列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加活动</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo SELLER_SITE_URL.'/activity_reg/save'?>">
    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:0;?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="title">名称:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" name="title" class="txt" value="<?php echo !empty($info)?$info['title']:'';?>" ></td>
          <td class="vatop tips">请输入名称</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="intro">说明:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><textarea name="intro" rows="3" cols="50"><?php echo !empty($info)?$info['intro']:'';?></textarea> </td>
          <td class="vatop tips">请输入说明</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="intro">活动加油站:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform w600"><select name="is_limit_site" id="is_limit_site">
              <option value="2"<?php if(!empty($info) && $info['is_limit_site']==2) echo ' selected';?>>不限</option>
              <option value="1"<?php if(!empty($info) && $info['is_limit_site']==1) echo ' selected';?>>限定加油站</option>
            </select>
            <div class="<?php if(empty($info) || $info['is_limit_site']!=1) echo 'fn-hide';?>" id="fixed-site">
              <input type="hidden" name="site_ids" id="site_ids" value="<?php echo !empty($info)?$info['site_ids']:'';?>">
              <div class="site-select com-plane-select fn-clear fn-mt15 fn-mb15 rowform">
                <ul class="fn-clear fn-fl">
                  <?php foreach($site_list as $v):?>
                    <li data-value="<?php echo $v['id']?>" class="item<?php if(!empty($info) && strpos(','.$info['site_ids'].',', ','.$v['id'].',')!== false ) echo ' item-selected';?>"><?php echo $v['site_name'];?></li>
                  <?php endforeach;?>
                </ul>
              </div>
            </div>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="phone">送礼物/积分:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <label><input type="checkbox" name="is_gift_integral" value="1"<?php if(!empty($info) && $info['is_gift_integral']==1) echo ' checked';?>>送积分</label>
            &nbsp;<input type="text" name="gift_integral" value="<?php if(!empty($info['gift_integral'])) echo $info['gift_integral'];?>" class="w36"> 分
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <label><input type="checkbox" name="is_gift" value="1"<?php if(!empty($info) && $info['is_gift']==1) echo ' checked';?>>送礼物</label>
            &nbsp;<select name="gift_id">
              <option value="">请选择</option>
                <?php foreach($gift_list as $v):?>
                <option <?php echo (!empty($info['gift_id']) && $info['gift_id']==$v['id'])?'selected':''?>  value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                <?php endforeach;?>
            </select>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="phone">发放总次数限制:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <label><input type="checkbox" name="is_limit_total_num" value="1"<?php if(!empty($info) && $info['is_limit_total_num']==1) echo ' checked';?>>限定总发放次数</label>
            &nbsp;<input type="text" name="limit_total_num" value="<?php if(!empty($info['limit_total_num'])) echo $info['limit_total_num'];?>" class="w36"> 次
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="start_time">开始时间 - 结束时间:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform" style="width:500px"><input type="text" value="<?php if (!empty($info['start_time'])) echo date('Y-m-d H:i',$info['start_time']);?>" name="start_time" id="start_time" class="txt date w200"> - 
            <input type="text" value="<?php if (!empty($info['end_time'])) echo date('Y-m-d H:i',$info['end_time']);?>" name="end_time" id="end_time" class="txt date w200">
          </td>
          <td class="vatop tips"></td>
        </tr>
        
      </tbody>
      <tbody id="title_status">
        <tr>
          <td colspan="2" class="required"><label for="status">状态: </label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <input type="radio" name="status" value="1" <?php if (empty($info['status']) || $info['status'] == 1) echo ' checked';?>>正常　
            <input type="radio" name="status" value="2" <?php if (isset($info['status']) && $info['status'] == 2) echo ' checked';?>>禁用</td>
          <td class="vatop tips"></td>
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
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css,jquery-ui/timepicker/jquery-ui-timepicker-addon.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/jquery.ui.js,jquery-ui/timepicker/jquery-ui-timepicker-addon.js,jquery-ui/jquery-ui-timepicker-zh-CN.js,jquery-ui/jquery-ui-timepicker-zh-CN.js','js');?>
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
        	 title : {
                required : true
            },
            intro : {
                required : true
            }      
        },
        messages : {
            title : {
                required : '名称不能为空'
            },
            intro : {
                required : '说明不能为空'
            }

        }
	});
});
</script>
<script type="text/javascript">
$(function(){
    $('#start_time').datetimepicker({timeFormat: "HH:mm",dateFormat: "yy-mm-dd"});
    $('#end_time').datetimepicker({timeFormat: "HH:mm",dateFormat: "yy-mm-dd"});
    $('#time1').timepicker();
    $('#time2').timepicker();

    $('.site-select ul li').click(function(){
      if($(this).hasClass('item-selected'))
        $(this).removeClass('item-selected');
      else
        $(this).addClass('item-selected');

      var item = '';
      $('.site-select .item-selected').each(function(){
        item += $(this).attr('data-value') +',';
      });
      if(item!='')
        item = item.substring(0, item.length-1);
      $('#site_ids').val(item);

    });
    $('#is_limit_site').change(function(){
      if($('#is_limit_site').val()==1)
        $('#fixed-site').show();
      else{
        $('#fixed-site').hide();
        $('#site_ids').val('');
        $('.site-select .item-selected').each(function(){
          $(this).removeClass('item-selected');
        });
      }
    });

});
</script>
</body>
</html>
