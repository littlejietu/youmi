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
        <div class="ico"><i></i></div>
        <h3><?php echo lang('nc_member');?></h3>
        <h5>油站管理</h5>
      </dt>
      <dd>
        <ul>
          <li class="w50pre normal" style="width:33.33%;"><a href="/seller/site">加油站管理</a></li>
          <li class="w50pre none" style="width:33.33%;"><a href="/seller/user">会员管理</a></li>
          <li class="w50pre none" style="width:33.33%;"><a href="/seller/cashier">收银员管理</a></li>
        </ul>
      </dd>
    </dl>
    
    <!-- <dl class="goods">
      <dt>
        <div class="ico"><i></i></div>
        <h3><?php //echo lang('nc_goods');?></h3>
        <h5>标准商品模板管理</h5>
      </dt>
      <dd>
        <ul>
          <li class="w25pre normal" style="width:33.33%;"><a href="/admin/category/">分类管理</a></li>
          <li class="w25pre none" style="width:33.33%;"><a href="/admin/goods_tpl/">标准商品管理</a></li>
          <li class="w25pre none" style="width:33.33%;"><a href="/admin/goods_audit/">商品价格审核</a></li>
        </ul>
      </dd>
    </dl> -->
    <dl class="trade">
      <dt>
        <div class="ico"><i></i></div>
        <h3><?php echo lang('nc_trade');?></h3>
        <h5>交易</h5>
      </dt>
      <dd>
        <ul>
          <li class="w25pre none"><a href="/seller/order/index">订单管理</a></li>
          <li class="w25pre none"><a href="/seller/report/cashier">收银员查询</a></li>
          <li class="w25pre none"><a href="/seller/report/customer_oil">客单量查询</a></li>
          <li class="w25pre none"><a href="/seller/report/rfm">消费频次查询</a></li>
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
          <li class="w100pre none"><a href="/seller/activity/">活动管理</a></li>
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