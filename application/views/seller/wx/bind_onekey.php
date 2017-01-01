<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>一键绑定</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>
<?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>

<?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('admin_js','perfect-scrollbar.min.js','js');?>

</head>
<body>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>一键绑定</h3>
      <ul class="tab-base">
        
        
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2">
          <?php if($authurl=='1'):?>
            已绑定<br />
            如出现问题,可以<a href="<?php echo SELLER_SITE_URL.'/bind/clean'?>" class="btn"><span>清空,重新绑定</span></a>
          <?php else:?>
            <a href="<?php echo $authurl;?>" class="btn"><span>一键绑定</span></a>
          <?php endif;?>
          </td>
        </tr>
      </tbody>
      <tfoot>
        
      </tfoot>
    </table>

</div>

</body>
</html>