	<div id="sidebar" class="sidebar">
      <div class="column-title" id="main-nav"><span class="ico-index"></span>
        <h2>商品</h2>
      </div>
        <div class="column-menu">
            <ul id="seller_center_left_menu">
                <!-- <li class=""> <a href="<?php //echo SELLER_SITE_URL;?>/goods_add"> 商品发布 </a> </li> -->
                <li class="<?php if(empty($arrParam['status'])) echo 'current';?>"> <a href="<?php echo SELLER_SITE_URL;?>/goods"> 出售中的商品 </a> </li>
                <!-- <li class="<?php //if(!empty($arrParam['status']) && $arrParam['status']==2) echo 'current';?>"> <a href="<?php //echo SELLER_SITE_URL;?>/goods?status=2"> 仓库中的商品 </a> </li> -->
            </ul>
        </div>
    </div>