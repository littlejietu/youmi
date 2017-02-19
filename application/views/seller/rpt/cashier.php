<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>收银员查询</title>
<?php echo _get_html_cssjs('seller_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('seller').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('seller_css','perfect-scrollbar.min.css','css');?>

<?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('seller',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('seller_js','perfect-scrollbar.min.js','js');?>
<style media="print" type="text/css">
　　.noprint{display:none;}
</style>

</head>
<body>
<div class="page">
 <div class="noprint">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>收银员查询</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>收银员查询</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='formSearch' action="">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <td>加油站：<select name="site_id" id="site_id">
            <option value="">请选择</option>
            <?php foreach($site_list as $k=>$a):?>
              <option  value="<?php echo $a['id']?>"<?php if(!empty($arrParam['site_id']) && $arrParam['site_id']==$a['id']) echo ' selected';?>><?php echo $a['site_name'];?></option>
            <?php endforeach;?>
              </select></td>
         <td>收银员：<select name="cashier_id" id="cashier_id">
            <option value="">请选择</option>
            <option value="null"<?php if(!empty($arrParam['cashier_id']) && $arrParam['cashier_id']=='null') echo ' selected';?>>用户自己付款</option>
            <?php foreach($cashier_list as $k=>$a):?>
              <option  value="<?php echo $a['id']?>"<?php if(!empty($arrParam['cashier_id']) && $arrParam['cashier_id']==$a['id']) echo ' selected';?>><?php echo $a['name'];?> - <?php echo $a['site_name'];?></option>
            <?php endforeach;?>
              </select>
              油品：<select name="oil_no" id="oil_no">
            <option value="">请选择</option>
            <?php foreach($oil_list as $k=>$a):?>
              <option  value="<?php echo $a['oil_no']?>"<?php if(!empty($arrParam['oil']) && $arrParam['oil']==$a['oil_no']) echo ' selected';?>><?php echo $a['oil_no'];?>#</option>
            <?php endforeach;?>
              </select>
              </td>
          </tr>
          <tr>
         <td>
            交易类型：
            <select name="status" id="status" class="querySelect">
              <option value="">请选择</option>
              <option value="WaitPay" <?php if (isset($arrParam['status']) && $arrParam['status']=='WaitPay') echo " selected";?>>待支付</option>
               <option value="Refunded" <?php if (isset($arrParam['status']) && $arrParam['status']=='Refunded') echo " selected";?>>已退款</option>
              <option value="Finished" <?php if (isset($arrParam['status']) && $arrParam['status']=='Finished') echo " selected";?>>已支付</option>
              <option value="Closed" <?php if (isset($arrParam['status']) && $arrParam['status']=='Closed') echo " selected";?>>已关闭</option>
            </select></td>
          <td>
            时间：<input class="txt date" style="width: 150px" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label>~</label>
              <input class="txt date" style="width: 150px" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/>
          </td>
          
          <td><a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="查询">&nbsp;</a>
          <a class="btns " href="" title="撤销检索"><span>撤销检索</span></a><a class="btns" herf="javascript:void(0)" id="btnPrint"><span>点击打印</span></a>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
 </div>
  <div id="search-label"></div>
  <?php if(!empty($report_info)):?>
    <table class="table tb-type2" id="printArea">
      <tbody>
        <tr class="hover">
          <td>交易笔数：<?php echo $report_info['total_num']?></td>
          <td>应收金额(元)：<?php echo $report_info['total_amt_sum']?></td>
        </tr>
        <tr>
          <td></td>
          <td>实收金额(元)：<?php echo $report_info['pay_amt_sum']?></td>
        </tr>
        <tr>
          <td></td>
          <td>优惠金额(元)：<?php echo $report_info['discount_amt_sum']?></td>
        </tr>
        <tr>
          <td></td>
          <td>优惠券金额(元)：<?php echo $report_info['coupon_amt_sum']?></td>
        </tr>
        <tr class="hover">
          <td>收银员：<?php echo $report_info['cashier']?></td>
          <td>打印时间：<?php echo date('Y-m-d H:i:s',time());?> </td>
        </tr>
      </tbody>
    </table>
  <?php endif;?>
</div>
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php //echo _get_html_cssjs('lib','jquery-ui/i18n/zh-CN.js,jquery-ui/jquery.ui.js','js');?>
<?php echo _get_html_cssjs('lib','jquery-ui/i18n/zh-CN.js,jquery-ui/jquery.ui.js,jquery-ui/timepicker/jquery-ui-timepicker-addon.js,jquery-ui/jquery-ui-timepicker-zh-CN.js,jquery-ui/jquery-ui-timepicker-zh-CN.js','js');?>
<script type="text/javascript">
$(function(){
    // $('#time1').datepicker({dateFormat: 'yy-mm-dd'});
    // $('#time2').datepicker({dateFormat: 'yy-mm-dd'});
    $('#time1').datetimepicker({timeFormat: "HH:mm",dateFormat: "yy-mm-dd"});
    $('#time2').datetimepicker({timeFormat: "HH:mm",dateFormat: "yy-mm-dd"});
    $('#btnSubmit').click(function(){
      $('#formSearch').submit();
    });

    $('#btnPrint').click(function(){
      var search_html = '加油站：'+$('#site_id').find("option:selected").text();
      if($('#cashier_id').val()!=''){
        var text_tmp = $('#cashier_id').find("option:selected").text();
        if(text_tmp.indexOf(' - ')>-1)
          text_tmp = text_tmp.substring(0, text_tmp.indexOf(' - '));
        search_html = search_html + ' 收银员：'+text_tmp;
      }
      if($("#time1").val()!='' && $("#time2").val()!='')
        search_html = search_html + ' 时间：'+$("#time1").val()+' ~ ' +$("#time2").val();
      if($('#oil_no').val()!='')
        search_html = search_html + ' 油品：'+$('#oil_no').find("option:selected").text();
      if($('#status').val()!='')
        search_html = search_html + ' 交易类型：'+$('#status').find("option:selected").text();
      $('#search-label').html(search_html);

      $('.noprint').hide();
      $('#btnPrint').hide();
      window.print();
      $('.noprint').show();
      $('#btnPrint').show();
    });
});
</script>
</body>
</html>
