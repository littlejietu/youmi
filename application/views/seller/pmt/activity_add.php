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
      <h3>满就送活动</h3>
      <ul class="tab-base">
      <li><a href="<?php echo SELLER_SITE_URL.'/activity';?>"><span>活动列表</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=1';?>"><span>满立减</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=2';?>"><span>满立折</span></a></li>
        <li><a href="<?php echo SELLER_SITE_URL.'/activity?type=3';?>"><span>限时折扣</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span>添加活动</span></a></li>
     </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo SELLER_SITE_URL.'/activity/save'?>">
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
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="words">活动广告词:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" name="words" class="txt" value="<?php echo !empty($info)?$info['words']:'';?>" ></td>
          <td class="vatop tips">一句话广告</td>
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
            <div class="<?php if(!empty($info) && $info['is_limit_site']!=1) echo 'fn-hide';?>" id="fixed-site">
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
          <td colspan="2" class="required"><label for="linkman">活动人群:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <select id="user_level" name="user_level">
              <option value="">所有客户</option>
              <option value="">指定客户</option>
            </select></td>
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
        <tr>
          <td colspan="2" class="required"><label for="is_period">活动时段:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop">
            <select name="is_period" id="is_period">
              <option value="2"<?php if(!empty($info) && $info['is_period']==2) echo ' selected'?>>任意时段</option>
              <option value="1"<?php if(!empty($info) && $info['is_period']==1) echo ' selected'?>>指定时段</option>
            </select>
            <div class="<?php if(empty($info) || (!empty($info)&&$info['is_period']==2)) echo 'fn-hide';?>" id="fixed-time">
              <input type="hidden" name="weekdays" id="weekdays" value="<?php echo !empty($info)?$info['weekdays']:'';?>">
              <div class="weekdays-select com-plane-select fn-clear fn-mt15 fn-mb15 rowform" style="width:500px">
                <ul class="fn-clear fn-fl">
                    <li data-value="7" class="item<?php if(!empty($info) && strpos($info['weekdays'], '7')!== false ) echo ' item-selected';?>">周日</li>
                    <li data-value="1" class="item<?php if(!empty($info) && strpos($info['weekdays'], '1')!== false ) echo ' item-selected';?>">周一</li>
                    <li data-value="2" class="item<?php if(!empty($info) && strpos($info['weekdays'], '2')!== false ) echo ' item-selected';?>">周二</li>
                    <li data-value="3" class="item<?php if(!empty($info) && strpos($info['weekdays'], '3')!== false ) echo ' item-selected';?>">周三</li>
                    <li data-value="4" class="item<?php if(!empty($info) && strpos($info['weekdays'], '4')!== false ) echo ' item-selected';?>">周四</li>
                    <li data-value="5" class="item<?php if(!empty($info) && strpos($info['weekdays'], '5')!== false ) echo ' item-selected';?>">周五</li>
                    <li data-value="6" class="item<?php if(!empty($info) && strpos($info['weekdays'], '6')!== false ) echo ' item-selected';?>">周六</li>
                </ul>
              </div>
              <input type="text" value="<?php if (!empty($info['time1'])) echo zerofill(intval($info['time1']/60)).':'.zerofill($info['time1']%60);?>" name="time1" id="time1" class="txt"> - <input type="text" value="<?php if (!empty($info['time2'])) echo zerofill(intval($info['time2']/60)).':'.zerofill($info['time2']%60);?>" name="time2" id="time2" class="txt">
            </div>
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
          <td colspan="2" class="required"><label for="phone">每个用户参与次数限制:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <label><input type="checkbox" name="is_limit_per_total_num" value="1"<?php if(!empty($info) && $info['is_limit_per_total_num']==1) echo ' checked';?>>限定总参与次数</label>
            &nbsp;&nbsp;&nbsp;<input type="text" name="limit_per_total_num" value="<?php if(!empty($info['limit_per_total_num'])) echo $info['limit_per_total_num'];?>" class="w24"> 次
            <br />
            <label><input type="checkbox" name="is_limit_per_day_num" value="1"<?php if(!empty($info) && $info['is_limit_per_day_num']==1) echo ' checked';?>>限定每日参与次数</label>
            <input type="text" name="limit_per_day_num" value="<?php if(!empty($info['limit_per_day_num'])) echo $info['limit_per_day_num'];?>" class="w24"> 次
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="phone">优惠类型:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
              <input type="hidden" name="discount_type" id="discount_type" value="<?php echo !empty($info)?$info['type']:1;?>">
              <div class="discount_type-select com-plane-select fn-clear rowform">
                <ul class="fn-clear fn-fl">
                  <?php if(empty($info)):?>
                    <li data-value="1" class="item item-selected">满立减</li>
                    <li data-value="2" class="item">满立折</li>
                    <li data-value="3" class="item">限时折扣</li>
                  <?php else:?>
                    <?php if($info['type']==1):?>
                      <li data-value="1" class="item item-selected">满立减</li>
                    <?php elseif($info['type']==2):?>
                      <li data-value="2" class="item item-selected">满立折</li>
                    <?php elseif($info['type']==3):?>
                      <li data-value="3" class="item item-selected">限时折扣</li>
                    <?php endif;?>
                  <?php endif;?>
                </ul>
              </div>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="phone">优惠规则:</label> <input type="button" id="btnStepAdd" value="+" name="btnStepAdd"></td>
        </tr>
        <tr class="trStepList noborder<?php if(!empty($info) && $info['type']!=1) echo ' fn-hide';?>" id="trStepList1">
          <td class="vatop rowform" id="tdStepList1">
            <?php if( !empty($info) && $info['type']==1 && !empty($discount_list)):
              foreach($discount_list as $k => $v):?>
              <div class="step_<?php echo $v['id']?>">
                <input type="hidden" name="step[<?php echo $v['id']?>][type]" value="1">消费满 
                <input name="step[<?php echo $v['id']?>][order_amount]" type="text" value="<?php echo $v['order_amount']?>" class="w48"> 元, 
                立减 <input name="step[<?php echo $v['id']?>][discount_amount]" type="text" value="<?php echo $v['discount_amount']?>" class="w36"> 元 
                &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="<?php echo $v['id']?>" act_id="<?php echo $v['act_id'];?>">
              </div>
            <?php endforeach;
              endif; ?>
          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="trStepList noborder<?php if(empty($info) || (!empty($info) && $info['type']!=2)) echo ' fn-hide';?>" id="trStepList2">
          <td class="vatop rowform" id="tdStepList2">
              最高优惠 <input type="text" id="discount_top_amount" name="discount_top_amount" class="w100" value="<?php echo !empty($info)?$info['discount_top_amount']:'';?>" > 元<br /><br />
              <?php if( !empty($info) && $info['type']==2 && !empty($discount_list)):
              foreach($discount_list as $k => $v):?>
              <div class="step_<?php echo $v['id']?>">
                <input type="hidden" name="step[<?php echo $v['id']?>][type]" value="2">消费满 
                <input name="step[<?php echo $v['id']?>][order_amount]" type="text" value="<?php echo $v['order_amount']?>" class="w48"> 元, 
                打折 <input name="step[<?php echo $v['id']?>][discount_percent]" type="text" value="<?php echo $v['discount_percent']*10;?>" class="w36"> 折 
                &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="<?php echo $v['id']?>" act_id="<?php echo $v['act_id'];?>">
              </div>
            <?php endforeach;
              endif; ?>

          </td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="trStepList noborder<?php if(empty($info) || (!empty($info) && $info['type']!=3)) echo ' fn-hide';?>" id="trStepList3">
          <td class="vatop rowform" id="tdStepList3">
              <?php if( !empty($info) && $info['type']==3 && !empty($discount_list)):
              foreach($discount_list as $k => $v):?>
              <div class="step_<?php echo $v['id']?>">
                <input type="hidden" name="step[<?php echo $v['id']?>][type]" value="3">油品 
                <input name="step[<?php echo $v['id']?>][oil_no]" type="text" value="<?php echo $v['oil_no']?>" class="w48"> 号, 
                优惠￥ <input name="step[<?php echo $v['id']?>][price]" type="text" value="<?php echo $v['price'];?>" class="w36"> 元/L
                &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="<?php echo $v['id']?>" act_id="<?php echo $v['act_id'];?>">
              </div>
            <?php endforeach;
              endif; ?>

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

  //step
  var i = 0;
  $("#btnStepAdd").click(function(){
    i = i-1;
    var type = $('#discount_type').val();

    var div = '';
    if(type==1){
      div = '<div class="step_'+i+'"><input type="hidden" name="step['+i+'][type]" value="'+type+'">消费满 <input name="step['+i+'][order_amount]" type="text" value="" class="w48"> 元, 立减 <input name="step['+i+'][discount_amount]" type="text" value="" class="w36"> 元 &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="'+i+'" /></div>';
    }else if(type==2){
      div = '<div class="step_'+i+'"><input type="hidden" name="step['+i+'][type]" value="'+type+'">消费满 <input name="step['+i+'][order_amount]" type="text" value="" class="w48"> 元, 打折 <input name="step['+i+'][discount_percent]" type="text" value="" class="w36"> 折 &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="'+i+'" /></div>';
    }else if(type==3){
      div = '<div class="step_'+i+'"><input type="hidden" name="step['+i+'][type]" value="'+type+'">油品 <input name="step['+i+'][oil_no]" type="text" value="" class="w48"> 号, 优惠￥ <input name="step['+i+'][price]" type="text" value="" class="w36"> 元/L &nbsp;&nbsp;<input type="button" value="-" name="btnStepDel" nctype="'+i+'" /></div>';
    }

    $("#tdStepList"+type).append(div);

    //, <span class="stepList"></span><input type="button"  class="btn" value="+送礼包" name="btnStep" nctype="'+i+'">
  });

  $("input[name=btnStepDel]").live('click',function(){
    var step_id = $(this).attr('nctype');
    if(step_id<0){
      $('.step_'+step_id).hide();
      $('.step_'+step_id+' input').remove();
      return;
    }

    var act_id = $(this).attr('act_id');
    var type = $('#discount_type').val();
    $.ajax({
        url: "<?php echo SELLER_SITE_URL;?>/activity/ajax_step_del",
        data:{
          step_id:step_id,
          act_id:act_id,
          type:type
        },  
        type:'post',
        success: function(data){
          if(data=='true'){
            $('.step_'+step_id).hide();
            $('.step_'+step_id+' input').remove();
          }
        },
        error: function(){
          alert('删除失败');
        }
      });
  });
  //-step
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

    

    $('.weekdays-select ul li').click(function(){
      if($(this).hasClass('item-selected'))
        $(this).removeClass('item-selected');
      else
        $(this).addClass('item-selected');

      var days = '';
      $('.weekdays-select .item-selected').each(function(){
        days += $(this).attr('data-value') +',';
      });
      if(days!='')
        days = days.substring(0, days.length-1);
      $('#weekdays').val(days);

    });
    $('#is_limit_site').change(function(){
      if($('#is_limit_site').val()==1)
        $('#fixed-site').show();
    });
    $('#is_period').change(function(){
      if($('#is_period').val()==1)
        $('#fixed-time').show();
      else{
        $('#fixed-time').hide();
        $('#weekdays').val('');
        $('.weekdays-select .item-selected').each(function(){
          $(this).removeClass('item-selected');
        });
      }
    });

    $('.discount_type-select ul li').click(function(){
      $('.discount_type-select .item-selected').each(function(){
        $(this).removeClass('item-selected');
      });

      $(this).addClass('item-selected');
      $('#discount_type').val($(this).attr('data-value'));

      var type = $('#discount_type').val();
      $('.trStepList').hide();
      $('#trStepList'+type).show();
    });




});
</script>
</body>
</html>
