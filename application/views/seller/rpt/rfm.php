<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>消费频次分析</title>
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
      <h3>消费频次分析</h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span>消费频次分析</span></a></li>
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
              会员手机号：<input class="txt" type="text" value="<?php echo !empty($arrParam['mobile'])?$arrParam['mobile']:'';?>" id="mobile" name="mobile">
              时间：<input class="txt date" type="text" value="<?php if (isset($arrParam['time1'])){echo $arrParam['time1'];}?>" id="time1" name="time1">
              <label>~</label>
              <input class="txt date" type="text" value="<?php if (isset($arrParam['time2'])){echo $arrParam['time2'];}?>" id="time2" name="time2"/>
          </td>
         <td>
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
        <th>月份</th>
        <th>消费次数</th>
        <th>加油数量</th>
        <th>加油金额</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($list) && is_array($list)):?>
      <?php foreach($list as $k => $v):?>
      <tr class="hover">
        <td><?php echo $v['stat_date'];?></td>
        <td><?php echo $v['num'];?></td>
        <td><?php echo $v['oil_num'];?></td>
        <td><?php echo $v['oil_amt'];?></td>
      </tr>
      <?php endforeach;?>
      <?php else:?>
      <tr class="no_data">
        <td colspan="15">没有符合条件的记录</td>
      </tr>
      <?php endif;?>
    </tbody>
    
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
      if(days>366){
        alert('查询时间不能相差超过1年');
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
      if($('#mobile').val()!='')
        search_html = search_html + ' 手机号：'+$('#mobile').val();
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
