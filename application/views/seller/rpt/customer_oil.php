<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>油品客单量查询</title>
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
      <h3>油品客单量查询</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>油品客单量查询</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='formSearch' action="">
    <input type="hidden" name="is_excel" id="is_excel" value="0">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <td>加油站：<select name="site_id" id="site_id">
            <option value="">请选择</option>
            <?php foreach($site_list as $k=>$a):?>
              <option  value="<?php echo $a['id']?>"<?php if(!empty($arrParam['site_id']) && $arrParam['site_id']==$a['id']) echo ' selected';?>><?php echo $a['site_name'];?></option>
            <?php endforeach;?>
              </select>
              时间：<input class="txt date" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label>~</label>
              <input class="txt date" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/>
          </td>
         <td>
              油品：<select name="oil_no" id="oil_no">
            <option value="">请选择</option>
            <?php foreach($oil_list as $k=>$a):?>
              <option  value="<?php echo $a['oil_no']?>"<?php if(!empty($arrParam['oil_no']) && $arrParam['oil_no']==$a['oil_no']) echo ' selected';?>><?php echo $a['oil_no'];?>#</option>
            <?php endforeach;?>
              </select>
              <a href="javascript:void(0);" id="btnSubmit" class="btn-search " title="查询">&nbsp;</a>
          <a class="btns " href="" title="撤销检索"><span>撤销检索</span></a>
          <a class="btns" herf="javascript:void(0)" id="btnPrint"><span>点击打印</span></a>
          <a class="btns " href="javascript:void(0)" title="导出" id="btnSubmit2"><span>导出</span></a>

              </td>
          </tr>
          
      </tbody>
    </table>
  </form>
 </div>
 <div id="search-label"></div>

 <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th>日期</th>
        <th>交易笔数</th>
        <th>实付金额</th>
        <th>微信客单量(元/人)</th>
        <th>支付宝客单量(元/人)</th>
        <th class="align-center">全站客单价(元/笔)</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($list['rows']) && is_array($list['rows'])):
        $oil_payed_order_num=$oil_payed_amt=$oil_payed_person_num=$wxpay_amt=$wxpay_person_num=$alipay_amt=$alipay_person_num = 0;?>
      <?php foreach($list['rows'] as $k => $v):
        $oil_payed_order_num+=$v['oil_payed_order_num'];
        $oil_payed_person_num += $v['oil_payed_person_num'];
        $oil_payed_amt+=$v['oil_payed_amt'];
        $wxpay_amt+=$v['wxpay_amt'];
        $wxpay_person_num+=$v['wxpay_person_num'];
        $alipay_amt+=$v['alipay_amt'];
        $alipay_person_num+=$v['alipay_person_num'];?>
      <tr class="hover">
        <td><?php echo date('Y-m-d', $v['stat_date']);?></td>
        <td><?php echo $v['oil_payed_order_num'];?></td>
        <td><?php echo $v['oil_payed_amt'];?></td>
        <td><?php if($v['wxpay_person_num']>0) echo round($v['wxpay_amt']/$v['wxpay_person_num'],2);?></td>
        <td class="align-center" ><?php if($v['alipay_person_num']>0) echo round($v['alipay_amt']/$v['alipay_person_num'],2);?></td>
        <td class="align-center" ><?php if($v['oil_payed_order_num']>0) echo round($v['oil_payed_amt']/$v['oil_payed_order_num'],2);?></td>
      </tr>
      <?php endforeach;?>
      <tr class="hover">
        <td>合计</td>
        <td><?php echo $oil_payed_order_num;?></td>
        <td><?php echo $oil_payed_amt;?></td>
        <td><?php if($wxpay_person_num>0) echo round($wxpay_amt/$wxpay_person_num,2);?></td>
        <td class="align-center" ><?php if($alipay_person_num>0) echo round($alipay_amt/$alipay_person_num,2);?></td>
        <td class="align-center" ><?php if($oil_payed_order_num>0) echo round($oil_payed_amt/$oil_payed_order_num,2);?></td>
      </tr>
      <?php else:?>
      <tr class="no_data">
        <td colspan="15">没有符合条件的记录</td>
      </tr>
      <?php endif;?>
    </tbody>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15" id="dataFuncs"><div class="pagination"> <?php echo $list['pages'];?> </div></td>
      </tr>
    </tfoot>
  </table>

</div>
<?php echo _get_html_cssjs('lib','jquery-ui/themes/ui-lightness/jquery.ui.css','css');?>
<?php echo _get_html_cssjs('lib','jquery-ui/i18n/zh-CN.js,jquery-ui/jquery.ui.js','js');?>
<script type="text/javascript">
$(function(){
    $('#time1').datepicker({dateFormat: 'yy-mm-dd'});
    $('#time2').datepicker({dateFormat: 'yy-mm-dd'});
    $('#btnSubmit,#btnSubmit2').click(function(){
      var time1=$("#time1").val();
      var time2=$("#time2").val();
      if(time1=='' || time2==''){
        alert('请选择查询日期');
        return;
      }
      var date1=new Date(time1.replace(/-/g,"/"));
      var date2=new Date(time2.replace(/-/g,"/"));
      var days=parseInt((date2.getTime()-date1.getTime())/(1000 * 60 * 60 * 24));
      if(days>31*3){
        alert('查询时间不能相差超过3个月');
        return;
      }
      if(days<0){
        alert('查询时间设置不对，请重新选择');
        return;
      }

      if($('#site_id').val()==''){
        alert('请选择加油站');
        return;
      }

      if($(this).attr('id')=='btnSubmit2')
        $('#is_excel').val(1);
      else
        $('#is_excel').val(0);
      $('#formSearch').submit();
    });

    $('#btnPrint').click(function(){
      var search_html = '加油站：'+$('#site_id').find("option:selected").text();
      if($("#time1").val()!='' && $("#time2").val()!='')
        search_html = search_html + ' 时间：'+$("#time1").val()+' ~ ' +$("#time2").val();
      if($('#oil_no').val()!='')
        search_html = search_html + ' 油品：'+$('#oil_no').find("option:selected").text();
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
