<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">
<title><?php echo $output['html_title'];?></title>

<?php echo _get_html_cssjs('seller_css','perfect-scrollbar.min.css','css');?>

<?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,admincp.js','js');?>
<link href="<?php echo _get_cfg_path('seller').TPL_ADMIN_NAME;?>css/skin_0.css" rel="stylesheet" type="text/css" id="cssfile2" />
<?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->

<?php echo _get_html_cssjs('seller_js','perfect-scrollbar.min.js,jquery.mousewheel.js','js');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo lang('dashboard_wel_system_info');?></h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <div class="info-panel">
    <dl class="member">
      <dt>
        <div class="ico"><i></i><!-- <sub title="<?php //echo lang('dashboard_wel_total_member');?>"><span><em id="statistics_member">0</em></span></sub> --></div>
        <h3><?php echo lang('nc_member');?></h3>
        <h5>会员管理</h5>
      </dt>
      <dd>
        <ul>
          <li class="w50pre normal"><a href="/admin/user/">会员管理<!-- <sub><em id="statistics_week_add_member"></em></sub> --></a></li>
          <li class="w50pre none"><a href="/admin/message/send/">会员通知<!-- <sub><em id="statistics_cashlist">0</em></sub> --></a></li>
        </ul>
      </dd>
    </dl>
    
    <dl class="goods">
      <dt>
        <div class="ico"><i></i><!-- <sub title="<?php //echo lang('dashboard_wel_total_goods');?>"><span><em id="statistics_goods"></em></span></sub> --></div>
        <h3><?php echo lang('nc_goods');?></h3>
        <h5>标准商品模板管理</h5>
      </dt>
      <dd>
        <ul>
          <li class="w25pre normal" style="width:33.33%;"><a href="/admin/category/">分类管理<!-- <sub title="<?php //echo lang('dashboard_wel_count_goods');?>"><em id="statistics_week_add_product"></em></sub> --></a></li>
          <!-- <li class="w25pre none"><sub><em id="statistics_product_verify">0</em></sub></li> -->
          <li class="w25pre none" style="width:33.33%;"><a href="/admin/goods_tpl/">标准商品管理<!-- <sub><em id="statistics_inform_list">0</em></sub> --></a></li>
          <li class="w25pre none" style="width:33.33%;"><a href="/admin/goods_audit/">商品价格审核<!-- <sub><em id="statistics_brand_apply">0</em></sub> --></a></li>
        </ul>
      </dd>
    </dl>
    <dl class="trade">
      <dt>
        <div class="ico"><i></i><!-- <sub title="<?php //echo lang('dashboard_wel_total_order');?>"><span><em id="statistics_order"></em></span></sub> --></div>
        <h3><?php echo lang('nc_trade');?></h3>
        <h5>交易订单</h5>
      </dt>
      <dd>
        <ul>
          <li class="w20pre none"><a href="/admin/order/index">订单管理<sub><em id="statistics_refund"></em></sub></a></li>
          <li class="w20pre none"><a href="/admin/recharge/">充值管理<sub><em id="statistics_return"></em></sub></a></li>
          <li class="w20pre none"><a href="/admin/cash/">提现管理<sub><em id="statistics_vr_refund"></em></sub></a></li>
          <li class="w20pre none"><a href="/admin/refund/refund_manage">退款管理<sub><em id="statistics_complain_new_list">0</em></sub></a></li>
          <li class="w20pre none"><a href="/admin/comment/">评价管理<sub><em id="statistics_complain_handle_list">0</em></sub></a></li>
        </ul>
      </dd>
    </dl>
    <dl class="operation">
      <dt>
        <div class="ico"><i></i></div>
        <h3><?php echo lang('nc_operation');?></h3>
        <h5>运营</h5>
      </dt>
      <dd>
        <ul>
          <li class="w20pre none"><a href="/admin/operation/">基本设置<!-- <sub><em id="statistics_groupbuy_verify_list">0</em></sub> --></a></li>
          <li class="w20pre none"><a href="/admin/activity/">活动管理<!-- <sub><em id="statistics_points_order">0</em></sub> --></a></li>
          <li class="w20pre none"><a href="/admin/coupon/">优惠券管理<!-- <sub><em id="statistics_check_billno">0</em></sub> --></a></li>
          <li class="w20pre none"><a href="/admin/integral_goods/">兑换礼品<!-- <sub><em id="statistics_pay_billno">0</em></sub> --></a></li>
          <li class="w20pre none"><a href="/admin/first/">推首管理<!-- <sub><em id="statistics_pay_billno">0</em></sub> --></a></li>
          <!-- <li class="w17pre none"><a href="<?php //echo urlAdmin('mall_consult', 'index');?>">平台客服<sub><em id="statistics_mall_consult">0</em></sub></a></li> -->
          <!-- <li class="w17pre none"><a href="<?php //echo urlAdmin('delivery', 'index', array('sign' => 'verify'));?>">服务站<sub><em id="statistics_delivery_point">0</em></sub></a></li> -->
        </ul>
      </dd>
    </dl>
    <div class="clear"></div>
    <div class="system-info"></div>
  </div>
</div>
<script type="text/javascript">
var normal = ['week_add_member','week_add_product'];
var work = ['store_joinin','store_bind_class_applay','store_reopen_applay','store_expired','store_expire','brand_apply','cashlist','groupbuy_verify_list','points_order','complain_new_list','complain_handle_list', 'product_verify','inform_list','refund','return','vr_refund','cms_article_verify','cms_picture_verify','circle_verify','check_billno','pay_billno','mall_consult','delivery_point','offline'];
$(document).ready(function(){
	/*$.getJSON("/admin/dashboard/statistics", function(data){
	  $.each(data, function(k,v){
		  $("#statistics_"+k).html(v);
		  if (v!= 0 && $.inArray(k,work) !== -1){
			$("#statistics_"+k).parent().parent().parent().removeClass('none').addClass('high');
		  }else if (v == 0 && $.inArray(k,normal) !== -1){
			$("#statistics_"+k).parent().parent().parent().removeClass('normal').addClass('none');
		  }
	  });
	});*/
	//自定义滚定条
	$('#system-info').perfectScrollbar();
});
</script>

</body>
</html>